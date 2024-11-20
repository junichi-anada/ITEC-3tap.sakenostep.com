<?php
/**
 * お知らせカテゴリモデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationCategory extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function parent()
    {
        return $this->belongsTo(NotificationCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NotificationCategory::class, 'parent_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'category_id');
    }
}
