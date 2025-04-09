<?php

namespace App\Livewire\ManagePayment;

use Livewire\Component;
use App\Models\BCompany;
use App\Models\Candidate;

class PaymentForm extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $vendors = [];
    public $candidates = [];
    public $vendorId;
    public $candidateId;

    public function mount($id = null)
    {
        $this->menu = "Payment";
        $this->breadcrumb = [
            ['route' => 'payment', 'title' => 'Payment'],
        ];
        $this->activeMenu = 'Add';
        $this->vendors = BCompany::active()->pluck('company_name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.manage-payment.payment-form')->extends('layouts.app');
    }

    public function updated($propertyName)
    {
        if($propertyName == 'vendorId'){
            $this->candidateId = '';
            $this->candidates = Candidate::where('b_company_id', $this->vendorId)->pluck('c_name', 'id')->toArray();
        }
        $this->dispatch('initPlugins');
    }
}
