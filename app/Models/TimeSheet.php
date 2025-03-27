<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Candidate;
use App\Models\TimeSheetDetails;

class TimeSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'user_id',
        'week_end_date',
    ];

    public function setWeekEndDateAttribute($value)
    {
        $this->attributes['week_end_date'] = $this->formatDateForDatabase($value);
    }

    public function getWeekEndDateAttribute($value)
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

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function details()
    {
        return $this->hasMany(TimeSheetDetails::class, 'time_sheet_id');
    }
}
