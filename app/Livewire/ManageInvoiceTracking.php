<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidate;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::statement("SET SQL_MODE=''");
        $totalHoursQuery = DB::table('time_sheet_details')
            ->selectRaw('COALESCE(SUM(hours), 0)')
            ->join('time_sheets', 'time_sheets.id', '=', 'time_sheet_details.time_sheet_id')
            ->whereColumn('time_sheets.candidate_id', 'candidates.id')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('time_sheet_details.date_of_day', [$startDate, $endDate]);
            });

        $generatedHoursQuery = DB::table('time_sheet_details')
            ->selectRaw('COALESCE(SUM(hours), 0)')
            ->join('time_sheets', 'time_sheets.id', '=', 'time_sheet_details.time_sheet_id')
            ->whereColumn('time_sheets.candidate_id', 'candidates.id')
            ->whereNotNull('invoice_id')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('time_sheet_details.date_of_day', [$startDate, $endDate]);
            });

        $invoiceAmountQuery = DB::table('time_sheet_details')
            ->selectRaw('COALESCE(SUM(hours), 0) * candidates.b_rate')
            ->join('time_sheets', 'time_sheets.id', '=', 'time_sheet_details.time_sheet_id')
            ->whereColumn('time_sheets.candidate_id', 'candidates.id')
            ->whereNotNull('invoice_id')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('time_sheet_details.date_of_day', [$startDate, $endDate]);
            });

        return DataTables::of(
            Candidate::select('candidates.id', 'candidates.c_name', 'candidates.b_company_id', 'candidates.b_rate', 'candidates.billing_type')
                ->with([
                    'bCompany',
                    'timeSheets' => function ($query) use ($startDate, $endDate) {
                        $query->with([
                            'details' => function ($q) use ($startDate, $endDate) {
                                $q->whereNotNull('invoice_id');

                                if ($startDate && $endDate) {
                                    $q->whereBetween('time_sheet_details.date_of_day', [$startDate, $endDate]);
                                }

                                $q->with('invoice'); // load invoice data
                            },
                        ]);
                    },
                ])->addSelect([
                    'total_hours' => $totalHoursQuery,
                    'generated_hours' => $generatedHoursQuery,
                    'invoice_amount' => $invoiceAmountQuery
                ])
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereHas('timeSheets.details', function ($q) use ($startDate, $endDate) {
                        $q->whereNotNull('invoice_id')
                          ->whereBetween('time_sheet_details.date_of_day', [$startDate, $endDate]);
                    });
                }, function ($query) {
                    $query->whereHas('timeSheets.details', function ($q) {
                        $q->whereNotNull('invoice_id');
                    });
                })
                ->when($billingOption && $billingOption != 'both', function ($query) use ($billingOption) {
                    $query->where('candidates.billing_type', $billingOption);
                })
                ->whereHas('timeSheets.details', function ($q) {
                    $q->whereNotNull('invoice_id');
                })
                ->groupBy(
                    'candidates.id',
                    'candidates.c_name',
                    'candidates.b_company_id',
                    'candidates.b_rate',
                    'candidates.billing_type'
                )
            )->editColumn('c_name', function ($row) {
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
                return $row->total_hours ?? 0;
            })
            ->addColumn('generated_hours', function ($row) {
                return $row->generated_hours;
            })
            ->editColumn('b_rate', function ($row) {
                return $row->b_rate ?? '';
            })
            ->addColumn('invoice_amount', function ($row) {
                return $row->invoice_amount ? number_format($row->invoice_amount, 2) : 0;
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
            // ->addColumn('mail', function ($row) {
            //     return '';
            // })
            // ->addColumn('mapping', function ($row) {
            //     return '';
            // })
            // ->addColumn('mapping_date', function ($row) {
            //     return '';
            // })
            ->filterColumn('vendor_company_name', function ($query, $keyword) {
                $query->whereHas('bCompany', function ($q) use ($keyword) {
                    $q->where('company_name', 'like', "%$keyword%");
                });
            })->orderColumn('vendor_company_name', function ($query, $order) {
                $query->join('b_companies', 'b_companies.id', '=', 'candidates.b_company_id')
                      ->orderBy('b_companies.company_name', $order);
            })->orderColumn('total_hours', function ($query, $order) use ($startDate, $endDate) {
                $whereClause = ($startDate && $endDate)
                    ? "AND time_sheet_details.date_of_day BETWEEN '$startDate' AND '$endDate'"
                    : '';

                $query->orderByRaw("(
                    SELECT COALESCE(SUM(hours), 0)
                    FROM time_sheet_details
                    JOIN time_sheets ON time_sheets.id = time_sheet_details.time_sheet_id
                    WHERE time_sheets.candidate_id = candidates.id
                    $whereClause
                ) $order");
            })->orderColumn('generated_hours', function ($query, $order) use ($startDate, $endDate) {
                $whereClause = ($startDate && $endDate)
                    ? "AND time_sheet_details.date_of_day BETWEEN '$startDate' AND '$endDate'"
                    : '';

                $query->orderByRaw("(
                    SELECT COALESCE(SUM(hours), 0)
                    FROM time_sheet_details
                    JOIN time_sheets ON time_sheets.id = time_sheet_details.time_sheet_id
                    WHERE time_sheet_details.invoice_id IS NOT NULL
                    AND time_sheets.candidate_id = candidates.id
                    $whereClause
                ) $order");
            })->orderColumn('invoice_amount', function ($query, $order) use ($startDate, $endDate) {
                $whereClause = ($startDate && $endDate)
                    ? "AND time_sheet_details.date_of_day BETWEEN '$startDate' AND '$endDate'"
                    : '';

                $query->orderByRaw("(
                    SELECT COALESCE(SUM(hours), 0) * candidates.b_rate
                    FROM time_sheet_details
                    JOIN time_sheets ON time_sheets.id = time_sheet_details.time_sheet_id
                    WHERE time_sheets.candidate_id = candidates.id
                    AND time_sheet_details.invoice_id IS NOT NULL
                    $whereClause
                ) $order");
            })->rawColumns(['c_name'])
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
