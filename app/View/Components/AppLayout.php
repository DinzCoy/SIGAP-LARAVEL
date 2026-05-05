<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    //Mendapatkan view / konten yang merepresentasikan komponen.
    public function render(): View
    {
        return view('layouts.app');
    }
}
