<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 注文モデル
 */
class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'order_code',
        'site_id',
        'user_id',
        'total_price',
        'tax',
        'postage',
        'ordered_at',
        'processed_at',
        'exported_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        'tax' => 'decimal:0',
        'postage' => 'decimal:0',
        'ordered_at' => 'datetime',
        'processed_at' => 'datetime',
        'exported_at' => 'datetime',
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
     * ユーザーとの関連を取得
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 注文明細との関連を取得
     *
     * @return HasMany
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
