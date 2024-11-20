<?php
/**
 * 注文基本モデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;

    use SoftDeletes;

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

    protected $dates = ['ordered_at', 'processed_at', 'exported_at', 'created_at', 'updated_at', 'deleted_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
