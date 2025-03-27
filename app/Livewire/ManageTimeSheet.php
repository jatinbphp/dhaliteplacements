<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TimeSheet;
use App\Models\TimeSheetDetails;
use App\Models\Candidate;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimeSheetExport;
use Carbon\Carbon;

class ManageTimeSheet extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $hoursByDate = [];

    protected $listeners = ['openCalendarModel'];

    public function render()
    {
        $this->menu = "Time Sheet";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Time Sheet';

        return view('livewire.manage-time-sheet')->extends('layouts.app');
    }

    public function getTimeSheetData()
    {
        $candidateType = Candidate::candidateType;
        return DataTables::of(
            TimeSheet::leftJoin('candidates', 'candidates.id', '=', 'time_sheets.candidate_id')
                ->leftJoin('visas', 'visas.id', '=', 'candidates.visa_status_id')
                ->leftJoin('time_sheet_details', 'time_sheet_details.time_sheet_id', '=', 'time_sheets.id')
                ->selectRaw('
                    time_sheets.*,
                    candidates.c_id as candidate_code,
                    candidates.c_name as candidate_name,
                    visas.name as visa_name,
                    (SELECT MAX(ts.week_end_date) FROM time_sheets AS ts WHERE ts.candidate_id = time_sheets.candidate_id) as last_week_end_date,
                    (SELECT SUM(tsd.hours) 
                         FROM time_sheet_details AS tsd 
                         JOIN time_sheets AS ts ON ts.id = tsd.time_sheet_id 
                         WHERE ts.candidate_id = time_sheets.candidate_id) as total_time_sheet_hours,
                    candidates.candidate_type as candidate_type,
                    SUM(CASE WHEN time_sheet_details.day_name = "mon" THEN time_sheet_details.hours ELSE 0 END) as mon_hours,
                    SUM(CASE WHEN time_sheet_details.day_name = "tue" THEN time_sheet_details.hours ELSE 0 END) as tue_hours,
                    SUM(CASE WHEN time_sheet_details.day_name = "wed" THEN time_sheet_details.hours ELSE 0 END) as wed_hours,
                    SUM(CASE WHEN time_sheet_details.day_name = "thu" THEN time_sheet_details.hours ELSE 0 END) as thu_hours,
                    SUM(CASE WHEN time_sheet_details.day_name = "fri" THEN time_sheet_details.hours ELSE 0 END) as fri_hours,
                    SUM(CASE WHEN time_sheet_details.day_name = "sat" THEN time_sheet_details.hours ELSE 0 END) as sat_hours,
                    SUM(CASE WHEN time_sheet_details.day_name = "sun" THEN time_sheet_details.hours ELSE 0 END) as sun_hours,
                    SUM(time_sheet_details.hours) as total_hours
                ')->groupBy('time_sheets.id')
            )->filter(function ($query) {
                if (request()->has('search') && request('search')['value']) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('candidates.c_id', 'like', "%{$search}%")
                          ->orWhere('candidates.c_name', 'like', "%{$search}%")
                          ->orWhere('visas.name', 'like', "%{$search}%")
                          ->orWhere('candidates.candidate_type', 'like', "%{$search}%")
                          ->orWhere('time_sheets.created_at', 'like', "%{$search}%");
                    });
                }
            })
            ->addColumn('c_id', fn($row) => $row->candidate_code ?? '')
            ->addColumn('c_name', fn($row) => $row->candidate_name ?? '')
            ->addColumn('visa', fn($row) => $row->visa_name ?? '')
            ->addColumn('candidate_type', fn($row) => $candidateType[$row->candidate_type] ?? '')
            ->addColumn('hr_ts', function ($row) {
                return $row->total_time_sheet_hours;
            })
            ->addColumn('last_time', function ($row) {
                return $row->last_week_end_date ? Carbon::parse($row->last_week_end_date)->format('m-d-Y'): '';
            })
            ->addColumn('mon', fn($row) => number_format($row->mon_hours, 2))
            ->addColumn('tue', fn($row) => number_format($row->tue_hours, 2))
            ->addColumn('wed', fn($row) => number_format($row->wed_hours, 2))
            ->addColumn('thu', fn($row) => number_format($row->thu_hours, 2))
            ->addColumn('fri', fn($row) => number_format($row->fri_hours, 2))
            ->addColumn('sat', fn($row) => number_format($row->sat_hours, 2))
            ->addColumn('sun', fn($row) => number_format($row->sun_hours, 2))
            ->addColumn('total_hours', fn($row) => number_format($row->total_hours, 2))
            ->addColumn('actions', function ($row) {
                return view('livewire.manage-time-sheet.actions', ['timesheet' => $row, 'type' => 'action']);
            })
            ->orderColumn('c_id', fn($query, $order) => $query->orderBy('candidate_code', $order))
            ->orderColumn('c_name', fn($query, $order) => $query->orderBy('candidate_name', $order))
            ->orderColumn('visa', fn($query, $order) => $query->orderBy('visa_name', $order))
            ->orderColumn('candidate_type', fn($query, $order) => $query->orderBy('candidate_type', $order))
            ->orderColumn('hr_ts', fn($query, $order) => $query->orderBy('total_time_sheet_hours', $order))
            ->orderColumn('last_time', fn($query, $order) => $query->orderBy('last_week_end_date', $order))
            ->orderColumn('total_hours', fn($query, $order) => $query->orderBy('total_hours', $order))
            ->orderColumn('mon', fn($query, $order) => $query->orderBy('mon_hours', $order))
            ->orderColumn('tue', fn($query, $order) => $query->orderBy('tue_hours', $order))
            ->orderColumn('wed', fn($query, $order) => $query->orderBy('wed_hours', $order))
            ->orderColumn('thu', fn($query, $order) => $query->orderBy('thu_hours', $order))
            ->orderColumn('fri', fn($query, $order) => $query->orderBy('fri_hours', $order))
            ->orderColumn('sat', fn($query, $order) => $query->orderBy('sat_hours', $order))
            ->orderColumn('sun', fn($query, $order) => $query->orderBy('sun_hours', $order))
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function openCalendar($timeSheetId)
    {
        $candidateId = TimeSheet::where('id', $timeSheetId)->value('candidate_id');

        $timeSheetIds = TimeSheet::where('candidate_id', $candidateId)
            ->pluck('id')
            ->toArray();

        $allHours = TimeSheetDetails::whereIn('time_sheet_id', $timeSheetIds)
            ->get()
            ->map(function ($item) {
                return [
                    'time_sheet_id' => $item->time_sheet_id,
                    'date_of_day'   => $item->getRawOriginal('date_of_day'), // Get raw value
                    'hours'         => $item->hours,
                ];
            })
            ->groupBy('time_sheet_id');

        $this->hoursByDate = [
            'current' => isset($allHours[$timeSheetId]) 
                ? $allHours[$timeSheetId]->pluck('hours', 'date_of_day')->toArray() 
                : [],
            
            'other' => $allHours->filter(function ($_, $key) use ($timeSheetId) {
                    return $key !== $timeSheetId;
                })->flatMap(function ($items) {
                    return $items->pluck('hours', 'date_of_day');
                })->toArray()
        ];

        $this->dispatch('openCalendarModel');
    }

    public function exportTimeSheetData()
    {
        return Excel::download(new TimeSheetExport, 'timesheets.xlsx');
    }

}
