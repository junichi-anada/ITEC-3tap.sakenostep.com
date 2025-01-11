<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 認証プロバイダーモデル
 */
class AuthProvider extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_code',
        'name',
        'description',
        'is_enable',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_enable' => 'boolean',
    ];

    /**
     * OAuth認証との関連を取得
     *
     * @return HasMany
     */
    public function authenticateOauths(): HasMany
    {
        return $this->hasMany(AuthenticateOauth::class);
    }

    /**
     * サイト認証プロバイダーとの関連を取得
     *
     * @return HasMany
     */
    public function siteAuthProviders(): HasMany
    {
        return $this->hasMany(SiteAuthProvider::class);
    }
}
