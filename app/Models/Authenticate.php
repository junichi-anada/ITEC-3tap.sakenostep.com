<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class Authenticate extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'auth_code',
        'site_id',
        'entity_type',
        'entity_id',
        'login_code',
        'password',
        'expires_at',
    ];

    protected $dates = ['expires_at', 'created_at', 'updated_at', 'deleted_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function authenticateUser($loginCode, $password)
    {
        $auth = self::where('login_code', $loginCode)->first();

        if ($auth && Hash::check($password, $auth->password)) {
            return $auth;
        }

        return null;
    }
}
