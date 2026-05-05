<?php

namespace App\Exports\Super;

use App\Models\Room;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RoomsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        $query = Room::query()->with(['pic', 'assets']);
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }
        return $query;
    }

    public function title(): string
    {
        return 'Data Ruangan';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Ruangan',
            'Kode Ruangan',
            'Deskripsi',
            'PIC / Penanggung Jawab',
            'Jumlah Aset Terdata',
            'Tanggal Dibuat',
        ];
    }

    public function map($room): array
    {
        return [
            $room->id,
            $room->name,
            $room->slug,
            $room->description ?? '-',
            $room->pic ? $room->pic->name : '-',
            $room->assets->count(),
            $room->created_at ? $room->created_at->format('Y-m-d H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFEC4899']], // Pink 500
            ],
        ];
    }
}
