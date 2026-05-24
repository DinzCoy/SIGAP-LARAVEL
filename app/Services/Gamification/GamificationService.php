<?php

namespace App\Services\Gamification;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Str;

class GamificationService
{
    /**
     * Menghitung statistik gamifikasi (XP, Level, Badges) untuk seorang User secara dinamis.
     */
    public static function calculateForUser(User $user): array
    {
        // 1. Ambil tiket yang sudah sukses diselesaikan oleh user ini sebagai teknisi
        $completedTickets = Ticket::where('technician_id', $user->id)
            ->where('status', Ticket::STATUS_SELESAI)
            ->get();

        $totalCompleted = $completedTickets->count();

        // 2. Kalkulasi XP
        $xp = 0;
        $slaMetCount = 0;
        $networkTicketCount = 0;
        $hardwareTicketCount = 0;

        foreach ($completedTickets as $ticket) {
            // Base XP: 100 XP per tiket selesai
            $ticketXp = 100;

            // Tambahan XP berdasarkan prioritas
            $priority = $ticket->priority;
            if ($priority === 'Tinggi') {
                $ticketXp += 50;
            } elseif ($priority === 'Sedang') {
                $ticketXp += 25;
            } else {
                $ticketXp += 10;
            }

            // Tambahan XP berdasarkan SLA
            if (!$ticket->is_sla_resolution_breached) {
                $ticketXp += 50;
                $slaMetCount++;
            }

            // Tambahan XP berdasarkan kategori
            if ($ticket->category === 'Service') {
                $ticketXp += 10;
            } elseif ($ticket->category === 'Troubleshooting') {
                $ticketXp += 20;
            }

            $xp += $ticketXp;

            // Hitung statistik untuk Badges spesialisasi
            $text = Str::lower($ticket->title . ' ' . $ticket->description);
            
            // Jaringan
            if (Str::contains($text, ['jaringan', 'internet', 'wifi', 'router', 'kabel lan', 'switch', 'mikrotik', 'koneksi'])) {
                $networkTicketCount++;
            }

            // Hardware
            if ($ticket->type === 'Asset' || Str::contains($text, ['komputer', 'pc', 'laptop', 'printer', 'monitor', 'mouse', 'keyboard', 'heatsink', 'hardware'])) {
                $hardwareTicketCount++;
            }
        }

        // 3. Kalkulasi Level Progresif (RPG Style)
        // Level 1: 0 - 299 XP
        // Level 2: 300 - 699 XP
        // Level 3: 700 - 1199 XP
        // Level 4: 1200 - 1999 XP
        // Level 5: 2000+ XP (dan tambahan 1000 XP per level berikutnya)
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

        // Progres XP saat ini dalam persen (0-100)
        $xpRange = $xpMax - $xpMin;
        $currentProgressXp = $xp - $xpMin;
        $levelProgressPct = $xpRange > 0 ? round(($currentProgressXp / $xpRange) * 100) : 0;

        // 4. Kalkulasi Badges Spesialisasi (Hanya badge positif)
        $badges = [];

        // Badge 1: Speedrunner (Penyelesai Kilat)
        $isSpeedrunner = $totalCompleted >= 2 && ($slaMetCount / $totalCompleted) >= 0.75;
        $badges[] = [
            'code' => 'speedrunner',
            'name' => 'Speedrunner',
            'icon' => 'zap',
            'color' => '#EAB308', // Amber
            'description' => 'Menyelesaikan perbaikan super cepat sebelum batas waktu SLA.',
            'earned' => $isSpeedrunner,
        ];

        // Badge 2: Network Guru (Pakar Jaringan)
        $isNetworkGuru = $networkTicketCount >= 2;
        $badges[] = [
            'code' => 'network_guru',
            'name' => 'Network Guru',
            'icon' => 'globe',
            'color' => '#38BDF8', // Sky Blue
            'description' => 'Ahli andalan dalam menyelesaikan masalah internet, wifi, dan jaringan.',
            'earned' => $isNetworkGuru,
        ];

        // Badge 3: Hardware Doctor (Dokter Hardware)
        $isHardwareDoctor = $hardwareTicketCount >= 2;
        $badges[] = [
            'code' => 'hardware_doctor',
            'name' => 'Hardware Doctor',
            'icon' => 'monitor',
            'color' => '#F47920', // Orange
            'description' => 'Spesialis tangguh dalam perbaikan fisik komputer, laptop, dan printer BMN.',
            'earned' => $isHardwareDoctor,
        ];

        // Badge 4: Rising Star (Bintang Melesat)
        $isRisingStar = $totalCompleted >= 1;
        $badges[] = [
            'code' => 'rising_star',
            'name' => 'Rising Star',
            'icon' => 'star',
            'color' => '#A855F7', // Purple
            'description' => 'Teknisi aktif yang tanggap menyelesaikan laporan kerusakan.',
            'earned' => $isRisingStar,
        ];

        // Badge 5: System Shield (Pelindung Sistem)
        $isSystemShield = $totalCompleted >= 5;
        $badges[] = [
            'code' => 'system_shield',
            'name' => 'System Shield',
            'icon' => 'shield',
            'color' => '#22C55E', // Green
            'description' => 'Telah sukses mengawal stabilitas pelayanan IT minimal 5 tiket selesai.',
            'earned' => $isSystemShield,
        ];

        // Filter untuk badge yang sudah didapatkan oleh teknisi ini
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
            'in_progress_count' => Ticket::where('technician_id', $user->id)->where('status', Ticket::STATUS_IN_PROGRESS)->count(),
            'total_count' => Ticket::where('technician_id', $user->id)->count(),
            'earned_badges' => $earnedBadges,
            'all_badges_info' => $badges, // Untuk modal info list semua lencana
        ];
    }

    /**
     * Mendapatkan seluruh Leaderboard Teknisi secara teratur.
     */
    public static function getLeaderboard(): array
    {
        $technicians = User::withRole(User::ROLE_TEKNISI)->get();

        $list = [];
        foreach ($technicians as $tech) {
            $list[] = self::calculateForUser($tech);
        }

        // Urutkan berdasarkan XP desc, lalu tiket diselesaikan desc, lalu nama asc
        usort($list, function ($a, $b) {
            if ($b['xp'] !== $a['xp']) {
                return $b['xp'] <=> $a['xp'];
            }
            if ($b['tickets_completed'] !== $a['tickets_completed']) {
                return $b['tickets_completed'] <=> $a['tickets_completed'];
            }
            return strcmp($a['name'], $b['name']);
        });

        // Tandai Top 3 dengan bingkai berkilau
        $leaderboard = [];
        foreach ($list as $index => $item) {
            $rank = $index + 1;
            $item['rank'] = $rank;
            $item['is_top_three'] = $rank <= 3;
            $item['glow_color'] = match ($rank) {
                1 => '#F59E0B', // Emas
                2 => '#94A3B8', // Perak
                3 => '#D97706', // Perunggu
                default => null,
            };
            $leaderboard[] = $item;
        }

        return $leaderboard;
    }
}
