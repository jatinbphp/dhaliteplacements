<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BCompany;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'vendor_id',
        'amount',
        'amount_date',
        'ceo_reference',
    ];

    public function setAmountDateAttribute($value)
    {
        $this->attributes['amount_date'] = formateDate($value, 'm-d-Y', 'Y-m-d');
    }

    public function getAmountDateAttribute($value)
    {
        return formateDate($value, 'Y-m-d', 'm-d-Y');
    }

    public function vendor()
    {
        return $this->belongsTo(BCompany::class);
    }
}
