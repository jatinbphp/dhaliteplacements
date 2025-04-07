<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidate;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        DB::statement("SET SQL_MODE=''");
        return DataTables::of(
            Candidate::select([
                'candidates.*',
                DB::raw('DATEDIFF(visa_end_date, CURDATE()) as remaining_visa'),
                DB::raw('MIN(time_sheet_details.date_of_day) as start_date'),
                DB::raw('MAX(time_sheet_details.date_of_day) as last_time_entry'),
                DB::raw('DATEDIFF(id_end_date, CURDATE()) as remaining_id')
            ])
            ->leftJoin('time_sheets', 'time_sheets.candidate_id', '=', 'candidates.id')
            ->leftJoin('time_sheet_details', 'time_sheet_details.time_sheet_id', '=', 'time_sheets.id')
            ->groupBy('candidates.id')
            ->with('visa')
        )
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
            return $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : '';
        })->addColumn('last_time_entry', function ($row) {
            return $row->last_time_entry ? Carbon::parse($row->last_time_entry)->format('d-m-Y') : '';
        })->editColumn('c_aggrement', function ($row) {
            return $row->c_aggrement ? 'Yes' : 'No';
        })->addColumn('mec_sent_date', function ($row) {
            return '';
        })->addColumn('laptop_rec', function ($row) {
            return '';
        })->editColumn('visa_start_date', function ($row) {
            return $row->visa_start_date ?? '';
        })->editColumn('visa_end_date', function ($row) {
            return $row->visa_end_date ?? '';
        })->editColumn('id_start_date', function ($row) {
            return $row->id_start_date ?? '';
        })->editColumn('id_end_date', function ($row) {
            return $row->id_end_date ?? '';
        })->editColumn('remaining_visa', function ($row) {
            return $row->remaining_visa ?? '';
        })->editColumn('remaining_id', function ($row) {
            return $row->remaining_id ?? '';
        })->addColumn('actions', function ($row) {
            return view('livewire.manage-visa-candidate.actions', ['candidate' => $row, 'type' => 'action']);
        })->filterColumn('visa', function ($query, $keyword) {
            $query->whereHas('visa', function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%");
            });
        })->orderColumn('visa', function ($query, $order) {
            $query->join('visas', 'visas.id', '=', 'candidates.visa_status_id')
                  ->orderBy('visas.name', $order);
        })->filterColumn('remaining_visa', function ($query, $keyword) {
            $query->whereRaw('DATEDIFF(visa_end_date, CURDATE()) like ?', ["%{$keyword}%"]);
        })->orderColumn('remaining_visa', function ($query, $order) {
            $query->orderByRaw('DATEDIFF(visa_end_date, CURDATE()) ' . $order);
        })->filterColumn('remaining_id', function ($query, $keyword) {
            $query->whereRaw('DATEDIFF(id_end_date, CURDATE()) like ?', ["%{$keyword}%"]);
        })->orderColumn('remaining_id', function ($query, $order) {
            $query->orderByRaw('DATEDIFF(id_end_date, CURDATE()) ' . $order);
        })->filterColumn('start_date', function ($query, $keyword) {
            $query->havingRaw('MIN(time_sheet_details.date_of_day) like ?', ["%{$keyword}%"]);
        })
        ->orderColumn('start_date', function ($query, $order) {
            $query->orderByRaw('MIN(time_sheet_details.date_of_day) ' . $order);
        })
        ->filterColumn('last_time_entry', function ($query, $keyword) {
            $query->havingRaw('MAX(time_sheet_details.date_of_day) like ?', ["%{$keyword}%"]);
        })
        ->orderColumn('last_time_entry', function ($query, $order) {
            $query->orderByRaw('MAX(time_sheet_details.date_of_day) ' . $order);
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
