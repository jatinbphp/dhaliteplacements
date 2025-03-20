<?php

namespace App\Livewire\Visa;

use Livewire\Component;
use App\Models\Visa;

class ManageVisaForm extends Component
{
    public $visaId;
    public $name, $status;
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function mount($id = null)
    {
        $this->menu = "Visa";
        $this->breadcrumb = [
            ['route' => 'visa', 'title' => 'Visa'],
        ];
        $this->activeMenu = 'Add';
        $this->status = 1;
        if($id){
            $this->activeMenu = 'Edit';
            $visa = Visa::findOrFail($id);
            $this->visaId = $visa->id;
            $this->name = $visa->name;
            $this->status = $visa->status;
        }
    }

    public function render()
    {
        return view('livewire.visa.manage-visa-form')->extends('layouts.app');
    }

    public function updateCompany()
    {
        $this->validate([
            'name' => 'required',
        ]);

        $filedData = [
            'name'   => $this->name,
            'status' => $this->status,
        ];

        if($this->visaId){
            $company = Visa::findOrFail($this->visaId);
            $company->update($filedData);
            session()->flash('success', 'Visa updated successfully!');
        } else {
            Visa::create($filedData);
            session()->flash('success', 'Visa created successfully!');
        }

        $this->redirect(route('visa'), navigate: true);
    }
}
