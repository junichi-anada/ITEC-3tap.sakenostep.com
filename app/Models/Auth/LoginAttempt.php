<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'login_id',
        'ip_address',
        'is_success',
        'failure_reason',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'created_at' => 'datetime',
    ];
} 