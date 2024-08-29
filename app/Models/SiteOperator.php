<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteOperator extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'operator_id',
        'role_id',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function role()
    {
        return $this->belongsTo(SiteOperatorRole::class);
    }
}
