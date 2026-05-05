<?php

namespace App\Exports\Super;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TicketsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        return Ticket::query()
            ->with(['reporter', 'technician', 'room'])
            ->filterByDate($this->startDate, $this->endDate);
    }

    public function title(): string
    {
        return 'Tiket & Maintenance';
    }

    public function headings(): array
    {
        return [
            'ID Tiket',
            'Judul Permasalahan',
            'Status',
            'Prioritas',
            'Tipe Tiket',
            'Kategori',
            'Aset BMN Terkait',
            'Pelapor',
            'Teknisi',
            'Lokasi/Ruangan',
            'Estimasi Biaya',
            'Tanggal Dibuat',
            'Terakhir Diperbarui',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->title,
            $ticket->status,
            $ticket->priority,
            $ticket->type,
            $ticket->category,
            $ticket->asset ? ($ticket->asset->bmn_number . ' - ' . ($ticket->asset->deviceName ? $ticket->asset->deviceName->name : '(?)')) : '-',
            $ticket->reporter ? $ticket->reporter->name : '-',
            $ticket->technician ? $ticket->technician->name : '-',
            $ticket->room ? $ticket->room->name : '-',
            number_format($ticket->estimated_cost, 0, ',', '.'),
            $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '-',
            $ticket->updated_at ? $ticket->updated_at->format('Y-m-d H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF8B5CF6']], // Violet 500
            ],
        ];
    }
}
