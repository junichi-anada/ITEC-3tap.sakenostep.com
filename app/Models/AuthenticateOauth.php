<?php
/**
 * OAuth認証モデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthenticateOauth extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'auth_code',
        'site_id',
        'entity_type',
        'entity_id',
        'auth_provider_id',
        'token',
        'expires_at',
    ];

    protected $dates = ['expires_at', 'created_at', 'updated_at', 'deleted_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function authProvider()
    {
        return $this->belongsTo(AuthProvider::class);
    }

    public function authenticateWithToken($token)
    {
        return self::where('token', $token)->first();
    }
}
