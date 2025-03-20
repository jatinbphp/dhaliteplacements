<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CCompany as CCompanyModel;
use Yajra\DataTables\Facades\DataTables;

class CCompany extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    protected $listeners = ['deleteCCompany'];

    public function render()
    {
        $this->menu = "C Company";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'C-Company';
        return view('livewire.c-company')->extends('layouts.app')->section('content');
    }

    public function getCCompanysData()
    {
        return DataTables::of(CCompanyModel::select())
            ->addColumn('actions', function ($row) {
                return view('livewire.c-company.actions', ['company' => $row, 'type' => 'action']);
            })->addColumn('status', function ($row) {
                return view('livewire.c-company.actions', ['company' => $row, 'type' => 'status']);
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function updateStatus($id)
    {
        if($id){
            $company = CCompanyModel::findOrFail($id);
            $company->status = !$company->status;
            $company->save();
        }
    }

    public function deleteCCompany($companyId)
    {
        $company = CCompanyModel::find($companyId);
        
        if ($company) {
            $company->delete();
            $this->dispatch('cCompanyDeleted');
        }
    }
}
