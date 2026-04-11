<?php

namespace App\Exports;

use App\Models\PcReport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PcReportsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * The number of minutes after which a PC is considered offline.
     * Must match WebReportController::OFFLINE_THRESHOLD_MINUTES.
     */
    private const OFFLINE_THRESHOLD_MINUTES = 5;

    /** @var string|null */
    protected ?string $softwareFilter;

    /** @var string|null */
    protected ?string $search;

    public function __construct(?string $softwareFilter, ?string $search)
    {
        $this->softwareFilter = $softwareFilter;
        $this->search         = $search;
    }

    /**
     * Build the base query for export, applying optional software and search filters.
     */
    public function query()
    {
        $query = PcReport::query();

        if (!empty($this->softwareFilter)) {
            $query = match ($this->softwareFilter) {
                'bit_defender' => $query->whereHas('installedSoftware', fn ($q) => $q->where('software_name', 'like', '%Bitdefender%')),
                'office_365'   => $query->whereHas('installedSoftware', fn ($q) => $q->where('software_name', 'like', '%Office 365%')->orWhere('software_name', 'like', '%Microsoft 365%')),
                'no_bmn'       => $query->where(fn ($q) => $q->whereDoesntHave('asset')->orWhereHas('asset', fn ($q2) => $q2->whereNull('bmn_number')->orWhere('bmn_number', ''))),
                default        => $query,
            };
        }

        if (!empty($this->search)) {
            $query->where('hostname', 'like', "%{$this->search}%");
        }

        return $query->orderByDesc('last_seen');
    }

    /**
     * Return the column headings for the Excel file.
     */
    public function headings(): array
    {
        return [
            'Hostname',
            'IP Address',
            'MAC Address',
            'Ruangan',
            'Sistem Operasi',
            'Status Agent',
            'Kapasitas RAM',
            'Kapasitas Storage',
            'Terakhir Aktif',
            'Indikasi Anomali',
            'Detail Anomali',
        ];
    }

    /**
     * Map each PC report row to the corresponding Excel columns.
     *
     * @param  PcReport  $report
     * @return array
     */
    public function map($report): array
    {
        $isOffline   = now()->diffInMinutes($report->last_seen) > self::OFFLINE_THRESHOLD_MINUTES;
        $totalRamGb  = round(($report->total_ram_kb ?? 0) / 1024 / 1024, 2);
        $usedRamGb   = round((($report->total_ram_kb - $report->ram_free_kb) ?? 0) / 1024 / 1024, 2);
        $totalDiskGb = round(($report->total_disk_b ?? 0) / 1024 / 1024 / 1024, 2);
        $freeDiskGb  = round(($report->disk_free_b ?? 0) / 1024 / 1024 / 1024, 2);

        return [
            $report->hostname,
            $report->ip_address,
            $report->mac_address,
            $report->room_name ?: '-',
            $report->os_name . ' (Build ' . $report->os_build . ')',
            $isOffline ? 'Offline' : 'Online',
            "{$usedRamGb} GB / {$totalRamGb} GB",
            "{$freeDiskGb} GB / {$totalDiskGb} GB",
            $report->last_seen ? $report->last_seen->format('d M Y, H:i') : '-',
            $report->is_trouble ? 'Ya' : 'Tidak',
            $report->trouble_note ?: '-',
        ];
    }

    /**
     * Apply styling to the header row (bold white text on BPS blue background).
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF005A8C']],
            ],
        ];
    }
}
