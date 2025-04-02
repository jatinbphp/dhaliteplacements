<?php

namespace App\Exports;

use App\Models\TimeSheet;
use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class TimeSheetExport implements FromArray, WithHeadings
{

    protected $candidateWiseTimeSheetData = [];
    protected $datesArray = [];
    protected $headings = [];
    protected $selectedCandidateIds;
    protected $dateRange;

    public function __construct($selectedCandidateIds, $dateRange)
    {
        $this->selectedCandidateIds = $selectedCandidateIds;
        $this->dateRange = $dateRange;
    }

    public function headings(): array
    {
        return array_merge(['Candidate Name', 'Visa', 'Candidate Type', 'Client'], $this->generateHeadings(), ['Total']); 
    }

    public function array(): array
    {
        $headings = $this->generateHeadings();
        $this->candidateWiseTimeSheetData = getCandidateWiseTimeSheetData($this->selectedCandidateIds);
        $candidates = Candidate::when(!empty($this->selectedCandidateIds), function ($query) {
            $query->whereIn('id', $this->selectedCandidateIds);
        })->get();

        $data = [];

        foreach ($candidates as $userData) {
            $row['Candidate Name'] = $userData->c_name ?? ''; 
            $row['Visa'] = $userData->visa->name ?? ''; 
            $row['Candidate Type'] = $userData->candidate_type ?? ''; 
            $row['Client'] = $userData->client ?? ''; 
            $candidateId = $userData->id ?? 0;
            $totalHours = 0;

            foreach ($this->datesArray as $date) {
                if (strpos($date, ' - ') !== false) {
                    [$start, $end] = explode(' - ', $date);
                    $row[$date] = sumHoursForDateRange($this->candidateWiseTimeSheetData, $candidateId, $start, $end);
                } else {
                    $row[$date] = $this->candidateWiseTimeSheetData[$candidateId][$date] ?? "0.00";
                }

                $totalHours += (float) $row[$date];
            }

            $row['Total'] = number_format($totalHours, 2);
            $data[] = $row;
        }
        return $data;
    }

    private function generateHeadings()
    {
        if($this->headings){
            return $this->headings;
        }

        if (!empty($this->dateRange)) {
            [$startDate, $endDate] = explode(" to ", $this->dateRange);
            $startDate = Carbon::parse(formateDate($startDate));
            $endDate = Carbon::parse(formateDate($endDate));
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        $range = getDateRangeArray($startDate, $endDate);

        $this->headings = $range['headings'] ?? [];
        $this->datesArray = $range['datesArray'] ?? [];
        return $this->headings;
    }
}
