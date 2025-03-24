<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidate;
use Yajra\DataTables\Facades\DataTables;

class ManageCandidate extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function render()
    {
        $this->menu = "Candidate";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Candidate';

        return view('livewire.manage-candidate')->extends('layouts.app');
    }

    public function getCandidateData()
    {
        $candidateType = Candidate::$candidateType;
        return DataTables::of(Candidate::select()->with('visa', 'bCompany'))
            ->editColumn('candidate_type', function ($row) use ($candidateType) {
                return $candidateType[$row->candidate_type] ?? '';
            })->editColumn('visa', function ($row) {
                return $row->visa->name;
            })->addColumn('margin', function ($row) {
                return $row->b_rate - $row->c_rate;
            })->editColumn('b_vendor', function ($row) {
                return $row->bCompany->company_name;
            })->addColumn('hr_ts', function ($row) {
                return '';
            })->addColumn('hr_inv', function ($row) {
                return '';
            })->addColumn('rem_hrs', function ($row) {
                return '';
            })->addColumn('l_invoiced_date', function ($row) {
                return '';
            })->addColumn('last_time', function ($row) {
                return '';
            })->addColumn('amt_inv', function ($row) {
                return '';
            })->addColumn('mapped_rec_amt', function ($row) {
                return '';
            })->addColumn('hrs_due', function ($row) {
                return '';
            })->addColumn('start_date', function ($row) {
                return '';
            })->addColumn('actions', function ($row) {
                return view('livewire.manage-candidate.actions', ['candidate' => $row, 'type' => 'action']);
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
