<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardStatsService;
use Illuminate\View\View;

class PimpinanController extends Controller
{
    public function __construct(protected DashboardStatsService $layananDashboard) {}

    public function dashboard(): View
    {
        $stats = $this->layananDashboard->getPimpinanStats();

        return view('pimpinan.dashboard', $stats);
    }
}
