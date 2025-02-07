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
     * フォーマット: [年(2桁)][月(2桁)][日(2桁)]+[ランダム数字(6桁)]
     * 注文番号の重複がないことを保証します
     *
     * @return string
     */
    public static function generateOrderCode()
    {
        $now = Carbon::now();
        $datePrefix = $now->format('ymd'); // 年(2桁)月日

        do {
            // 6桁のランダム数字を生成
            $randomNumber = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // 注文番号を組み立て
            $orderCode = $datePrefix . $randomNumber;

            // 重複チェック
            $exists = self::where('order_code', $orderCode)->exists();
        } while ($exists); // 重複がある場合は再生成

        return $orderCode;
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

    /**
     * 今月の注文のスコープ
     */
    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
                     ->whereMonth('created_at', now()->month);
    }

    /**
     * 本日の注文のスコープ
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    /**
     * CSV未書出の注文のスコープ
     */
    public function scopeNotExported($query)
    {
        return $query->whereNull('exported_at');
    }

    /**
     * CSV書出済の注文のスコ�
     */
    public function scopeExported($query)
    {
        return $query->whereNotNull('exported_at');
    }
}
