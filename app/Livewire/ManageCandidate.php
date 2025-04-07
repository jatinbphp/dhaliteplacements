<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidate;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $candidateType = Candidate::candidateType;
        DB::statement("SET SQL_MODE=''");
        return DataTables::of(
            Candidate::select([
                'candidates.*',
                DB::raw('SUM(time_sheet_details.hours) as total_hours'),
                DB::raw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) as hr_inv'),
                DB::raw('SUM(time_sheet_details.hours) - SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) as rem_hrs'),
                DB::raw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) * candidates.b_rate as amt_inv'),
                DB::raw('MIN(time_sheet_details.date_of_day) as start_date'),
                DB::raw('MAX(time_sheet_details.date_of_day) as last_time'),
            ])
            ->leftJoin('time_sheets', 'time_sheets.candidate_id', '=', 'candidates.id')
            ->leftJoin('time_sheet_details', 'time_sheet_details.time_sheet_id', '=', 'time_sheets.id')
            ->groupBy('candidates.id')
            ->with('visa', 'bCompany')
        )
            ->editColumn('candidate_type', function ($row) use ($candidateType) {
                return $candidateType[$row->candidate_type] ?? '';
            })->editColumn('visa', function ($row) {
                return $row->visa->name;
            })->addColumn('margin', function ($row) {
                return $row->b_rate - $row->c_rate;
            })->editColumn('b_vendor', function ($row) {
                return $row->bCompany->company_name;
            })->addColumn('hr_ts', function ($row) {
                return $row->total_hours ?? 0;
            })->addColumn('hr_inv', function ($row) {
                return $row->hr_inv ?? 0;
            })->addColumn('rem_hrs', function ($row) {
                return $row->rem_hrs ?? 0;
            })->addColumn('l_invoiced_date', function ($row) {
                return '';
            })->addColumn('last_time', function ($row) {
                return $row->last_time ? Carbon::parse($row->last_time)->format('d-m-Y') : '';
            })->addColumn('amt_inv', function ($row) {
                return number_format($row->amt_inv, 2) ?? 0;
            })->addColumn('mapped_rec_amt', function ($row) {
                return '';
            })->addColumn('due_rec_amt', function ($row) {
                return '';
            })->addColumn('hrs_due', function ($row) {
                return '';
            })->addColumn('start_date', function ($row) {
                return $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : '';
            })->addColumn('actions', function ($row) {
                return view('livewire.manage-candidate.actions', ['candidate' => $row, 'type' => 'action']);
            })->filterColumn('visa', function ($query, $keyword) {
                $query->whereHas('visa', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })->orderColumn('visa', function ($query, $order) {
                $query->join('visas', 'visas.id', '=', 'candidates.visa_status_id')
                      ->orderBy('visas.name', $order);
            })->filterColumn('margin', function ($query, $keyword) {
                $query->whereRaw('(b_rate - c_rate) like ?', ["%$keyword%"]);
            })->orderColumn('margin', function ($query, $order) {
                $query->orderByRaw('(b_rate - c_rate) ' . $order);
            })->filterColumn('b_vendor', function ($query, $keyword) {
                $query->whereHas('bCompany', function ($q) use ($keyword) {
                    $q->where('company_name', 'like', "%$keyword%");
                });
            })->orderColumn('b_vendor', function ($query, $order) {
                $query->join('b_companies', 'b_companies.id', '=', 'candidates.b_company_id')
                      ->orderBy('b_companies.company_name', $order);
            })->filterColumn('hr_ts', function ($query, $keyword) {
                $query->havingRaw('SUM(time_sheet_details.hours) like ?', ["%{$keyword}%"]);
            })->orderColumn('hr_ts', function ($query, $order) {
                $query->orderByRaw('SUM(time_sheet_details.hours) ' . $order);
            })->filterColumn('hr_inv', function ($query, $keyword) {
                $query->havingRaw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) like ?', ["%{$keyword}%"]);
            })->orderColumn('hr_inv', function ($query, $order) {
                $query->orderByRaw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) ' . $order);
            })->filterColumn('rem_hrs', function ($query, $keyword) {
                $query->havingRaw('(SUM(time_sheet_details.hours) - SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END)) like ?', ["%{$keyword}%"]);
            })->orderColumn('rem_hrs', function ($query, $order) {
                $query->orderByRaw('(SUM(time_sheet_details.hours) - SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END)) ' . $order);
            })->filterColumn('amt_inv', function ($query, $keyword) {
                $query->havingRaw('(SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) * candidates.b_rate) like ?', ["%{$keyword}%"]);
            })->orderColumn('amt_inv', function ($query, $order) {
                $query->orderByRaw('(SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) * candidates.b_rate) ' . $order);
            })->filterColumn('start_date', function ($query, $keyword) {
                $query->havingRaw('MIN(time_sheet_details.date_of_day) like ?', ["%{$keyword}%"]);
            })
            ->orderColumn('start_date', function ($query, $order) {
                $query->orderByRaw('MIN(time_sheet_details.date_of_day) ' . $order);
            })
            ->filterColumn('last_time', function ($query, $keyword) {
                $query->havingRaw('MAX(time_sheet_details.date_of_day) like ?', ["%{$keyword}%"]);
            })
            ->orderColumn('last_time', function ($query, $order) {
                $query->orderByRaw('MAX(time_sheet_details.date_of_day) ' . $order);
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
