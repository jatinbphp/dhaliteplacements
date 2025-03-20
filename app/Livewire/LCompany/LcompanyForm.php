<?php

namespace App\Livewire\LCompany;

use Livewire\Component;
use App\Models\LCompany;

class LcompanyForm extends Component
{
    public $companyId;
    public $name, $address, $status;
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function mount($id = null)
    {
        $this->menu = "L Company";
        $this->breadcrumb = [
            ['route' => 'l-company', 'title' => 'L Company'],
        ];
        $this->activeMenu = 'Add';
        $this->status = 1;
        if($id){
            $this->activeMenu = 'Edit';
            $company = LCompany::findOrFail($id);
            $this->companyId = $company->id;
            $this->name = $company->company_name;
            $this->address = $company->address;
            $this->status = $company->status;
        }
    }

    public function render()
    {
        return view('livewire.l-company.lcompany-form')->extends('layouts.app');
    }

    public function updateCompany()
    {
        $this->validate([
            'name' => 'required',
            'address' => 'required',
        ]);

        $filedData = [
            'company_name' => $this->name,
            'address'      => $this->address,
            'status'       => $this->status,
        ];

        if($this->companyId){
            $company = LCompany::findOrFail($this->companyId);
            $company->update($filedData);
            session()->flash('success', 'L Company updated successfully!');
        } else {
            LCompany::create($filedData);
            session()->flash('success', 'L Company created successfully!');
        }

        $this->redirect(route('l-company'), navigate: true);
    }
}
