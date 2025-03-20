<?php

namespace App\Livewire\OurCompany;

use Livewire\Component;
use App\Models\OurCompany;

class OurcompanyForm extends Component
{
    public $companyId;
    public $name, $address, $status, $phone;
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function mount($id = null)
    {
        $this->menu = "Our Company";
        $this->breadcrumb = [
            ['route' => 'our-company', 'title' => 'Our Company'],
        ];
        $this->activeMenu = 'Add';
        $this->status = 1;
        if($id){
            $this->activeMenu = 'Edit';
            $company = OurCompany::findOrFail($id);
            $this->companyId = $company->id;
            $this->name = $company->company_name;
            $this->address = $company->address;
            $this->phone = $company->phone;
            $this->status = $company->status;
        }
    }

    public function render()
    {
        return view('livewire.our-company.ourcompany-form')->extends('layouts.app');
    }

    public function updateCompany()
    {
        $this->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required|digits_between:10,15',
        ]);

        $filedData = [
            'company_name' => $this->name,
            'address'      => $this->address,
            'phone'        => $this->phone,
            'status'       => $this->status,
        ];

        if($this->companyId){
            $company = OurCompany::findOrFail($this->companyId);
            $company->update($filedData);
            session()->flash('success', 'Our Company updated successfully!');
        } else {
            OurCompany::create($filedData);
            session()->flash('success', 'Our Company created successfully!');
        }

        $this->redirect(route('our-company'), navigate: true);
    }
}
