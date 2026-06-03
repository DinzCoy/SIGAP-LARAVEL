<?php

namespace App\Services\Gamification;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class GamificationService
{

    public static function calculateForUser(User $user, $userTickets = null): array
    {

        if ($userTickets !== null) {
            $completedTickets = $userTickets->where('status', Ticket::STATUS_SELESAI);
        } else {
            $completedTickets = Ticket::where('technician_id', $user->id)
                ->where('status', Ticket::STATUS_SELESAI)
                ->get();
        }

        $totalCompleted = $completedTickets->count();

        $xp = 0;
        $slaMetCount = 0;
        $networkTicketCount = 0;
        $hardwareTicketCount = 0;

        foreach ($completedTickets as $ticket) {

            $ticketXp = 100;

            $priority = $ticket->priority;
            if ($priority === 'Tinggi') {
                $ticketXp += 50;
            } elseif ($priority === 'Sedang') {
                $ticketXp += 25;
            } else {
                $ticketXp += 10;
            }

            if (!$ticket->is_sla_resolution_breached) {
                $ticketXp += 50;
                $slaMetCount++;
            }

            if ($ticket->category === 'Service') {
                $ticketXp += 10;
            } elseif ($ticket->category === 'Troubleshooting') {
                $ticketXp += 20;
            }

            $xp += $ticketXp;

            $text = Str::lower($ticket->title . ' ' . $ticket->description);

            if (Str::contains($text, ['jaringan', 'internet', 'wifi', 'router', 'kabel lan', 'switch', 'mikrotik', 'koneksi'])) {
                $networkTicketCount++;
            }

            if ($ticket->type === 'Asset' || Str::contains($text, ['komputer', 'pc', 'laptop', 'printer', 'monitor', 'mouse', 'keyboard', 'heatsink', 'hardware'])) {
                $hardwareTicketCount++;
            }
        }

        if ($xp < 300) {
            $level = 1;
            $xpMin = 0;
            $xpMax = 300;
            $levelName = 'Junior Support';
        } elseif ($xp < 700) {
            $level = 2;
            $xpMin = 300;
            $xpMax = 700;
            $levelName = 'Active Responder';
        } elseif ($xp < 1200) {
            $level = 3;
            $xpMin = 700;
            $xpMax = 1200;
            $levelName = 'Expert Support';
        } elseif ($xp < 2000) {
            $level = 4;
            $xpMin = 1200;
            $xpMax = 2000;
            $levelName = 'System Guardian';
        } else {
            $level = 5 + (int) floor(($xp - 2000) / 1000);
            $xpMin = 2000 + ($level - 5) * 1000;
            $xpMax = $xpMin + 1000;
            $levelName = 'Master IT Guardian';
        }

        $xpRange = $xpMax - $xpMin;
        $currentProgressXp = $xp - $xpMin;
        $levelProgressPct = $xpRange > 0 ? round(($currentProgressXp / $xpRange) * 100) : 0;

        $badges = [];

        $isSpeedrunner = $totalCompleted >= 2 && ($slaMetCount / $totalCompleted) >= 0.75;
        $badges[] = [
            'code' => 'speedrunner',
            'name' => 'Speedrunner',
            'icon' => 'zap',
            'color' => '#EAB308',
            'description' => 'Menyelesaikan perbaikan super cepat sebelum batas waktu SLA.',
            'earned' => $isSpeedrunner,
        ];

        $isNetworkGuru = $networkTicketCount >= 2;
        $badges[] = [
            'code' => 'network_guru',
            'name' => 'Network Guru',
            'icon' => 'globe',
            'color' => '#38BDF8',
            'description' => 'Ahli andalan dalam menyelesaikan masalah internet, wifi, dan jaringan.',
            'earned' => $isNetworkGuru,
        ];

        $isHardwareDoctor = $hardwareTicketCount >= 2;
        $badges[] = [
            'code' => 'hardware_doctor',
            'name' => 'Hardware Doctor',
            'icon' => 'monitor',
            'color' => '#F47920',
            'description' => 'Spesialis tangguh dalam perbaikan fisik komputer, laptop, dan printer BMN.',
            'earned' => $isHardwareDoctor,
        ];

        $isRisingStar = $totalCompleted >= 1;
        $badges[] = [
            'code' => 'rising_star',
            'name' => 'Rising Star',
            'icon' => 'star',
            'color' => '#A855F7',
            'description' => 'Teknisi aktif yang tanggap menyelesaikan laporan kerusakan.',
            'earned' => $isRisingStar,
        ];

        $isSystemShield = $totalCompleted >= 5;
        $badges[] = [
            'code' => 'system_shield',
            'name' => 'System Shield',
            'icon' => 'shield',
            'color' => '#22C55E',
            'description' => 'Telah sukses mengawal stabilitas pelayanan IT minimal 5 tiket selesai.',
            'earned' => $isSystemShield,
        ];

        $earnedBadges = array_values(array_filter($badges, fn($b) => $b['earned']));

        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'photo_url' => $user->photo_path ? url('storage/' . $user->photo_path) : null,
            'xp' => $xp,
            'level' => $level,
            'level_name' => $levelName,
            'level_progress_pct' => $levelProgressPct,
            'xp_min' => $xpMin,
            'xp_max' => $xpMax,
            'tickets_completed' => $totalCompleted,
            'tickets_sla_met' => $slaMetCount,
            'completed_count' => $totalCompleted,
            'in_progress_count' => $userTickets !== null
                ? $userTickets->where('status', Ticket::STATUS_IN_PROGRESS)->count()
                : Ticket::where('technician_id', $user->id)->where('status', Ticket::STATUS_IN_PROGRESS)->count(),
            'total_count' => $userTickets !== null
                ? $userTickets->count()
                : Ticket::where('technician_id', $user->id)->count(),
            'earned_badges' => $earnedBadges,
            'all_badges_info' => $badges,
        ];
    }

    public static function getLeaderboard(): array
    {
        return Cache::remember('technicians_leaderboard', 300, function () {
            $technicians = User::withRole(User::ROLE_TEKNISI)->get();

            $allTickets = Ticket::whereIn('technician_id', $technicians->pluck('id'))->get()->groupBy('technician_id');

            $list = [];
            foreach ($technicians as $tech) {
                $userTickets = $allTickets->get($tech->id, collect());
                $list[] = self::calculateForUser($tech, $userTickets);
            }

            usort($list, function ($a, $b) {
                if ($b['xp'] !== $a['xp']) {
                    return $b['xp'] <=> $a['xp'];
                }
                if ($b['tickets_completed'] !== $a['tickets_completed']) {
                    return $b['tickets_completed'] <=> $a['tickets_completed'];
                }
                return strcmp($a['name'], $b['name']);
            });

            $leaderboard = [];
            foreach ($list as $index => $item) {
                $rank = $index + 1;
                $item['rank'] = $rank;
                $item['is_top_three'] = $rank <= 3;
                $item['glow_color'] = match ($rank) {
                    1 => '#F59E0B',
                    2 => '#94A3B8',
                    3 => '#D97706',
                    default => null,
                };
                $leaderboard[] = $item;
            }

            return $leaderboard;
        });
    }
}
