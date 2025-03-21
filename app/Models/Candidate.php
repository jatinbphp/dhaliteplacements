<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'candidate_type',
        'l_company_id',
        'l_rate',
        'l_aggrement',
        'c_id',
        'c_name',
        'visa_status_id',
        'visa_start_date',
        'visa_end_date',
        'id_start_date',
        'id_end_date',
        'city_state',
        'project',
        'c_rate',
        'candidate_note',
        'c_rate_note',
        'position',
        'client',
        'lapt_received',
        'pv_company_id',
        'b_company_id',
        'b_rate',
        'b_rate_note',
        'our_company_id',
        'b_aggrement',
        'c_aggrement',
        'marketer',
        'recruiter',
        'b_due_terms_id',
    ];

    protected $casts = [
        'visa_start_date' => 'date',
        'visa_end_date' => 'date',
        'id_start_date' => 'date',
        'id_end_date' => 'date',
    ];


    public function setVisaStartDateAttribute($value)
    {
        $this->attributes['visa_start_date'] = $this->formatDateForDatabase($value);
    }

    public function setVisaEndDateAttribute($value)
    {
        $this->attributes['visa_end_date'] = $this->formatDateForDatabase($value);
    }

    public function setIdStartDateAttribute($value)
    {
        $this->attributes['id_start_date'] = $this->formatDateForDatabase($value);
    }

    public function setIdEndDateAttribute($value)
    {
        $this->attributes['id_end_date'] = $this->formatDateForDatabase($value);
    }

    // === ACCESSORS: Convert YYYY-MM-DD -> MM/DD/YYYY when retrieving ===
    public function getVisaStartDateAttribute($value)
    {
        return $this->formatDateForFrontend($value);
    }

    public function getVisaEndDateAttribute($value)
    {
        return $this->formatDateForFrontend($value);
    }

    public function getIdStartDateAttribute($value)
    {
        return $this->formatDateForFrontend($value);
    }

    public function getIdEndDateAttribute($value)
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
}
