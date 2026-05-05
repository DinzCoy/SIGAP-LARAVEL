<?php

namespace App\Exports\Super;

use App\Models\AssetMovementLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetMovementsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = AssetMovementLog::query()->with([
            'asset.deviceName', 
            'oldUser', 
            'newUser', 
            'oldRoom', 
            'newRoom'
        ]);

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }
        return $query->orderBy('created_at', 'desc');
    }

    public function title(): string
    {
        return 'Log Mutasi Aset';
    }

    public function headings(): array
    {
        return [
            'Waktu Kejadian',
            'Sistem NUP Aset', // placeholder if needed, using BMN for now
            'Nomor BMN',
            'Nama Aset',
            'Tipe Aksi',
            'Pengguna Lama',
            'Pengguna Baru',
            'Ruangan Lama',
            'Ruangan Baru',
            'Alasan/Keterangan',
        ];
    }

    public function map($log): array
    {
        return [
            $log->created_at ? $log->created_at->format('Y-m-d H:i') : '-',
            $log->asset_id,
            $log->asset ? $log->asset->bmn_number : '-',
            $log->asset && $log->asset->deviceName ? $log->asset->deviceName->name : '-',
            $log->action_type,
            $log->oldUser ? $log->oldUser->name : 'N/A',
            $log->newUser ? $log->newUser->name : 'N/A',
            $log->oldRoom ? $log->oldRoom->name : 'N/A',
            $log->newRoom ? $log->newRoom->name : 'N/A',
            $log->reason,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF6366F1']], // Indigo 500
            ],
        ];
    }
}
