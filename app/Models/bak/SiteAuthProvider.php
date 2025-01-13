<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * サイト認証プロバイダーモデル
 */
class SiteAuthProvider extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'site_id',
        'auth_provider_id',
        'is_enabled',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * サイトとの関連を取得
     *
     * @return BelongsTo
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * 認証プロバイダーとの関連を取得
     *
     * @return BelongsTo
     */
    public function authProvider(): BelongsTo
    {
        return $this->belongsTo(AuthProvider::class);
    }
}
