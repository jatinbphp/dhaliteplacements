<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Payment;
use App\Models\PaymentMapping;
use App\Models\Invoice;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ManagePayment extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $selectedPayment;
    public $mappedAmount;
    public $mappingInvoiceId;
    public $paymentId;
    public $errorMessage;

    public function render()
    {
        $this->menu = "Payment";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Payment';

        return view('livewire.manage-payment')->extends('layouts.app');
    }

    public function getPaymentData()
    {
        DB::statement("SET SQL_MODE=''");
        return DataTables::of(
            Payment::query()
                ->leftJoin('b_companies', 'payments.vendor_id', '=', 'b_companies.id')
                ->leftJoin('payment_mappings', 'payment_mappings.payment_id', '=', 'payments.id')
                ->select([
                    'payments.*',
                    'b_companies.company_name as vendor_name',
                    DB::raw('COALESCE(SUM(payment_mappings.amount), 0) as paid_invoice'),
                    DB::raw('payments.amount - COALESCE(SUM(payment_mappings.amount), 0) as remaining_amount'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_mappings.invoice_id) as mapped_invoice_id'),
                ])
                ->groupBy('payments.id', 'b_companies.company_name')
            )->addColumn('invoice_details', function ($row) {
                $mappings = PaymentMapping::query()
                    ->with([
                        'invoice.timeSheetDetails.timeSheet.candidate'  // Nested eager loading
                    ])
                    ->where('payment_id', $row->id)
                    ->get();

                return $mappings->map(function ($map) {
                    $invoice = optional($map->invoice);
                    $timeSheetDetails = $invoice->timeSheetDetails;

                    $candidate = optional($timeSheetDetails->first())
                        ?->timeSheet
                        ?->candidate;

                    $totalHours = $timeSheetDetails->sum('hours');
                    $rate = $invoice->rate ?? 0;
                    $totalAmount = $totalHours * $rate;

                    return [
                        'invoice_id'     => $map->invoice_id,
                        'mapped_amount'  => $map->amount,
                        'candidate_name' => optional($candidate)->c_name,
                        'total_amount'   => round($totalAmount, 2),
                    ];
                })->values()->toArray();
            })
            ->editColumn('vendor_name', function ($row) {
                return $row->vendor_name ?? '';
            })
            ->editColumn('amount', function ($row) {
                return number_format($row->amount ?? 0, 2);
            })
            ->addColumn('paid_invoice', function ($row) {
                return number_format($row->paid_invoice ?? 0, 2);
            })
            ->addColumn('remaining_amount', function ($row) {
                return number_format($row->remaining_amount ?? 0, 2);
            })
            ->addColumn('mapped_invoice_id', function ($row) {
                return $row->mapped_invoice_id;
            })
            ->addColumn('actions', function ($row) {
                return view('livewire.manage-payment.actions', ['row' => $row]);
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getLinkData($paymentId, $vendorId)
    {
        $this->paymentId = $paymentId;
        $candidateData = Invoice::select(
            'invoices.*',
            'candidates.c_name',
            'b_companies.company_name',
            \DB::raw('(
                SELECT COALESCE(SUM(hours), 0)
                FROM time_sheet_details
                WHERE time_sheet_details.invoice_id = invoices.id
            ) as total_hours'),
            \DB::raw('(
                SELECT COALESCE(SUM(amount), 0)
                FROM payment_mappings
                WHERE payment_mappings.invoice_id = invoices.id
            ) as mapped_amount')
        )
        ->join('candidates', 'candidates.id', '=', 'invoices.candidate_id')
        ->join('b_companies', 'b_companies.id', '=', 'candidates.b_company_id')
        ->where('candidates.b_company_id', $vendorId)
        ->orderBy('invoices.id', 'desc')
        ->get()
        ->toArray();
        $remaionngPaymentAmount = $this->getRemainingAmountFromPaymentId();
        $htmlContent = view('livewire.manage-payment.payment-link', ['candidateData' => $candidateData, 'remaionngPaymentAmount' => $remaionngPaymentAmount])->render();
        $this->dispatch('openMapping', ['htmlContent' => $htmlContent]);
    }

    public function linkPayment($invoiceId)
    {
        $this->mappingInvoiceId = $invoiceId;
        $this->dispatch('openMappAmount');
    }

    public function saveMappingAmount()
    {
        if($this->errorMessage){
            $this->dispatch('swal:error', $this->errorMessage);
            return;
        }
        if(!$this->mappedAmount){
            $this->dispatch('swal:warning', 'Please enter amount.');
            return;
        }
        if(!$this->paymentId || !$this->mappingInvoiceId || !$this->mappedAmount){
            $this->dispatch('swal:error', 'Something went wrong! Please try again.');
            return;
        }

        PaymentMapping::create([
            'payment_id' => $this->paymentId,
            'invoice_id' => $this->mappingInvoiceId,
            'amount' => $this->mappedAmount,
        ]);

        $this->dispatch('closeMappAmount');
        $this->dispatch('swal:success', 'Amount Mapped Successfully.');
    }

    public function checkAmount()
    {
        $this->errorMessage = '';
        $paymentReainingAmount = $this->getRemainingAmountFromPaymentId();
        if($this->mappedAmount > $paymentReainingAmount){
            $this->errorMessage = 'The mapped amount cannot be greater than the total remaining payment amount.';
            return;
        }
        $remainingAmount = $this->getRemainingAmountFromInvoiceId();
        if($this->mappedAmount > $remainingAmount){
            $this->errorMessage = 'The mapped amount cannot be greater than the total invoice amount.';
            return;
        }
    }

    public function getRemainingAmountFromInvoiceId()
    {
        $invoice = Invoice::with('timeSheetDetails')
            ->where('id', $this->mappingInvoiceId)
            ->first();

        $totalHours = $invoice->timeSheetDetails->sum('hours');
        $rate = $invoice->rate ?? 0;
        $totalAmount = $totalHours * $rate;

        $mappedAmount = PaymentMapping::where('invoice_id', $this->mappingInvoiceId)->sum('amount');

        return ($totalAmount - $mappedAmount);
    }

    public function getRemainingAmountFromPaymentId()
    {
        $payment = Payment::select(
            'payments.*',
            \DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM payment_mappings WHERE payment_id = payments.id) as mapped_amount')
        )
        ->find($this->paymentId);

        return $payment->amount - $payment->mapped_amount;
    }
}
