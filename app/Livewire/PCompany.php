<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PCompany as PCompanyModel;
use Yajra\DataTables\Facades\DataTables;

class PCompany extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    protected $listeners = ['deletePCompany'];

    public function render()
    {
        $this->menu = "P Company";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'P Company';
        return view('livewire.p-company')->extends('layouts.app');
    }

    public function getPCompanysData()
    {
        return DataTables::of(PCompanyModel::select())
            ->addColumn('actions', function ($row) {
                return view('livewire.p-company.actions', ['company' => $row, 'type' => 'action']);
            })->addColumn('status', function ($row) {
                return view('livewire.p-company.actions', ['company' => $row, 'type' => 'status']);
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function updateStatus($id)
    {
        if($id){
            $company = PCompanyModel::findOrFail($id);
            $company->status = !$company->status;
            $company->save();
        }
    }

    public function deletePCompany($companyId)
    {
        $company = PCompanyModel::find($companyId);
        
        if ($company) {
            $company->delete();
            $this->dispatch('PCompanyDeleted');
        }
    }
}
