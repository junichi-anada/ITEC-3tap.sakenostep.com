<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsableSite extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'site_id',
        'shared_login_allowed',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
