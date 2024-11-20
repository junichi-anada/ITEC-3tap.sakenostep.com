<?php
/**
 * お気に入り商品モデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FavoriteItem extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'item_id',
        'site_id'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * アイテムとのリレーション
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * サイトとのリレーション
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

}
