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
        $this->prepareCandidateWiseTimeSheetData();
        $candidates = Candidate::when(!empty($this->selectedCandidateIds), function ($query) {
            $query->where('id', $this->selectedCandidateIds);
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
                    $row[$date] = $this->sumHoursForDateRange($candidateId, $start, $end);
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



    private function sumHoursForDateRange($candidateId, $start, $end) {
        $startDate = Carbon::createFromFormat('m-d-Y', $start);
        $endDate = Carbon::createFromFormat('m-d-Y', $end);
        $total = 0;

        $candidateData = $this->candidateWiseTimeSheetData[$candidateId] ?? [];

        while ($startDate->lte($endDate)) {
            $formattedDate = $startDate->format('m-d-Y');
            if (isset($candidateData[$formattedDate])) {
                $total += (float) $candidateData[$formattedDate];
            }
            $startDate->addDay();
        }

        return number_format($total, 2);
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

        $headings = [];
        while ($startDate->lte($endDate)) {
            $weekEndDate = $startDate->copy()->endOfWeek(Carbon::SUNDAY)->startOfDay();
            if ($startDate->isMonday() && $endDate->gte($weekEndDate) && $startDate->isSameMonth($weekEndDate)) {
                $headings[] = 'Week End - ' . $weekEndDate->format('m-d-Y');
                $this->datesArray[] = $startDate->format('m-d-Y') . ' - ' . $weekEndDate->format('m-d-Y');

                $startDate = $weekEndDate; // Move to the week's end
            } else {
                $headings[] = $startDate->format('m-d-Y');
                $this->datesArray[] = $startDate->format('m-d-Y');
            }

            $startDate->addDay();
        }

        $this->headings = $headings;
        \Log::info($headings);
        return $this->headings;
    }

    protected function prepareCandidateWiseTimeSheetData()
    {
        $timeSheetData = TimeSheet::with('details')
            ->when(!empty($this->selectedCandidateIds), function ($query) {
                $query->whereIn('candidate_id', $this->selectedCandidateIds);
            })->get()
            ->toArray();

        foreach($timeSheetData as $data){
            $candidateId = $data['candidate_id'] ?? '';
            $timeSheetDetails = $data['details'] ?? [];

            if(!$candidateId || !$timeSheetDetails || !count($timeSheetDetails)){
                continue;
            }

            $hoursDetails = [];
            foreach ($timeSheetDetails as $detail) {
                $dayOfDate = $detail['date_of_day'] ?? '';
                $hours = $detail['hours'] ?? '';

                $this->candidateWiseTimeSheetData[$candidateId][$dayOfDate] = $hours;
            }
        }
    }
}
