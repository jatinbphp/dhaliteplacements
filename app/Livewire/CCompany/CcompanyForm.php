<?php

namespace App\Livewire\CCompany;

use Livewire\Component;
use App\Models\CCompany;

class CcompanyForm extends Component
{
    public $companyId;
    public $name, $address, $status;
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function mount($id = null)
    {
        $this->menu = "C Company";
        $this->breadcrumb = [
            ['route' => 'c-company', 'title' => 'C Company'],
        ];
        $this->activeMenu = 'Add';
        $this->status = 1;
        if($id){
            $this->activeMenu = 'Edit';
            $company = CCompany::findOrFail($id);
            $this->companyId = $company->id;
            $this->name = $company->company_name;
            $this->address = $company->address;
            $this->status = $company->status;
        }
    }

    public function render()
    {
        return view('livewire.c-company.ccompany-form')->extends('layouts.app');
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
            $company = CCompany::findOrFail($this->companyId);
            $company->update($filedData);
            session()->flash('success', 'C Company updated successfully!');
        } else {
            CCompany::create($filedData);
            session()->flash('success', 'C Company created successfully!');
        }

        $this->redirect(route('c-company'), navigate: true);
    }
}
