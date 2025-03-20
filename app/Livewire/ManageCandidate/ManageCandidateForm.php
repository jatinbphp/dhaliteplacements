<?php

namespace App\Livewire\ManageCandidate;

use Livewire\Component;
use App\Models\LCompany;

class ManageCandidateForm extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $companyId;
    public $candidateOptions = [];
    public $candidateType = '';
    public $lCompanyData = [];
    public $lCompanyId = '';
    public $selectedLCompanyAddress = '';
    public $visaStatus = [];

    public function mount($id='')
    {
        $this->candidateOptions = [
            'w2'     => 'W2',
            'w2_c2c' => 'W2 & c2c',
            'c2c'    => 'C2C',
        ];

        $this->visaStatus = [
            'citizen' => 'Citizen', 
            'opt'     => 'OPT',
            'h1b'     => 'H1b',
            'cpt'     => 'CPT',
            'gc'      => 'GC',
            'h4'      => 'H4',
            'others'  => 'Others',
        ];

        $this->candidateType = 'w2';
        $this->lCompanyData = LCompany::active()->pluck('company_name', 'id');
    }

    public function render()
    {
        return view('livewire.manage-candidate.manage-candidate-form')->extends('layouts.app');
    }

    public function updateCandidateType()
    {
        
    }

    public function updateLCompany()
    {
        \Log::info($this->lCompanyId);
        $this->selectedLCompanyAddress = $selectedLCompanyAddress = LCompany::where('id', $this->lCompanyId)
            ->active()
            ->value('address');

    }
}
