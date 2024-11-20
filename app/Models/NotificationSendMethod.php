<?php
/**
 * お知らせ送信方法モデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationSendMethod extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function receivers()
    {
        return $this->hasMany(NotificationReceiver::class, 'send_method_id');
    }
}
