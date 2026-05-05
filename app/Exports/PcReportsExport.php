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
    //Filter software tertentu.
    protected ?string $softwareFilter;

    //Filter pencarian hostname.
    protected ?string $search;

    public function __construct(?string $softwareFilter, ?string $search)
    {
        $this->softwareFilter = $softwareFilter;
        $this->search         = $search;
    }

    //Membangun query untuk ekspor dengan filter yang dipilih.
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

    //Menentukan judul kolom untuk file Excel.
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

    //Memetakan data dari setiap baris laporan ke kolom Excel.
    public function map($report): array
    {
        $isOffline   = $report->isOffline();
        $totalRamKb  = $report->total_ram_kb ?? 0;
        $ramFreeKb   = $report->ram_free_kb ?? 0;
        $totalRamGb  = round($totalRamKb / 1024 / 1024, 2);
        $usedRamGb   = round(($totalRamKb - $ramFreeKb) / 1024 / 1024, 2);
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

    //Memberikan gaya (styling) pada baris header.
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
