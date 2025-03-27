<?php

namespace App\Livewire\ManageCandidate;

use Livewire\Component;
use App\Models\LCompany;
use App\Models\PCompany;
use App\Models\BCompany;
use App\Models\OurCompany;
use App\Models\Candidate;
use App\Models\Visa;
use Illuminate\Validation\Rule;

class ManageCandidateForm extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $candidateId;
    public $candidateOptions = [];
    public $candidateType = '';
    public $lCompanyData = [];
    public $lCompanyId;
    public $selectedLCompanyAddress = '';
    public $visaStatus = [];
    public $pvCompanyData = [];
    public $pvCompanyId = '';
    public $selectedPvCompanyAddress = '';
    public $bCompanyData = [];
    public $bCompanyId = '';
    public $selectedBCompanyAddress = '';
    public $bDueTerms = [];
    public $ourCompanyData = [];
    public $ourCompanyId = '';
    public $selectedOurCompanyAddress = '';
    public $selectedOurCompanyPhone = '';
    public $lRate;
    public $lAggrement = 0;
    public $cId;
    public $cName;
    public $visaStatusId;
    public $visaStartDate;
    public $visaEndDate;
    public $idStartDate;
    public $idEndDate;
    public $cityState;
    public $project;
    public $cRate;
    public $candidateNote;
    public $cRateNote;
    public $position;
    public $client;
    public $laptReceived = 0;
    public $bDueTermsId;
    public $bRate;
    public $bRateNote;
    public $bAggrement = 0;
    public $cAggrement = 0;
    public $marketer;
    public $recruiter;
    public $isCidAvailable = 3;
    public $oldCandidateData = [];
    public $showDate = true;
    public $candidateStatus = [];
    public $status;
    public $billingOptions = [];
    public $billingTypeId = Candidate::BILLING_MONTHLY;

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
            $this->oldCandidateData = $candidate;

            $this->candidateType = $candidate->candidate_type;
            $this->lCompanyId    = $candidate->l_company_id;
            $this->lRate         = $candidate->l_rate;
            $this->lAggrement    = $candidate->l_aggrement;
            $this->cId           = $candidate->c_id;
            $this->cName         = $candidate->c_name;
            $this->visaStatusId  = $candidate->visa_status_id;
            $this->visaStartDate = $candidate->visa_start_date;
            $this->visaEndDate   = $candidate->visa_end_date;
            $this->idStartDate   = $candidate->id_start_date;
            $this->idEndDate     = $candidate->id_end_date;
            $this->cityState     = $candidate->city_state;
            $this->project       = $candidate->project;
            $this->cRate         = $candidate->c_rate;
            $this->candidateNote = $candidate->candidate_note;
            $this->cRateNote     = $candidate->c_rate_note;
            $this->position      = $candidate->position;
            $this->client        = $candidate->client;
            $this->laptReceived  = $candidate->lapt_received;
            $this->pvCompanyId   = $candidate->pv_company_id;
            $this->bCompanyId    = $candidate->b_company_id;
            $this->bRate         = $candidate->b_rate;
            $this->bRateNote     = $candidate->b_rate_note;
            $this->ourCompanyId  = $candidate->our_company_id;
            $this->bAggrement    = $candidate->b_aggrement;
            $this->cAggrement    = $candidate->c_aggrement;
            $this->marketer      = $candidate->marketer;
            $this->recruiter     = $candidate->recruiter;
            $this->bDueTermsId   = $candidate->b_due_terms_id;
            $this->status        = $candidate->status;
            $this->billingTypeId = $candidate->billing_type;


            $this->updateLCompany();
            $this->updatePvCompany();
            $this->updateBCompany();
            $this->updateOurCompany();
            $this->manageDateFileds();
        }
        $this->prepareData();
    }

    public function render()
    {
        return view('livewire.manage-candidate.manage-candidate-form')->extends('layouts.app');
    }

    public function updateLCompany()
    {
        $this->selectedLCompanyAddress = LCompany::where('id', $this->lCompanyId)
            ->active()
            ->value('address');

    }

    public function updatePvCompany()
    {
        $this->selectedPvCompanyAddress = PCompany::where('id', $this->pvCompanyId)
            ->active()
            ->value('address');
    }

    public function updateBCompany()
    {
        $this->selectedBCompanyAddress = BCompany::where('id', $this->bCompanyId)
            ->active()
            ->value('address');
    }

    public function updated($propertyName)
    {
        if($propertyName == 'lCompanyId'){
            $this->updateLCompany();
        } elseif($propertyName == 'pvCompanyId'){
            $this->updatePvCompany();
        } elseif($propertyName == 'bCompanyId'){
            $this->updateBCompany();
        }elseif($propertyName == 'ourCompanyId'){
            $this->updateOurCompany();
        }elseif($propertyName == 'visaStatusId'){
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
        }
    }

    public function updateOurCompany()
    {
        $ourCompanyData = OurCompany::where('id', $this->ourCompanyId)
            ->active()
            ->first();

        if($ourCompanyData){
            $this->selectedOurCompanyAddress = $ourCompanyData->address;
            $this->selectedOurCompanyPhone = $ourCompanyData->phone;
        }
    }

    public function updateCandidate()
    {
        $rules = [
            'candidateType'             => 'required|string',
            'lCompanyId'                => 'required_if:candidateType,c2c,w2_c2c',
            'selectedLCompanyAddress'   => 'required_if:candidateType,c2c,w2_c2c',
            'lRate'                     => 'required_if:candidateType,c2c,w2_c2c',
            'lAggrement'                => 'required_if:candidateType,c2c,w2_c2c',
            'cId'                       => [
                'required',
                'integer',
                Rule::unique('candidates', 'c_id')->ignore($this->candidateId),
            ],
            'cName'                     => 'required|string',
            'showDate'                  => 'required|boolean',
            'visaStatusId'              => 'required|integer',
            'cityState'                 => 'required|string',
            'project'                   => 'required',
            'cRate'                     => 'required|numeric',
            'position'                  => 'required|string',
            'client'                    => 'required|string',
            'laptReceived'              => 'required|boolean',
            'pvCompanyId'               => 'required|integer',
            'selectedPvCompanyAddress'  => 'required|string',
            'bCompanyId'                => 'required|integer',
            'selectedBCompanyAddress'   => 'required|string',
            'bRate'                     => 'required|numeric',
            'ourCompanyId'              => 'required|integer',
            'selectedOurCompanyAddress' => 'required|string',
            'selectedOurCompanyPhone'   => 'required|string',
            'bAggrement'                => 'required|boolean',
            'cAggrement'                => 'required|boolean',
            'marketer'                  => 'required|string',
            'recruiter'                 => 'required|string',
            'bDueTermsId'               => 'required|string',
            'billingTypeId'             => 'required',
        ];


        if ($this->showDate) {
            $rules = array_merge($rules, [
                'visaStartDate' => 'required|string',
                'visaEndDate'   => 'required|string',
                'idStartDate'   => 'required|string',
                'idEndDate'     => 'required|string',
            ]);
        }

        $this->validate($rules);

        $filedData = [
            'candidate_type'   => $this->candidateType,
            'l_company_id'     => $this->lCompanyId,
            'l_rate'           => $this->lRate,
            'l_aggrement'      => $this->lAggrement,
            'c_id'             => $this->cId,
            'c_name'           => $this->cName,
            'visa_status_id'   => $this->visaStatusId,
            'visa_start_date'  => $this->visaStartDate,
            'visa_end_date'    => $this->visaEndDate,
            'id_start_date'    => $this->idStartDate,
            'id_end_date'      => $this->idEndDate,
            'city_state'       => $this->cityState,
            'project'          => $this->project,
            'c_rate'           => $this->cRate,
            'candidate_note'   => $this->candidateNote,
            'c_rate_note'      => $this->cRateNote,
            'position'         => $this->position,
            'client'           => $this->client,
            'lapt_received'    => $this->laptReceived,
            'pv_company_id'    => $this->pvCompanyId,
            'b_company_id'     => $this->bCompanyId,
            'b_rate'           => $this->bRate,
            'b_rate_note'      => $this->bRateNote,
            'our_company_id'   => $this->ourCompanyId,
            'b_aggrement'      => $this->bAggrement,
            'c_aggrement'      => $this->cAggrement,
            'marketer'         => $this->marketer,
            'recruiter'        => $this->recruiter,
            'b_due_terms_id'   => $this->bDueTermsId,
            'status'           => $this->status,
            'billing_type'     => $this->billingTypeId
        ];

        if($this->candidateId){
            $candidate = Candidate::findOrFail($this->candidateId);
            $candidate->update($filedData);
            session()->flash('success', 'Candidate updated successfully!');
        } else {
            Candidate::create($filedData);
            session()->flash('success', 'Candidate created successfully!');
        }
        $this->redirect(route('candidate'), navigate: true);

    }

    public function prepareData()
    {
        $this->candidateOptions = Candidate::candidateType;

        $this->visaStatus      = Visa::active()->pluck('name', 'id');
        $this->lCompanyData    = LCompany::active()->pluck('company_name', 'id');
        $this->pvCompanyData   = PCompany::active()->pluck('company_name', 'id');
        $this->bCompanyData    = BCompany::active()->pluck('company_name', 'id');
        $this->ourCompanyData  = OurCompany::active()->pluck('company_name', 'id');
        $this->bDueTerms       = array_combine(range(15, 75, 5), range(15, 75, 5));
        $this->candidateStatus = Candidate::candidateStatus;
        $this->billingOptions  = Candidate::billingOptions;
    }

    public function checkExistiongCid()
    {
        if(!$this->cId){
            $this->isCidAvailable = 3;
            return $this;
        }
        if(Candidate::where('c_id', $this->cId)->exists()){
            $this->isCidAvailable = 0;
        } else {
            $this->isCidAvailable = 1;
        }
        if($this->oldCandidateData && $this->oldCandidateData->c_id == $this->cId){
            $this->isCidAvailable = 3;
        }
        return $this;
    }
}
