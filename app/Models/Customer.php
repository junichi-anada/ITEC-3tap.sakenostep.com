<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Customer extends Model
{
    /**
     * 日付として扱うカラム
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'first_login_at',
        'last_login_at',
        'deleted_at'
    ];

    public function getFormattedFirstLoginAttribute()
    {
        return $this->first_login_at ? $this->first_login_at->format('Y-m-d') : '';
    }
} 