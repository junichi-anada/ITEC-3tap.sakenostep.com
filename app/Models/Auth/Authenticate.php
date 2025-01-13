<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Authenticate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'auth_code',
        'site_id',
        'entity_type',
        'entity_id',
        'login_code',
        'password',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
    ];

    public function entity()
    {
        return $this->morphTo();
    }
} 