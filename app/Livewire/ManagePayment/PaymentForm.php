<?php

namespace App\Livewire\ManagePayment;

use Livewire\Component;
use App\Models\BCompany;
use App\Models\Candidate;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentForm extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $vendors = [];
    public $vendorId;
    public $candidateData = [];
    public $amountReceived;
    public $amountDate;
    public $ceoReference;

    public function mount($id = null)
    {
        $this->menu = "Payment";
        $this->breadcrumb = [
            ['route' => 'payment', 'title' => 'Payment'],
        ];
        $this->activeMenu = 'Add';
        $this->amountDate = Carbon::now()->format('m-d-Y');
        $this->vendors = BCompany::active()->pluck('company_name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.manage-payment.payment-form')->extends('layouts.app');
    }

    public function updated($propertyName)
    {
        if($propertyName == 'vendorId'){
            $this->prepareCandidateData();
        }
        $this->dispatch('initPlugins');
    }

    public function prepareCandidateData()
    {
        if(!$this->vendorId){
            return [];
        }
        $this->candidateData = DB::table('candidates')
            ->select([
                'candidates.id',
                'candidates.c_name',
                'candidates.b_rate',
                DB::raw('COUNT(DISTINCT CASE WHEN candidates.status = ' . Candidate::STATUS_ACTIVE . ' THEN candidates.id END) as active_status_candidate'),
                DB::raw('COUNT(DISTINCT CASE WHEN candidates.status = ' . Candidate::STATUS_PROJECT_END . ' THEN candidates.id END) as project_end_status_candidate'),
                DB::raw('SUM(time_sheet_details.hours) - SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) as rem_hrs'),
                DB::raw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours * candidates.b_rate ELSE 0 END) as amt_invoiced'),
                DB::raw('SUM(
                    CASE
                        WHEN candidates.b_due_terms_id - DATEDIFF(CURDATE(), invoices.generated_date) < 0 THEN
                            time_sheet_details.hours * candidates.b_rate
                        ELSE 0
                    END
                ) as past_due'),
                DB::raw('SUM(
                    CASE
                        WHEN candidates.b_due_terms_id - DATEDIFF(CURDATE(), invoices.generated_date) < 0 THEN
                            time_sheet_details.hours
                        ELSE 0
                    END
                ) as past_due_hours')
            ])
            ->leftJoin('time_sheets', 'time_sheets.candidate_id', '=', 'candidates.id')
            ->leftJoin('time_sheet_details', 'time_sheet_details.time_sheet_id', '=', 'time_sheets.id')
            ->leftJoin('invoices', 'invoices.candidate_id', '=', 'candidates.id')
            ->where('candidates.b_company_id', $this->vendorId)
            ->groupBy('candidates.id')
            ->get()
            ->toArray();
    }

    public function addPayment()
    {
        $this->validate([
            'vendorId' => 'required',
            'amountReceived' => 'required|numeric',
            'amountDate' => 'required',
        ]);

        Payment::create([
            'vendor_id' => $this->vendorId,
            'amount' => $this->amountReceived,
            'amount_date' => $this->amountDate,
            'ceo_reference' => $this->ceoReference,
        ]);

        session()->flash('message', 'Payment saved successfully!');
        $this->redirect(route('payment'), navigate: true);
    }
}
