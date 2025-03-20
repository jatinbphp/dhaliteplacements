<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BCompany as BCompanyModel;
use Yajra\DataTables\Facades\DataTables;

class BCompany extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    protected $listeners = ['deleteBCompany'];

    public function render()
    {
        $this->menu = "B Company";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'B-Company';
        return view('livewire.b-company')->extends('layouts.app');
    }

    public function getBCompanysData()
    {
        return DataTables::of(BCompanyModel::select())
            ->addColumn('actions', function ($row) {
                return view('livewire.b-company.actions', ['company' => $row, 'type' => 'action']);
            })->addColumn('status', function ($row) {
                return view('livewire.b-company.actions', ['company' => $row, 'type' => 'status']);
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function updateStatus($id)
    {
        if($id){
            $company = BCompanyModel::findOrFail($id);
            $company->status = !$company->status;
            $company->save();
        }
    }

    public function deleteBCompany($companyId)
    {
        $company = BCompanyModel::find($companyId);
        
        if ($company) {
            $company->delete();
            $this->dispatch('bCompanyDeleted');
        }
    }
}
