<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 商品モデル
 */
class Item extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'item_code',
        'site_id',
        'category_id',
        'maker_name',
        'name',
        'description',
        'unit_price',
        'unit_id',
        'from_source',
        'is_recommended',
        'published_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_recommended' => 'boolean',
        'published_at' => 'datetime',
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
     * 商品カテゴリとの関連を取得
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    /**
     * 商品単位との関連を取得
     *
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }
}
