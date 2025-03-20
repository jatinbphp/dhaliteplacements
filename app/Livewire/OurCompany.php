<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OurCompany as OurCompanyModel;
use Yajra\DataTables\Facades\DataTables;


class OurCompany extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    protected $listeners = ['deleteOurCompany'];

    public function render()
    {
        $this->menu = "Our Company";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Our-Company';
        return view('livewire.our-company')->extends('layouts.app');
    }

    public function getOurCompanysData()
    {
        return DataTables::of(OurCompanyModel::select())
            ->addColumn('actions', function ($row) {
                return view('livewire.our-company.actions', ['company' => $row, 'type' => 'action']);
            })->addColumn('status', function ($row) {
                return view('livewire.our-company.actions', ['company' => $row, 'type' => 'status']);
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function updateStatus($id)
    {
        if($id){
            $company = OurCompanyModel::findOrFail($id);
            $company->status = !$company->status;
            $company->save();
        }
    }

    public function deleteOurCompany($companyId)
    {
        $company = OurCompanyModel::find($companyId);
        
        if ($company) {
            $company->delete();
            $this->dispatch('ourCompanyDeleted');
        }
    }
}
