<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use App\Models\Invoice;

class PaymentMapping extends Model
{
    protected $fillable = [
        'payment_id',
        'invoice_id',
        'amount',
        'mapping_id',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
