<?php

namespace App\Livewire\ManageVisaCandidate;

use Livewire\Component;
use App\Models\Candidate;
use App\Models\Visa;

class VisaCandidateForm extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $candidateId;
    public $visaStatus = [];
    public $visaStatusId;
    public $visaStartDate;
    public $visaEndDate;
    public $idStartDate;
    public $idEndDate;
    public $cityState;
    public $showDate = true;

    public function mount($id='')
    {
        $this->menu = "Candidate";
        $this->breadcrumb = [
            ['route' => 'candidate', 'title' => 'Candidate'],
        ];
        $this->activeMenu = 'Add';
        $this->candidateType = 'w2';
        if($id){
            $this->activeMenu = 'Edit';
            $this->candidateId = $id;

            $candidate = Candidate::findOrFail($id);

            $this->visaStatusId  = $candidate->visa_status_id;
            $this->visaStartDate = $candidate->visa_start_date;
            $this->visaEndDate   = $candidate->visa_end_date;
            $this->idStartDate   = $candidate->id_start_date;
            $this->idEndDate     = $candidate->id_end_date;
            $this->cityState     = $candidate->city_state;
        }
        $this->manageDateFileds();
        $this->prepareData();
    }

    public function render()
    {
        return view('livewire.manage-visa-candidate.visa-candidate-form')->extends('layouts.app');
    }

    public function prepareData()
    {
        $this->visaStatus = Visa::active()->pluck('name', 'id');
    }

    public function updateVisaCandidate()
    {
        $rules = [
            'visaStatusId'  => 'required|integer',
            'cityState'     => 'required|string',
        ];

        if($this->showDate){
            $rules = array_merge($rules, [
                'visaStartDate' => 'required|string',
                'visaEndDate'   => 'required|string',
                'idStartDate'   => 'required|string',
                'idEndDate'     => 'required|string',
            ]);
        }

        $this->validate($rules);

        $filedData = [
            'visa_status_id'  => $this->visaStatusId,
            'visa_start_date' => $this->visaStartDate,
            'visa_end_date'   => $this->visaEndDate,
            'id_start_date'   => $this->idStartDate,
            'id_end_date'     => $this->idEndDate,
            'city_state'      => $this->cityState,
        ];

        if($this->candidateId){
            $candidate = Candidate::findOrFail($this->candidateId);
            $candidate->update($filedData);
            session()->flash('success', 'Candidate updated successfully!');
        }
        $this->redirect(route('visa-candidate'), navigate: true);

    }

    public function updated($propertyName)
    {
        if($propertyName == 'visaStatusId'){
            $this->manageDateFileds();
        }
        $this->dispatch('initPlugins');
    }

    public function manageDateFileds()
    {
        $visaStatus = Visa::where('id', $this->visaStatusId)->active()->pluck('name')->first();
        $this->showDate = true;
        if(strtolower($visaStatus) == 'citizen'){
            $this->showDate = false;
            $this->visaStartDate = '';
            $this->visaEndDate   = '';
            $this->idStartDate   = '';
            $this->idEndDate     = '';
        }
    }
}
