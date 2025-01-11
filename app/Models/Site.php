<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * サイトモデル
 */
class Site extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'site_code',
        'company_id',
        'url',
        'name',
        'description',
        'is_btob',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_btob' => 'boolean',
    ];

    /**
     * 会社との関連を取得
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * 認証との関連を取得
     *
     * @return HasMany
     */
    public function authenticates(): HasMany
    {
        return $this->hasMany(Authenticate::class);
    }

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
     * 使用可能サイトとの関連を取得
     *
     * @return HasMany
     */
    public function usableSites(): HasMany
    {
        return $this->hasMany(UsableSite::class);
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
