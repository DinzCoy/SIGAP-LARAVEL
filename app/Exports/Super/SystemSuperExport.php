<?php

namespace App\Exports\Super;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SystemSuperExport implements WithMultipleSheets
{
    use Exportable;

    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new AssetsSheet($this->startDate, $this->endDate),
            new PcReportsSheet($this->startDate, $this->endDate),
            new TicketsSheet($this->startDate, $this->endDate),
            new RoomsSheet($this->startDate, $this->endDate),
            new UsersSheet($this->startDate, $this->endDate),
            new AssetLoansSheet($this->startDate, $this->endDate),
            new AssetMovementsSheet($this->startDate, $this->endDate),
        ];
    }
}
