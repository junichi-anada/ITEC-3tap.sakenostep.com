<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 注文明細モデル
 */
class OrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'detail_code',
        'order_id',
        'item_id',
        'volume',
        'unit_price',
        'unit_name',
        'price',
        'tax',
        'processed_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'volume' => 'integer',
        'unit_price' => 'decimal:2',
        'price' => 'decimal:2',
        'tax' => 'decimal:0',
        'processed_at' => 'datetime',
    ];

    /**
     * 注文との関連を取得
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 商品との関連を取得
     *
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
