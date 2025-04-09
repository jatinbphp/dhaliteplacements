<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ManageVendorWiseData extends Component
{
    public $menu;
    public $breadcrumb;
    public $activeMenu;

    public function render()
    {
        $this->menu = "Vendor wise";
        $this->breadcrumb = [
            ['route' => 'dashboard', 'title' => 'Dashboard'],
        ];
        $this->activeMenu = 'Vendor wise';

        return view('livewire.manage-vendor-wise-data')->extends('layouts.app');
    }

    public function getVendorWiseData()
    {
        DB::statement("SET SQL_MODE=''");
        return DataTables::of(
            DB::table('candidates')
            ->select([
                'b_companies.company_name as b_vendor',
                DB::raw('b_companies.id as vendor_id'),
                DB::raw('COUNT(DISTINCT candidates.id) as total_candidate'),
                DB::raw('COUNT(DISTINCT CASE WHEN candidates.status = ' . Candidate::STATUS_ACTIVE . ' THEN candidates.id END) as active_status_candidate'),
                DB::raw('COUNT(DISTINCT CASE WHEN candidates.status = ' . Candidate::STATUS_PROJECT_END . ' THEN candidates.id END) as project_end_status_candidate'),
                DB::raw('SUM(time_sheet_details.hours) as total_hours'),
                DB::raw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) as hr_inv'),
                DB::raw('SUM(time_sheet_details.hours) - SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) as rem_hrs'),
                DB::raw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END * candidates.b_rate) as amt_invoiced'),
                DB::raw('MIN(time_sheet_details.date_of_day) as start_date'),
                DB::raw('MAX(time_sheet_details.date_of_day) as last_time'),

                // Safe fallback past_due amount logic per vendor (SUM only over overdue candidates)
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
                ) as past_due_hours'),
            ])
            ->leftJoin('b_companies', 'b_companies.id', '=', 'candidates.b_company_id')
            ->leftJoin('time_sheets', 'time_sheets.candidate_id', '=', 'candidates.id')
            ->leftJoin('time_sheet_details', 'time_sheet_details.time_sheet_id', '=', 'time_sheets.id')
            ->leftJoin('invoices', 'invoices.candidate_id', '=', 'candidates.id')
            ->groupBy('b_companies.id')
            )
            ->addColumn('candidates', function ($row) {
                return DB::table('candidates')
                    ->select([
                        'candidates.id',
                        'candidates.c_name',
                        'candidates.status',
                        'candidates.b_rate',
                        DB::raw('COUNT(DISTINCT candidates.id) as total_candidate'),
                        DB::raw('COUNT(DISTINCT CASE WHEN candidates.status = ' . Candidate::STATUS_ACTIVE . ' THEN candidates.id END) as active_status_candidate'),
                        DB::raw('COUNT(DISTINCT CASE WHEN candidates.status = ' . Candidate::STATUS_PROJECT_END . ' THEN candidates.id END) as project_end_status_candidate'),
                        DB::raw('SUM(time_sheet_details.hours) as total_hours'),
                        DB::raw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) as hr_inv'),
                        DB::raw('SUM(time_sheet_details.hours) - SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours ELSE 0 END) as rem_hrs'),
                        DB::raw('SUM(CASE WHEN time_sheet_details.invoice_id IS NOT NULL THEN time_sheet_details.hours * candidates.b_rate ELSE 0 END) as amt_invoiced'),
                        DB::raw('MIN(time_sheet_details.date_of_day) as start_date'),
                        DB::raw('MAX(time_sheet_details.date_of_day) as last_time'),
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
                    ->where('candidates.b_company_id', $row->vendor_id)
                    ->groupBy('candidates.id')
                    ->get()
                    ->toArray();
            })
            ->addColumn('vendor_name', function ($row) {
                return $row->b_vendor;
            })->addColumn('total_candidate', function ($row) {
                return $row->total_candidate;
            })->addColumn('active_status_candidate', function ($row) {
                return $row->active_status_candidate;
            })->addColumn('project_end_status_candidate', function ($row) {
                return $row->project_end_status_candidate;
            })->addColumn('rem_hrs', function ($row) {
                return $row->rem_hrs;
            })->addColumn('hr_due', function ($row) {
                return '';
            })->addColumn('post_due_hrs', function ($row) {
                return $row->past_due_hours;
            })->addColumn('amt_invoiced', function ($row) {
                return number_format($row->amt_invoiced, 2);
            })->addColumn('map_amount', function ($row) {
                return '';
            })->addColumn('over_due', function ($row) {
                return '';
            })->addColumn('past_due', function ($row) {
                return number_format($row->past_due, 2);
            })->orderColumn('vendor_name',function ($query, $order) {
                $query->orderBy('b_vendor', $order);
            })->orderColumn('total_candidate',function ($query, $order) {
                $query->orderBy('total_candidate', $order);
            })->orderColumn('active_status_candidate',function ($query, $order) {
                $query->orderBy('active_status_candidate', $order);
            })->orderColumn('project_end_status_candidate',function ($query, $order) {
                $query->orderBy('project_end_status_candidate', $order);
            })->orderColumn('rem_hrs',function ($query, $order) {
                $query->orderBy('rem_hrs', $order);
            })->orderColumn('hr_due',function ($query, $order) {
                // $query->orderBy('hr_due', $order);
            })->orderColumn('post_due_hrs',function ($query, $order) {
                $query->orderBy('past_due_hours', $order);
            })->orderColumn('amt_invoiced',function ($query, $order) {
                $query->orderBy('amt_invoiced', $order);
            })->orderColumn('map_amount',function ($query, $order) {
                // $query->orderBy('map_amount', $order);
            })->orderColumn('over_due',function ($query, $order) {
                // $query->orderBy('over_due', $order);
            })->orderColumn('past_due',function ($query, $order) {
                $query->orderBy('past_due', $order);
            })
            ->make(true);
    }
}
