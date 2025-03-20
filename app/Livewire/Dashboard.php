<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LCompany;
use App\Models\BCompany;
use App\Models\PCompany;
use App\Models\OurCompany;

class Dashboard extends Component
{
    public $totalCompanyCounts = [];
    
    public function render()
    {
        $this->totalCompanyCounts['l_company'] = LCompany::active()->count();
        $this->totalCompanyCounts['c_company'] = BCompany::active()->count();
        $this->totalCompanyCounts['p_company'] = PCompany::active()->count();
        $this->totalCompanyCounts['our_company'] = OurCompany::active()->count();
        return view('livewire.dashboard')->extends('layouts.app');
    }
}
