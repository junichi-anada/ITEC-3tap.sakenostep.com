<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'notification_code',
        'category_id',
        'title',
        'content',
        'publish_start_at',
        'publish_end_at',
    ];

    protected $dates = ['publish_start_at', 'publish_end_at', 'created_at', 'updated_at', 'deleted_at'];

    public function category()
    {
        return $this->belongsTo(NotificationCategory::class);
    }

    public function receivers()
    {
        return $this->hasMany(NotificationReceiver::class);
    }

    public function senders()
    {
        return $this->hasMany(NotificationSender::class);
    }
}
