<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LCompany extends Model
{
    use HasFactory;

    CONST STATUS_ACTIVE   = 1;
    CONST STATUS_INACTIVE = 0;

    CONST STATUS_ACTIVE_TEXT   = 'Active';
    CONST STATUS_INACTIVE_TEXT = 'Inactive';
    
    protected $fillable = [
        'user_id',
        'company_name',
        'address',
        'status'
    ];

    public static $status = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'In Active',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
