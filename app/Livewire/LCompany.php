<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LCompany as LCompanyModel;
use Yajra\DataTables\Facades\DataTables;


class LCompany extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    protected $listeners = ['deleteCompany'];

    public function render()
    {
        $this->menu = "L Company";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'L-Company';
        return view('livewire.l-company')->extends('layouts.app')->section('content');
    }

    public function getLCompanysData()
    {
        return DataTables::of(LCompanyModel::select())
            ->addColumn('actions', function ($row) {
                return view('livewire.l-company.actions', ['company' => $row, 'type' => 'action']);
            })->addColumn('status', function ($row) {
                return view('livewire.l-company.actions', ['company' => $row, 'type' => 'status']);
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function updateStatus($id)
    {
        if($id){
            $company = LCompanyModel::findOrFail($id);
            $company->status = !$company->status;
            $company->save();
        }
    }

    public function deleteCompany($companyId)
    {
        $company = LCompanyModel::find($companyId);
        
        if ($company) {
            $company->delete();
            $this->dispatch('companyDeleted');
        }
    }
}
