<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'operator_code',
        'company_id',
        'name',
        'operator_rank_id',
    ];

    public function authenticate()
    {
        return $this->morphOne(Authenticate::class, 'entity');
    }
} 