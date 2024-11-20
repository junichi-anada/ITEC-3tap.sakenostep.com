<?php
/**
 * 商品モデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;

    use SoftDeletes;

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

    protected $dates = ['published_at', 'created_at', 'updated_at', 'deleted_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function favoriteItems()
    {
        return $this->hasMany(FavoriteItem::class);
    }
}
