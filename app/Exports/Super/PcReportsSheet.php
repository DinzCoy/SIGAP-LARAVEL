<?php

namespace App\Exports\Super;

use App\Models\PcReport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PcReportsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        return PcReport::query()
            ->with('asset')
            ->filterByDate($this->startDate, $this->endDate);
    }

    public function title(): string
    {
        return 'Monitoring PC';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Hostname',
            'Username',
            'IP Address',
            'MAC Address',
            'Sistem Operasi',
            'OS Build',
            'Kapasitas RAM MB',
            'RAM Tersedia MB',
            'Kapasitas Disk GB',
            'Disk Tersedia GB',
            'Status Disk',
            'Nomor BMN Asset',
            'Status Anomali',
            'Catatan Anomali',
            'Terakhir Aktif',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->hostname,
            $report->username,
            $report->ip_address,
            $report->mac_address,
            $report->os_name,
            $report->os_build,
            round(($report->total_ram_kb ?? 0) / 1024, 0),
            round(($report->ram_free_kb ?? 0) / 1024, 0),
            round(($report->total_disk_b ?? 0) / 1024 / 1024 / 1024, 1),
            round(($report->disk_free_b ?? 0) / 1024 / 1024 / 1024, 1),
            $report->disk_status,
            $report->asset ? $report->asset->bmn_number : '-',
            $report->is_trouble ? 'Ya' : 'Tidak',
            $report->trouble_note ?: '-',
            $report->last_seen ? $report->last_seen->format('Y-m-d H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF3B82F6']], // Blue 500
            ],
        ];
    }
}
