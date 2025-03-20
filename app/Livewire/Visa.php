<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visa as VisaModel;
use Yajra\DataTables\Facades\DataTables;

class Visa extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function render()
    {
        $this->menu = "Visa";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Visa';
        return view('livewire.visa')->extends('layouts.app');
    }

    public function getVisaData()
    {
        return DataTables::of(VisaModel::select())
            ->addColumn('actions', function ($row) {
                return view('livewire.visa.actions', ['visa' => $row, 'type' => 'action']);
            })->addColumn('status', function ($row) {
                return view('livewire.visa.actions', ['visa' => $row, 'type' => 'status']);
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function updateStatus($id)
    {
        if($id){
            $company = VisaModel::findOrFail($id);
            $company->status = !$company->status;
            $company->save();
        }
    }
}
