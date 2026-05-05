<?php

namespace App\Exports\Super;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        return Asset::query()
            ->with(['deviceName', 'room', 'user'])
            ->filterByDate($this->startDate, $this->endDate);
    }

    public function title(): string
    {
        return 'Data Aset Fisik';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nomor BMN',
            'Nama Perangkat',
            'Merk',
            'Tipe',
            'Kategori',
            'Status Kondisi',
            'Ruangan',
            'MAC Address',
            'Pengguna (Alokasi Permanen)',
            'Tanggal Alokasi',
            'Tahun Pengadaan',
            'Tanggal Dibuat',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->id,
            $asset->bmn_number ?? '-',
            $asset->deviceName ? $asset->deviceName->name : '-',
            $asset->brand ?? ($asset->deviceName ? $asset->deviceName->brand : '-'),
            $asset->deviceName ? $asset->deviceName->type : '-',
            $asset->deviceName ? $asset->deviceName->category : '-',
            $asset->status_kondisi,
            $asset->room ? $asset->room->name : '-',
            $asset->mac_address ?? '-',
            $asset->user ? $asset->user->name : '-',
            $asset->allocated_at ? $asset->allocated_at->format('Y-m-d') : '-',
            $asset->deviceName && $asset->deviceName->procurement_date ? $asset->deviceName->procurement_date->format('Y') : '-',
            $asset->created_at ? $asset->created_at->format('Y-m-d H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF10B981']], // Emerald 500
            ],
        ];
    }
}
