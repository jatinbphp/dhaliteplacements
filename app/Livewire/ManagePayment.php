<?php

namespace App\Livewire;

use Livewire\Component;

class ManagePayment extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function render()
    {
        $this->menu = "Payment";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Payment';

        return view('livewire.manage-payment')->extends('layouts.app');
    }
}
