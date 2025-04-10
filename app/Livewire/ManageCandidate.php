<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidate;
use App\Models\TimeSheetDetails;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManageCandidate extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $statusWiseHoursNotInvoicedCandidateData = [];

    public function render()
    {
        $this->menu = "Candidate";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Candidate';
        $this->prepareStatusWiseHoursNotInvoicedCandidateData();

        return view('livewire.manage-candidate')->extends('layouts.app');
    }

    public function getCandidateData()
    {
        $candidateType = Candidate::candidateType;
        DB::statement("SET SQL_MODE=''");
        $mappedSubquery = DB::table('payment_mappings')
            ->select('invoices.candidate_id', DB::raw('SUM(payment_mappings.amount) as mapped_rec_amt'))
            ->join('invoices', 'invoices.id', '=', 'payment_mappings.invoice_id')
            ->groupBy('invoices.candidate_id');

        return DataTables::of(
            Candidate::select([
                'candidates.*',
                DB::raw('SUM(time_sheet_details.hours) as total_hours'),
                DB::raw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) as hr_inv'),
                DB::raw('SUM(time_sheet_details.hours) - SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) as rem_hrs'),
                DB::raw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) * invoices.rate as amt_inv'),
                DB::raw('MIN(time_sheet_details.date_of_day) as start_date'),
                DB::raw('MAX(time_sheet_details.date_of_day) as last_time'),
                DB::raw('GROUP_CONCAT(DISTINCT time_sheet_details.invoice_id) as invoice_ids'),
                DB::raw('COALESCE(mapped_pm.mapped_rec_amt, 0) as mapped_rec_amt'),
                DB::raw('(SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) * invoices.rate - COALESCE(mapped_pm.mapped_rec_amt, 0)) as due_rec_amt'),
                DB::raw('ROUND((SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) * invoices.rate - COALESCE(mapped_pm.mapped_rec_amt, 0)) / NULLIF(invoices.rate, 0), 2) as hrs_due'),

            ])
            ->leftJoin('time_sheets', 'time_sheets.candidate_id', '=', 'candidates.id')
            ->leftJoin('time_sheet_details', 'time_sheet_details.time_sheet_id', '=', 'time_sheets.id')
            ->leftJoin('invoices', 'invoices.id', '=', 'time_sheet_details.invoice_id')
            ->leftJoinSub($mappedSubquery, 'mapped_pm', function ($join) {
                $join->on('mapped_pm.candidate_id', '=', 'candidates.id');
            })
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
                return number_format($row->amt_inv  ?? 0, 2);
            })->addColumn('mapped_rec_amt', function ($row) {
                return number_format($row->mapped_rec_amt ?? 0, 2);
            })->addColumn('due_rec_amt', function ($row) {
                return number_format($row->due_rec_amt ?? 0, 2);
            })->addColumn('hrs_due', function ($row) {
                return $row->hrs_due;
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
                $query->havingRaw('(SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) * invoices.rate) like ?', ["%{$keyword}%"]);
            })->orderColumn('amt_inv', function ($query, $order) {
                $query->orderByRaw('(SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) * invoices.rate) ' . $order);
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
            ->orderColumn('mapped_rec_amt',function ($query, $order) {
                $query->orderBy('mapped_rec_amt', $order);
            })
            ->orderColumn('due_rec_amt',function ($query, $order) {
                $query->orderBy('due_rec_amt', $order);
            })
            ->orderColumn('hrs_due',function ($query, $order) {
                $query->orderBy('hrs_due', $order);
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function prepareStatusWiseHoursNotInvoicedCandidateData()
    {
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $data = TimeSheetDetails::query()
            ->selectRaw("
                candidates.status,
                CASE 
                    WHEN time_sheet_details.date_of_day < ? THEN 'before'
                    ELSE DATE_FORMAT(time_sheet_details.date_of_day, '%Y-%m')
                END as month,
                SUM(time_sheet_details.hours) as total_hours
            ", [$startDate])
            ->join('time_sheets', 'time_sheets.id', '=', 'time_sheet_details.time_sheet_id')
            ->join('candidates', 'candidates.id', '=', 'time_sheets.candidate_id')
            ->whereNull('time_sheet_details.invoice_id')
            ->groupBy('candidates.status', 'month')
            ->orderBy('month')
            ->get();

        $formattedDate = $this->formateData($data);
        $this->statusWiseHoursNotInvoicedCandidateData = $this->prepareNotInvoicedTableData($formattedDate);
    }

    public function formateData($data)
    {
        $statuses = array_keys(Candidate::candidateStatus);
        $months = collect(['before'])->merge(
            collect(range(0, 8))->map(fn ($i) => Carbon::now()->subMonths(8 - $i)->format('Y-m'))
        );

        $formatted = [];

        foreach ($statuses as $status) {
            foreach ($months as $month) {
                $formatted[$status][$month] = 0;
            }
        }

        foreach ($data as $row) {
            $formatted[$row->status][$row->month] = $row->total_hours;
        }

        foreach ($formatted as $status => &$monthData) {
            $monthData = collect($months)->mapWithKeys(fn ($m) => [$m => $monthData[$m]])->toArray();
        }

        return $formatted;
    }

    public function prepareNotInvoicedTableData($rawData)
    {
        $statusLabels = Candidate::candidateStatus;

        $months = collect($rawData)->first()
            ? array_keys(collect($rawData)->first())
            : [];

        $rows = [];
        $statusTotals = array_fill_keys(array_keys($statusLabels), 0);
        $grandTotal = 0;

        foreach ($months as $month) {
            $row = [
                'label' => $month === 'before'
                    ? 'Before'
                    : Carbon::parse($month . '-01')->format('M') .
                        ($month === now()->format('Y-m') ? ' (Current Month)' : ''),
                'values' => [],
                'total' => 0,
            ];

            foreach ($statusLabels as $status => $label) {
                $value = $rawData[$status][$month] ?? 0;
                $row['values'][$status] = $value;
                $row['total'] += $value;
                $statusTotals[$status] += $value;
            }

            $grandTotal += $row['total'];
            $rows[] = $row;
        }

        return [
            'rows' => $rows,
            'statusLabels' => $statusLabels,
            'statusTotals' => $statusTotals,
            'grandTotal' => $grandTotal,
        ];
    }
}
