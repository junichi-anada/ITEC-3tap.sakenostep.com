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
use Carbon\Carbon;

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

    protected $casts = [
        'ordered_at' => 'datetime',
        'processed_at' => 'datetime',
        'exported_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        if ($this->processed_at) {
            return '処理済';
        }
        return '未処理';
    }

    /**
     * 注文番号を生成する
     * フォーマット: p + YYYYMMDDHHmm + _XX（XXは連番2桁）
     *
     * @return string
     */
    public static function generateOrderCode()
    {
        $now = Carbon::now();
        $datePrefix = 'p' . $now->format('YmdHi');

        // 同じ時間（分）の最後の注文番号を取得
        $lastOrder = self::where('order_code', 'LIKE', $datePrefix . '_%')
            ->orderBy('order_code', 'desc')
            ->first();

        if ($lastOrder) {
            // 最後の注文番号から連番を取得して+1
            $lastNumber = intval(substr($lastOrder->order_code, -2));
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            // その時間の最初の注文
            $newNumber = '01';
        }

        return $datePrefix . '_' . $newNumber;
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
