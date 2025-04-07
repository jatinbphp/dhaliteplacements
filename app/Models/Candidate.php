<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Models\Visa;
use App\Models\BCompany;
use App\Models\LCompany;
use App\Models\OurCompany;

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
        'status',
        'billing_type',
    ];

    protected $casts = [
        'visa_start_date' => 'date',
        'visa_end_date' => 'date',
        'id_start_date' => 'date',
        'id_end_date' => 'date',
    ];

    const STATUS_ACTIVE      = 1;
    const STATUS_PROJECT_END = 2;
    const STATUS_CLEAR       = 3;
    const STATUS_NOT_CLEAR   = 4;

    const STATUS_ACTIVE_TEXT      = 'Active';
    const STATUS_PROJECT_END_TEXT = 'Project End';
    const STATUS_CLEAR_TEXT       = 'Clear';
    const STATUS_NOT_CLEAR_TEXT   = 'Not Clear';

    const BILLING_MONTHLY     = 1;
    const BILLING_NOT_MONTHLY = 2;

    const BILLING_MONTHLY_TEXT     = 'Monthly';
    const BILLING_NOT_MONTHLY_TEXT = 'Not Monthly';
    
    const candidateType = [
        'w2'     => 'W2',
        'w2_c2c' => 'W2 & c2c',
        'c2c'    => 'C2C',
    ];

    const candidateStatus = [
        self::STATUS_ACTIVE      => self::STATUS_ACTIVE_TEXT,
        self::STATUS_PROJECT_END => self::STATUS_PROJECT_END_TEXT,
        self::STATUS_CLEAR       => self::STATUS_CLEAR_TEXT,
        self::STATUS_NOT_CLEAR   => self::STATUS_NOT_CLEAR_TEXT,
    ];

    const billingOptions = [
        self::BILLING_MONTHLY      => self::BILLING_MONTHLY_TEXT,
        self::BILLING_NOT_MONTHLY  => self::BILLING_NOT_MONTHLY_TEXT,
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
            return Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::error("Invalid date format: " . $value);
            return null;
        }
    }

    private function formatDateForFrontend($value)
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)->format('m-d-Y');
    }

    public function visa()
    {
         return $this->belongsTo(Visa::class, 'visa_status_id');
    }

    public function bCompany()
    {
        return $this->belongsTo(BCompany::class, 'b_company_id');
    }

    public function lCompany()
    {
        return $this->belongsTo(LCompany::class, 'l_company_id');
    }

    public function ourCompany()
    {
        return $this->belongsTo(OurCompany::class, 'our_company_id');
    }

    public function timeSheets()
    {
        return $this->hasMany(TimeSheet::class);
    }

    public function timeSheet()
    {
        return $this->belongsTo(TimeSheet::class);
    }
}
