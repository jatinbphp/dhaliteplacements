<?php

namespace App\Livewire\ManageTimeSheet;

use Livewire\Component;
use App\Models\Candidate;
use App\Models\TimeSheet;
use App\Models\TimeSheetDetails;
use Carbon\Carbon;

class TimeSheetForm extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $candidateId;
    public $timeSheetId;
    public $selectedCandidateData = [];
    public $activeCandidate = [];
    public $weekEndDate;
    public $weekDate = [];
    public $weekDays = ['mon' => 0, 'tue' => 0, 'wed' => 0, 'thu' => 0, 'fri' => 0, 'sat' => 0, 'sun' => 0]; 

    public $timeSheetData = [];
    public $candidateAddedTimesheetDates = [];

    public function mount($id=0)
    {
        $this->menu = "Time Sheet";
        $this->breadcrumb = [
            ['route' => 'candidate', 'title' => 'Candidate'],
        ];
        $this->activeMenu = 'Add';
        if($id){
            $this->activeMenu = 'Edit';
            $this->timeSheetId = $id;

            $timeSheetData = TimeSheet::findOrFail($id);
            $this->weekEndDate = $timeSheetData->week_end_date;
            $this->candidateId = $timeSheetData->candidate_id;

            $timeSheetData = TimeSheetDetails::where('time_sheet_id', $this->timeSheetId)->get()->toArray();

            foreach ($timeSheetData as $data) {
                $day = $data['day_name'] ?? '';
                $this->weekDays[$day] = $data['hours'] ?? 0;
            }

            $this->manageTimeSheetData();
            $this->manageWeekData();
        }
        $this->activeCandidate = Candidate::pluck('c_name', 'id');
    }

    public function render()
    {
        return view('livewire.manage-time-sheet.time-sheet-form')->extends('layouts.app');
    }

    public function updated($propertyName)
    {
        if($propertyName == 'candidateId'){
            $this->manageTimeSheetData();
        }

        if($propertyName == 'weekEndDate'){
            $this->manageWeekData();
        }
        $this->dispatch('initPlugins');
    }

    public function manageTimeSheetData()
    {
        $selectedCandidateData = Candidate::where('id', $this->candidateId)->first();
        $this->selectedCandidateData = [];
        if($selectedCandidateData){
            $this->selectedCandidateData = $selectedCandidateData;
        }
        $this->timeSheetData = TimeSheet::where('candidate_id', $this->candidateId)->with('details')->get()->toArray();
        $this->candidateAddedTimesheetDates = array_column($this->timeSheetData, 'week_end_date');
    }

    public function manageWeekData()
    {
        $weekEnd = Carbon::createFromFormat('m-d-Y', $this->weekEndDate);
        $weekStart = $weekEnd->copy()->subDays(6);

        $this->weekDate = [];
        foreach (array_keys($this->weekDays) as $index => $day) {
            $this->weekDate[$day] = $weekStart->copy()->addDays($index)->format('m-d-Y');
        }
    }

    public function addTimeSheet()
    {
        $rules = [
            'weekEndDate' => 'required|string',
        ];

        $this->validate($rules);

        if($this->timeSheetId){
            $filedData = [
                'candidate_id'  => $this->candidateId,
                'week_end_date' => $this->weekEndDate,
            ];

            $timeSheetData = TimeSheet::findOrFail($this->timeSheetId);
            $timeSheetData->update($filedData);

            $timeSheetData = TimeSheetDetails::where('time_sheet_id', $this->timeSheetId)->get();

            foreach ($timeSheetData as $data) {
                $day = $data->day_name ?? '';
                $hours = $this->weekDays[$day] ?? 0;
                $data->hours = $this->weekDays[$day] ?? 0;
                $data->save();
            }

            session()->flash('success', 'TimeSheet updated successfully!');
        } else {
            $filedData = [
                'candidate_id'  => $this->candidateId,
                'week_end_date' => $this->weekEndDate,
            ];

            $timeSheet = TimeSheet::create($filedData);
            $lastInsertedId = $timeSheet->id;
            foreach ($this->weekDate as $day => $date) {
                $filedData = [
                    'time_sheet_id' => $lastInsertedId,
                    'date_of_day'   => $date,
                    'day_name'      => $day,
                    'hours'         => $this->weekDays[$day] ?? 0,
                ];

                TimeSheetDetails::create($filedData);
            }

            session()->flash('success', 'TimeSheet added successfully!');
        }

        $this->redirect(route('time-sheet'), navigate: true);
    }
}
