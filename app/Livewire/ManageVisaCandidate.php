<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidate;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ManageVisaCandidate extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    
    public function render()
    {
        $this->menu = "Visa Candidate";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Candidate';

        return view('livewire.manage-visa-candidate')->extends('layouts.app');
    }

    public function getVisaCandidateData()
    {
        $candidateType = Candidate::candidateType;
        $candidateStatus = Candidate::candidateStatus;
        return DataTables::of(Candidate::select()->with('visa'))
            ->editColumn('created_at', function ($row) {
                return date('m-d-Y', strtotime($row->created_at));
            })
            ->editColumn('candidate_type', function ($row) use ($candidateType) {
                return $candidateType[$row->candidate_type] ?? '';
            })->editColumn('status', function ($row) use ($candidateStatus) {
                return $candidateStatus[$row->status] ?? '';
            })->editColumn('visa', function ($row) {
                return $row->visa->name ?? '';
            })->addColumn('start_date', function ($row) {
                return '';
            })->addColumn('last_time_entry', function ($row) {
                return '';
            })->editColumn('c_aggrement', function ($row) {
                return $row->c_aggrement ? 'Yes' : 'No';
            })->addColumn('mec_sent_date', function ($row) {
                return '';
            })->addColumn('laptop_rec', function ($row) {
                return '';
            })->editColumn('visa_start', function ($row) {
                return $row->visa_start_date ?? '';
            })->editColumn('visa_end', function ($row) {
                return $row->visa_end_date ?? '';
            })->editColumn('id_start', function ($row) {
                return $row->id_start_date ?? '';
            })->editColumn('id_end', function ($row) {
                return $row->id_end_date ?? '';
            })->addColumn('remaining_visa', function ($row) {
                $startDate = $row->visa_start_date;
                $endDate = $row->visa_end_date;
                if(!$startDate || !$endDate){
                    return '';
                }
                $date1 = Carbon::createFromFormat('m-d-Y', Carbon::now()->format('m-d-Y'));
                $date2 = Carbon::createFromFormat('m-d-Y', $row->visa_end_date);
                return $date1->diffInDays($date2);
            })->addColumn('remaining_id', function ($row) {
                $startDate = $row->id_start_date;
                $endDate = $row->id_end_date;
                if(!$startDate || !$endDate){
                    return '';
                }
                $date1 = Carbon::createFromFormat('m-d-Y', Carbon::now()->format('m-d-Y'));
                $date2 = Carbon::createFromFormat('m-d-Y', $row->id_end_date);
                return $date1->diffInDays($date2);
            })->addColumn('actions', function ($row) {
                return view('livewire.manage-visa-candidate.actions', ['candidate' => $row, 'type' => 'action']);
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
