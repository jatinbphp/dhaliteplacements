<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidate;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ManageInvoiceTracking extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $dateRange;
    public $billingOptions = [];
    public $billingTypeId = '';

    public function mount()
    {
        $this->menu = "Invoice Tracking";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Invoice Tracking';
        $this->billingOptions = ['both' => 'Both'] + Candidate::billingOptions;
        $this->billingTypeId = 'both';
    }

    public function render()
    {
        return view('livewire.manage-invoice-tracking')->extends('layouts.app');
    }

    public function getInvoiceTrackingData(Request $request)
    {
        $startDate = $endDate = '';
        if ($request->dateRange && !empty($request->dateRange)) {
            if (strpos($request->dateRange, " to ") !== false) {
                [$startDate, $endDate] = explode(" to ", $request->dateRange);
                $startDate = formateDate($startDate); // Ensure this formats the date correctly.
                $endDate = formateDate($endDate);     // Ensure this formats the date correctly.
            }
        }

        $billingOption = 'both';
        if($request->billingOption && !empty($request->billingOption)){
            $billingOption = $request->billingOption;
        }
        $this->billingOptions = ['both' => 'Both'] + Candidate::billingOptions;

        return DataTables::of(
            Candidate::select('id', 'c_name', 'b_company_id','b_rate','billing_type')
                ->with(['bCompany', 'timeSheets' => function ($query) use ($startDate, $endDate) {
                    $query->with(['details' => function ($q) use ($startDate, $endDate) {
                        // Apply the date filter only if both dates are provided
                        if ($startDate && $endDate) {
                            $q->whereBetween('time_sheet_details.date_of_day', [$startDate, $endDate]);
                        }
                    }]);
                }])
                ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                    // Apply the date filter on timeSheets only if both dates are provided
                    $query->whereHas('timeSheets.details', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('time_sheet_details.date_of_day', [$startDate, $endDate]);
                    });
                })->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                    $query->whereHas('timeSheets.details', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('time_sheet_details.date_of_day', [$startDate, $endDate]);
                    });
                })
                ->when($billingOption && $billingOption != 'both', function ($query) use ($billingOption) {
                    $query->where('candidates.billing_type', $billingOption);
                })->whereHas('timeSheets')   
                ->groupBy(
                    'candidates.id',
                    'candidates.c_name',
                    'candidates.b_company_id',
                    'candidates.b_rate',
                    'candidates.billing_type',
                )
            )
            ->editColumn('c_name', function ($row) {
                $billingType = $this->billingOptions[$row->billing_type] ?? '';
                $candidateName = $row->c_name ?? '';

                return '<div>
                            <span>' . $candidateName . '</span><br>
                            <span><b>' . $billingType . '</b></span>
                        </div>';
            })->addColumn('vendor_company_name', function ($row) {
                return $row->bCompany->company_name ?? '';
            })
            ->addColumn('total_hours', function ($row) {
                return $row->timeSheets->flatMap(function ($sheet) {
                    return $sheet->details->pluck('hours');
                })->sum() ?? 0;
            })
            ->addColumn('generated_hours', function ($row) {
                return $row->timeSheets->flatMap(function ($sheet) {
                    return $sheet->details->pluck('generated_hours');
                })->sum() ?? 0;
            })
            ->addColumn('time_from_to', function ($row) {
                return '';
            })
            ->addColumn('invoice_id', function ($row) {
                return optional($row->timeSheets->first())->invoice_id ?? '';
            })
            ->addColumn('invoice_no', function ($row) {
                return optional($row->timeSheets->first())->invoice_no ?? '';
            })
            ->addColumn('rate', function ($row) {
                return $row->b_rate ?? '';
            })
            ->addColumn('generated_date', function ($row) {
                return optional($row->timeSheets->first())->generated_date ?? '';
            })
            ->addColumn('invoice_amount', function ($row) {
                $hours = $row->timeSheets->flatMap(function ($sheet) {
                    return $sheet->details->pluck('hours');
                })->sum();
                return  number_format($hours * $row->b_rate);
            })
            ->addColumn('received_amount', function ($row) {
                $receiveAmount = 0;
                return $receiveAmount;
            })
            ->addColumn('amount_due', function ($row) {
                $hours = $row->timeSheets->flatMap(function ($sheet) {
                    return $sheet->details->pluck('hours');
                })->sum();
                $amount =  ($hours * $row->b_rate);
                $receiveAmount = 0;
                return number_format($amount - $receiveAmount);
            })
            ->addColumn('mail', function ($row) {
                return '';
            })
            ->addColumn('mapping', function ($row) {
                return '';
            })
            ->addColumn('mapping_date', function ($row) {
                return '';
            })
            ->addColumn('actions', function ($row) {
                return '';
                //return view('livewire.manage-candidate.actions', ['candidate' => $row, 'type' => 'action']);
            })
            ->rawColumns(['actions', 'c_name'])
            ->make(true);
    }

    public function clearFilter()
    {
        $this->dateRange = '';
        $this->dispatch('refreshDataTable');
    }

    public function filter()
    {
        $this->dispatch('refreshDataTable');
    }
}
