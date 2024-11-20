<?php
/**
 * お知らせ受信者モデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationReceiver extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'notification_id',
        'entity_type',
        'entity_id',
        'send_method_id',
        'sent_at',
        'is_read',
        'read_at',
    ];

    protected $dates = ['sent_at', 'read_at', 'created_at', 'updated_at', 'deleted_at'];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function sendMethod()
    {
        return $this->belongsTo(NotificationSendMethod::class);
    }
}
