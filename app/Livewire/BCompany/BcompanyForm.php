<?php

namespace App\Livewire\BCompany;

use Livewire\Component;
use App\Models\BCompany;

class BcompanyForm extends Component
{
    public $companyId;
    public $name, $address, $status;
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function mount($id = null)
    {
        $this->menu = "B Company";
        $this->breadcrumb = [
            ['route' => 'b-company', 'title' => 'B Company'],
        ];
        $this->activeMenu = 'Add';
        $this->status = 1;
        if($id){
            $this->activeMenu = 'Edit';
            $company = BCompany::findOrFail($id);
            $this->companyId = $company->id;
            $this->name = $company->company_name;
            $this->address = $company->address;
            $this->status = $company->status;
        }
    }

    public function render()
    {
        return view('livewire.b-company.bcompany-form')->extends('layouts.app');
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
            $company = BCompany::findOrFail($this->companyId);
            $company->update($filedData);
            session()->flash('success', 'B Company updated successfully!');
        } else {
            BCompany::create($filedData);
            session()->flash('success', 'B Company created successfully!');
        }

        $this->redirect(route('b-company'), navigate: true);
    }
}
