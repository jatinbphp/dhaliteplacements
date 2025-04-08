<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Candidate;
use App\Models\TimeSheet;

class TimeSheetDetails extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'time_sheet_id',
        'date_of_day',
        'day_name',
        'hours',
        'invoice_id',
    ];

    public function setDateOfDayAttribute($value)
    {
        $this->attributes['date_of_day'] = $this->formatDateForDatabase($value);
    }

    public function getDateOfDayAttribute($value)
    {
        return $this->formatDateForFrontend($value);
    }

    private function formatDateForDatabase($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d'); // Convert MM-DD-YYYY to MySQL format
        } catch (\Exception $e) {
            \Log::error("Invalid date format: " . $value); // Log incorrect formats for debugging
            return null;
        }
    }

    private function formatDateForFrontend($value)
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)->format('m-d-Y'); // Convert MySQL YYYY-MM-DD to MM-DD-YYYY
    }

    public function timeSheet()
    {
        return $this->belongsTo(TimeSheet::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
