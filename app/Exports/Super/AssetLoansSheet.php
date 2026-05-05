<?php

namespace App\Exports\Super;

use App\Models\AssetLoan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetLoansSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        $query = AssetLoan::query()->with(['asset.deviceName', 'lender', 'borrower']);
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
        return 'Peminjaman Aset';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Aset',
            'Nomor BMN (Aset)',
            'Peminjam',
            'Alasan Meminjam?',
            'Pemberi Pinjaman',
            'Status',
            'Tanggal Pinjam',
            'Tanggal Jatuh Tempo',
            'Selesai pada (Dikembalikan)',
            'Tanggal Pengajuan',
        ];
    }

    public function map($loan): array
    {
        $statusLabels = [
            'pending' => 'Menunggu Persetujuan',
            'active' => 'Sedang Dipinjam',
            'returned' => 'Selesai (Dikembalikan)',
            'rejected' => 'Ditolak',
        ];

        return [
            $loan->id,
            $loan->asset && $loan->asset->deviceName ? $loan->asset->deviceName->name : '-',
            $loan->asset ? $loan->asset->bmn_number : '-',
            $loan->borrower ? $loan->borrower->name : '-',
            $loan->loan_reason ?? '-',
            $loan->lender ? $loan->lender->name : '-',
            $statusLabels[$loan->status] ?? $loan->status,
            $loan->loaned_at ? $loan->loaned_at->format('Y-m-d H:i') : '-',
            $loan->due_date ? $loan->due_date->format('Y-m-d H:i') : '-',
            $loan->returned_at ? $loan->returned_at->format('Y-m-d H:i') : ($loan->status === 'returned' && $loan->updated_at ? $loan->updated_at->format('Y-m-d H:i') : '-'),
            $loan->created_at ? $loan->created_at->format('Y-m-d H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF14B8A6']], // Teal 500
            ],
        ];
    }
}
