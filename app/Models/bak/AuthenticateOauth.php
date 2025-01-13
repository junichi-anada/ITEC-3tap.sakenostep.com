<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OAuth認証モデル
 */
class AuthenticateOauth extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'auth_code',
        'site_id',
        'entity_type',
        'entity_id',
        'auth_provider_id',
        'token',
        'expires_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
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
