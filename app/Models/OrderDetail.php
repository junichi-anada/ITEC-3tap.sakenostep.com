<?php
/**
 * 注文詳細モデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

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

    protected $dates = ['processed_at', 'created_at', 'updated_at', 'deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
