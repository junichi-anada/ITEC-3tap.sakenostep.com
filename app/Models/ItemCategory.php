<?php
/**
 * 商品カテゴリモデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemCategory extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'category_code',
        'site_id',
        'name',
        'priority',
        'is_published',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
