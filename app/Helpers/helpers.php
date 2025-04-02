<?php

use Carbon\Carbon;
use App\Models\TimeSheet;

if (!function_exists('formateDate')) {
    function formateDate($date, $fromFormat = 'm-d-Y', $toFormat = 'Y-m-d')
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::createFromFormat($fromFormat, $date)->format($toFormat);
        } catch (\Exception $e) {
            return null;
        }
    }
}


if (!function_exists('getDateRangeArray')) {
    function getDateRangeArray($startDate, $endDate)
    {
        $headings = [];
        $datesArray = [];
        while ($startDate->lte($endDate)) {
            $weekEndDate = $startDate->copy()->endOfWeek(Carbon::SUNDAY)->startOfDay();
            if ($startDate->isMonday() && $endDate->gte($weekEndDate) && $startDate->isSameMonth($weekEndDate)) {
                $headings[] = 'Week End - ' . $weekEndDate->format('m-d-Y');
                $datesArray[] = $startDate->format('m-d-Y') . ' - ' . $weekEndDate->format('m-d-Y');

                $startDate = $weekEndDate; // Move to the week's end
            } else {
                $headings[] = $startDate->format('m-d-Y');
                $datesArray[] = $startDate->format('m-d-Y');
            }

            $startDate->addDay();
        }

        return ['headings' => $headings, 'datesArray' => $datesArray];
    }
}

if (!function_exists('getCandidateWiseTimeSheetData')) {
    function getCandidateWiseTimeSheetData($candidateIds = [])
    {
        $candidateWiseTimeSheetData = [];
         $timeSheetData = TimeSheet::with('details')
            ->when(!empty($candidateIds), function ($query) use ($candidateIds) {
                $query->whereIn('candidate_id', $candidateIds);
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

                $candidateWiseTimeSheetData[$candidateId][$dayOfDate] = $hours;
            }
        }

        return $candidateWiseTimeSheetData;
    }
}

if (!function_exists('sumHoursForDateRange')) {
    function sumHoursForDateRange($data, $candidateId, $start, $end)
    {
        $startDate = Carbon::createFromFormat('m-d-Y', $start);
        $endDate = Carbon::createFromFormat('m-d-Y', $end);
        $total = 0;

        $candidateData = $data[$candidateId] ?? [];

        while ($startDate->lte($endDate)) {
            $formattedDate = $startDate->format('m-d-Y');
            if (isset($candidateData[$formattedDate])) {
                $total += (float) $candidateData[$formattedDate];
            }
            $startDate->addDay();
        }

        return number_format($total, 2);
    }
}
