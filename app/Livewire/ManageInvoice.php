<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidate;
use App\Models\TimeSheetDetails;
use App\Models\Invoice;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mpdf\Mpdf;

class ManageInvoice extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;
    public $dateRange;
    public $billingOptions = [];
    public $billingTypeId = '';
    public $invoiceData = [];

    protected $listeners = ['openInvoiceModel'];

    public function mount()
    {
        $this->menu = "Invoice";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Invoice';
        $this->billingOptions = ['both' => 'Both'] + Candidate::billingOptions;
        $this->billingTypeId = 'both';
    }

    public function render()
    {
        return view('livewire.manage-invoice')->extends('layouts.app');
    }

    public function getInvoiceData(Request $request)
    {
        $this->billingOptions = ['both' => 'Both'] + Candidate::billingOptions;
        if ($request->dateRange && !empty($request->dateRange)) {
            if (strpos($request->dateRange, " to ") !== false) {
                [$startDate, $endDate] = explode(" to ", $request->dateRange);
                $startDate = formateDate($startDate); // Ensure this formats the date correctly.
                $endDate = formateDate($endDate);     // Ensure this formats the date correctly.
            }
        }

        if (!isset($startDate) || !isset($endDate)) {
            return DataTables::of(collect([]))->make(true);
        }

        $billingOption = 'both';
        if($request->billingOption && !empty($request->billingOption)){
            $billingOption = $request->billingOption;
        }

        $candidatesQuery = Candidate::select('id', 'c_name', 'b_company_id', 'billing_type')
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
                'candidates.billing_type',
            );

        $totalCandidates = (clone $candidatesQuery)->get()->count();

        return DataTables::of($candidatesQuery)
            ->with(['totalCandidates' => $totalCandidates])
            ->editColumn('c_name', function ($row) {
                $billingType = $this->billingOptions[$row->billing_type] ?? '';
                $candidateName = $row->c_name ?? '';

                return '<div>
                            <span>' . $candidateName . '</span><br>
                            <span><b>' . $billingType . '</b></span>
                        </div>';
            })
            ->addColumn('vendor_company_name', function ($row) {
                return $row->bCompany->company_name ?? '';
            })
            ->addColumn('total_hours', function ($row) {
                return $row->timeSheets->flatMap(function ($sheet) {
                    return $sheet->details->pluck('hours');
                })->sum() ?? 0;
            })
            ->editColumn('billing_type', function ($row) {
                return $this->billingOptions[$row->billing_type] ?? '';
            })
            ->addColumn('generated_hours', function ($row) {
                return $row->timeSheets->flatMap(function ($sheet) {
                    return $sheet->details->pluck('hours');
                })->sum();
            })->addColumn('time_from_to', function ($row) {
                return '';
            })
            ->addColumn('invoice_id', function ($row) {
                return optional($row->timeSheets->first())->invoice_id ?? '';
            })
            ->addColumn('generated_date', function ($row) {
                return optional($row->timeSheets->first())->generated_date ?? '';
            })
            ->addColumn('actions', function ($row) {
                return view('livewire.manage-invoice.actions', ['data' => $row]);
            })
            ->addColumn('status', function ($row) use ($startDate, $endDate) {
                $statusData = isPreviousInvoiceDueOrDone($row->id, $startDate, $endDate);
                return $statusData['status'] ?? 0;
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

    public function generateInvoice($id)
    {
        $startDate = '';
        $endDate = '';
        if ($this->dateRange && !empty($this->dateRange)) {
            if (strpos($this->dateRange, " to ") !== false) {
                [$startDate, $endDate] = explode(" to ", $this->dateRange);
                $startDate = formateDate($startDate);
                $endDate = formateDate($endDate);
                $formattedStartDate = Carbon::parse($startDate)->format('M jS, Y');
                $formattedEndDate = Carbon::parse($endDate)->format('M jS, Y');
            }
        }

        $messageData = isPreviousInvoiceDueOrDone($id, $startDate, $endDate);
        if($messageData){
            $this->dispatch('swal:warning', $messageData['message'] ?? 'Something went wrong!');
            return;
        }

        $candidate = Candidate::where('id', $id)->with('lCompany', 'bCompany', 'ourCompany')->first();
        $timeSheetData = $this->getTimeSheetData($id, $startDate, $endDate);
        $invoiceId = Invoice::max('id') + 1;
        $today = Carbon::now()->format('M jS, Y');
        $this->invoiceData = [
            'invoiceId'     => $invoiceId,
            'startDate'     => $formattedStartDate,
            'endDate'       => $formattedEndDate,
            'today'         => $today,
            'candidate'     => $candidate,
            'timeSheetData' => $timeSheetData,
            'pdf'           => 0,
        ];
        $htmlContent = view('livewire.manage-invoice.invoice-data', $this->invoiceData)->render();
        $this->dispatch('openInvoiceModel', ['htmlContent' => $htmlContent]);
    }

    public function getTimeSheetData($id, $startDate, $endDate)
    {
        $candidateTimeData = getCandidateWiseTimeSheetData([$id]);
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $rangeDates = getDateRangeArray($startDate, $endDate);
        $datesArray = $rangeDates['datesArray'] ?? [];

        $row = [];
        foreach ($datesArray as $date) {
            if (strpos($date, ' - ') !== false) {
                [$start, $end] = explode(' - ', $date);
                $formattedEndDate = Carbon::createFromFormat('m-d-Y', $end)->format('M jS, Y');
                $row['Week Ending - '.$formattedEndDate] = sumHoursForDateRange($candidateTimeData, $id, $start, $end);
            } else {
                $formattedDate = Carbon::createFromFormat('m-d-Y', $date)->format('M jS, Y');
                $row[$formattedDate] = $candidateTimeData[$id][$date] ?? "0.00";
            }
        }
        return $row;
    }

    public function generatePdf()
    {

        $this->invoiceData['pdf'] = 1;
        $htmlContent = view('livewire.manage-invoice.invoice-data', $this->invoiceData)->render();

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
        </head>
        <body>' . $htmlContent . '</body>
        </html>';

        // Fix encoding issues
        $mpdf = new Mpdf(['mode' => 'utf-8', 'default_font' => 'Arial', 'useAdobeCJK' => true]);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        $fileName = $this->invoiceData['candidate']['c_name'] ?? 'invoice';

        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, $fileName.'.pdf'); 
    }

    public function saveInvoice()
    {
        try {
            $startDate = '';
            $endDate = '';

            if ($this->dateRange && !empty($this->dateRange)) {
                if (strpos($this->dateRange, " to ") !== false) {
                    [$startDate, $endDate] = explode(" to ", $this->dateRange);
                    $startDate = formateDate($startDate);
                    $endDate = formateDate($endDate);
                }
            }

            $candidateId = $this->invoiceData['candidate']['id'] ?? 0;

            if (!$startDate || !$endDate || !$candidateId) {
                $this->dispatch('swal:error', 'Missing required fields! Please check date range and candidate.');
                return;
            }

            $invoice = Invoice::create([
                'candidate_id' => $candidateId,
                'generated_date' => Carbon::now()->format('Y-m-d'),
                'from_date' => $startDate,
                'to_date' => $endDate,
            ]);

            TimeSheetDetails::whereHas('timeSheet', function ($query) use ($candidateId) {
                $query->where('candidate_id', $candidateId);
            })
            ->whereBetween('date_of_day', [$startDate, $endDate])
            ->update(['invoice_id' => $invoice->id]);

            $this->dispatch('swal:success', 'Invoice created successfully and linked to timesheets!');
            $this->dispatch('closeModal');
            $this->dispatch('refreshDataTable');
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', 'Something went wrong! ' . $e->getMessage());
        }
    }
}
