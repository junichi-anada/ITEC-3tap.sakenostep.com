<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthProvider extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'provider_code',
        'name',
        'description',
        'is_enable',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function siteAuthProviders()
    {
        return $this->hasMany(SiteAuthProvider::class);
    }
}
