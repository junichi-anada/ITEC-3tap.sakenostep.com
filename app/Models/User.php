<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * ユーザーモデル
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_code',
        'site_id',
        'last_name',
        'first_name',
        'last_name_kana',
        'first_name_kana',
        'postal_code',
        'address',
        'phone',
        'phone2',
        'fax',
        'birthday',
        'gender',
        'is_active',
        'last_login_at',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthday' => 'date',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
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
     * 注文との関連を取得
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * お気に入り商品との関連を取得
     *
     * @return HasMany
     */
    public function favoriteItems(): HasMany
    {
        return $this->hasMany(FavoriteItem::class);
    }

    /**
     * メッセージログとの関連を取得
     *
     * @return HasMany
     */
    public function messageLogs(): HasMany
    {
        return $this->hasMany(MessageLog::class);
    }

    /**
     * フルネームを取得
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->last_name} {$this->first_name}";
    }

    /**
     * フルネーム（カナ）を取得
     *
     * @return string|null
     */
    public function getFullNameKanaAttribute(): ?string
    {
        if ($this->last_name_kana === null || $this->first_name_kana === null) {
            return null;
        }
        return "{$this->last_name_kana} {$this->first_name_kana}";
    }
}
