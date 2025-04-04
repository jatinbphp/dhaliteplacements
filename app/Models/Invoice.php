<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'candidate_id',
        'generated_date',
        'from_date',
        'to_date',
    ];
}
