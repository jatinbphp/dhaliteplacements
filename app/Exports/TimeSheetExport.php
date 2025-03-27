<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\TimeSheet;
use App\Models\TimeSheetDetail;

class TimeSheetExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $timeSheets = TimeSheet::with('details')->get();
        $data = [];

        foreach ($timeSheets->groupBy('candidate_id') as $candidateId => $sheets) {
            foreach ($sheets as $sheet) {
                $weekMonth = date('Y-m', strtotime($sheet->week_end_date));
                $allSameMonth = $sheet->details->every(fn($d) => date('Y-m', strtotime($d->day_date)) === $weekMonth);

                $row = ['Candidate ID' => $candidateId];
                
                if ($allSameMonth) {
                    $row[$sheet->week_end_date] = $sheet->details->sum('hours');
                } else {
                    foreach ($sheet->details as $detail) {
                        $row[$detail->day_date] = $detail->hours;
                    }
                }
                
                $data[] = $row;
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['Candidate ID', 'Week Ending / Dates'];
    }
}