<?php

use Carbon\Carbon;
use App\Models\TimeSheet;
use App\Models\TimeSheetDetails;

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

if (!function_exists('isPreviousInvoiceDueOrDone')) {
    function isPreviousInvoiceDueOrDone($candidateId, $startDate, $endDate)
    {
        $data = TimeSheetDetails::whereHas('timeSheet', function ($query) use ($candidateId) {
                $query->where('candidate_id', $candidateId);
            })
            ->whereDate('date_of_day', '<', $startDate)
            ->whereNull('invoice_id');

        if($data->exists()){
            $earliestDate = formateDate($data->min('date_of_day'), 'Y-m-d', 'm-d-y');
            $latestDate = formateDate($data->max('date_of_day'), 'Y-m-d', 'm-d-y');

            $messageData['message'] = "Please add the previous invoice first.<br> Time Range:<b> $earliestDate to $latestDate.<b>";
            $messageData['status'] = 1;
            return $messageData;
        }

        $totalRecords = TimeSheetDetails::whereHas('timeSheet', function ($query) use ($candidateId) {
                $query->where('candidate_id', $candidateId);
            })
            ->whereBetween('date_of_day', [$startDate, $endDate])
            ->count();

        $invoicedRecords = TimeSheetDetails::whereHas('timeSheet', function ($query) use ($candidateId) {
                $query->where('candidate_id', $candidateId);
            })
            ->whereBetween('date_of_day', [$startDate, $endDate])
            ->whereNotNull('invoice_id')
            ->count();

        if($invoicedRecords){
            if ($totalRecords === $invoicedRecords) {
                $earliestDate = formateDate($startDate, 'Y-m-d', 'm-d-y');
                $latestDate = formateDate($endDate, 'Y-m-d', 'm-d-y');
                $messageData['message'] = "This range (<b>$earliestDate</b> to <b>$latestDate</b>) is already invoiced.";
                $messageData['status'] = 2;
                return $messageData;
            } else {
                 $missingStartDate = TimeSheetDetails::whereHas('timeSheet', function ($query) use ($candidateId) {
                        $query->where('candidate_id', $candidateId);
                    })
                    ->whereBetween('date_of_day', [$startDate, $endDate])
                    ->whereNull('invoice_id')
                    ->min('date_of_day'); 

                $missingStartDateFormatted = formateDate($missingStartDate, 'Y-m-d', 'm-d-y');
                $latestDate = formateDate($endDate, 'Y-m-d', 'm-d-y');
                $messageData['message'] = "Invoice missing from <b>$missingStartDateFormatted</b> to <b>$latestDate</b>.";
                $messageData['status'] = 3;
                return $messageData;
            }
        }
        return [];
    }
}
