<?php

namespace App\Livewire;

use Livewire\Component;

class ManageCandidate extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function render()
    {
        $this->menu = "Candidate";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Candidate';

        return view('livewire.manage-candidate')->extends('layouts.app');
    }


}
