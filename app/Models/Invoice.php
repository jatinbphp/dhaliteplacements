<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Candidate;
use App\Models\TimeSheetDetails;

class Invoice extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'candidate_id',
        'generated_date',
        'from_date',
        'to_date',
        'rate',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function timeSheetDetails()
    {
        return $this->hasMany(TimeSheetDetails::class, 'invoice_id', 'id');
    }

    public function paymentMappings()
    {
        return $this->hasMany(PaymentMapping::class, 'invoice_id');
    }

    public function getReceivedAmountAttribute()
    {
        return $this->paymentMappings->sum('amount');
    }
}
