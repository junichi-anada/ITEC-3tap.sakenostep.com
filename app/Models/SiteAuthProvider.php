<?php
/**
 * サイト認証プロバイダーモデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteAuthProvider extends Model
{
    protected $fillable = [
        'site_id',
        'auth_provider_id',
        'is_enabled',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function authProvider()
    {
        return $this->belongsTo(AuthProvider::class);
    }
}
