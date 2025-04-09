<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Candidate;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManageDateWiseInvoice extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function render()
    {
        return view('livewire.manage-date-wise-invoice')->extends('layouts.app');
    }

    public function mount()
    {
        $this->menu = "Invoice Date Wise";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Invoice Date Wise';
    }

    public function getDateWiseInvoiceTrackingData(Request $request)
    {
        $statusData = Candidate::candidateStatus;
        $candidateTypes = Candidate::candidateType;

        return DataTables::of(
            Invoice::leftJoin('candidates', 'candidates.id', '=', 'invoices.candidate_id')
            ->leftJoin('b_companies', 'b_companies.id', '=', 'candidates.b_company_id')
            ->select([
                'invoices.*',
                'candidates.c_name as candidate_name',
                'candidates.status as candidate_status',
                'candidates.candidate_type as candidate_type',
                'candidates.b_rate as rate',
                'b_companies.company_name as b_vender',
                'candidates.b_due_terms_id as net_terms',
                DB::raw('(SELECT SUM(hours) FROM time_sheet_details WHERE invoice_id = invoices.id) as inv_hr'),
                DB::raw('(
                    (SELECT SUM(hours) FROM time_sheet_details WHERE invoice_id = invoices.id)
                    * candidates.b_rate
                ) as inv_amt'),
                DB::raw('(
                    (SELECT candidates.b_due_terms_id FROM candidates WHERE candidates.id = invoices.candidate_id)
                    - DATEDIFF(CURDATE(), invoices.generated_date)
                ) as due_in'),
                DB::raw('DATEDIFF(CURDATE(), invoices.generated_date) as sent_days'),
                DB::raw('
                    CASE
                        WHEN (
                            (SELECT candidates.b_due_terms_id FROM candidates WHERE candidates.id = invoices.candidate_id)
                            - DATEDIFF(CURDATE(), invoices.generated_date)
                        ) < 0 THEN
                            (SELECT SUM(hours) FROM time_sheet_details WHERE invoice_id = invoices.id) * candidates.b_rate
                        ELSE 0
                    END as past_due
                '),
            ])
        )->filter(function ($query) use ($request) {
            if ($request->has('search') && $search = $request->get('search')['value']) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoices.generated_date', 'like', "%{$search}%")
                      ->orWhere('invoices.id', 'like', "%{$search}%")
                      ->orWhere('candidates.c_name', 'like', "%{$search}%")
                      ->orWhere('candidates.status', 'like', "%{$search}%")
                      ->orWhere('candidates.candidate_type', 'like', "%{$search}%")
                      ->orWhere('b_companies.company_name', 'like', "%{$search}%")
                      ->orWhere('invoices.from_date', 'like', "%{$search}%")
                      ->orWhere('invoices.to_date', 'like', "%{$search}%")
                      ->orWhere(DB::raw('(SELECT SUM(hours) FROM time_sheet_details WHERE invoice_id = invoices.id)'), 'like', "%{$search}%")
                      ->orWhere('candidates.b_rate', 'like', "%{$search}%")
                      ->orWhere(DB::raw('((SELECT SUM(hours) FROM time_sheet_details WHERE invoice_id = invoices.id) * candidates.b_rate)'), 'like', "%{$search}%")
                      ->orWhere(DB::raw('DATEDIFF(CURDATE(), invoices.generated_date)'), 'like', "%{$search}%");
                });
            }
        })
        ->editColumn('generated_date', function ($row) {
            return formateDate($row->generated_date, 'Y-m-d', 'd-m-Y');
        })
        ->addColumn('candidate_name', function ($row) {
            return $row->candidate->c_name ?? '';
        })
        ->addColumn('status', function ($row) use ($statusData) {
            return $statusData[$row->candidate_status] ?? '';
        })
        ->addColumn('type', function ($row) use ($candidateTypes) {
            return $candidateTypes[$row->candidate->candidate_type] ?? '';
        })
        ->addColumn('b_vender', function ($row) {
            return $row->b_vender ?? '';
        })
        ->addColumn('time_from_to', function () {
            return '';
        })
        ->editColumn('from_date', function ($row) {
            return formateDate($row->from_date, 'Y-m-d', 'd-m-Y');
        })
        ->editColumn('to_date', function ($row) {
            return formateDate($row->to_date, 'Y-m-d', 'd-m-Y');
        })
        ->addColumn('inv_hr', function ($row) {
            return $row->inv_hr ?? 0;
        })
        ->addColumn('rate', function ($row) {
            return $row->rate ?? 0;
        })
        ->addColumn('inv_amt', function ($row) {
            return number_format($row->inv_amt ?? 0, 2);
        })
        ->addColumn('map', function () {
            return '';
        })
        ->addColumn('due', function () {
            return '';
        })
        ->addColumn('due_in', function ($row) {
            $dueIn = $row->due_in ?? 0;
            return $dueIn < 0
                ? '<span class="text-danger">' . $dueIn . '</span>'
                : $dueIn;
        })
        ->addColumn('sent_days', function ($row) {
            return $row->sent_days ?? 0;
        })
        ->addColumn('net_terms', function ($row) {
            return $row->candidate->b_due_terms_id ?? '';
        })
        ->addColumn('payment_id', function () {
            return '';
        })
        ->addColumn('ttl_hr_due', function () {
            return '';
        })
        ->addColumn('past_due', function($row) {
            return number_format($row->past_due, 2);
        })
        ->orderColumn('candidate_name', function ($query, $order) {
            $query->orderBy('candidates.c_name', $order);
        })
        ->orderColumn('status', function ($query, $order) {
            $query->orderBy('candidates.status', $order);
        })
        ->orderColumn('candidate_type', function ($query, $order) {
            $query->orderBy('candidates.candidate_type', $order);
        })
        ->orderColumn('b_vender', function ($query, $order) {
            $query->orderBy('b_companies.company_name', $order);
        })
        ->orderColumn('inv_hr', function ($query, $order) {
            $query->orderBy('inv_hr', $order);
        })
        ->orderColumn('inv_hr', function ($query, $order) {
            $query->orderBy('inv_hr', $order);
        })
        ->orderColumn('rate', function ($query, $order) {
            $query->orderBy('rate', $order);
        })
        ->orderColumn('inv_amt', function ($query, $order) {
            $query->orderBy('inv_amt', $order);
        })
        ->orderColumn('due_in', function ($query, $order) {
            $query->orderBy('due_in', $order);
        })
        ->orderColumn('sent_days', function ($query, $order) {
            $query->orderBy('sent_days', $order);
        })
        ->orderColumn('net_terms', function ($query, $order) {
            $query->orderBy('net_terms', $order);
        })
        ->orderColumn('past_due', function ($query, $order) {
            $query->orderBy('past_due', $order);
        })
        ->rawColumns(['due_in'])
        ->make(true);

    }
}
