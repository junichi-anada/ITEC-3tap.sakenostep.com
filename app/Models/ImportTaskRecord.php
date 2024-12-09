<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportTaskRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'import_task_id',
        'row_number',
        'status',
        'data',
        'error_message',
        'processed_at',
    ];

    protected $dates = [
        'processed_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * ステータス定数
     */
    const STATUS_PENDING = 'pending';      // 待機中
    const STATUS_PROCESSING = 'processing'; // 処理中
    const STATUS_COMPLETED = 'completed';   // 完了
    const STATUS_FAILED = 'failed';        // 失敗

    /**
     * インポートタスクとのリレーション
     */
    public function importTask()
    {
        return $this->belongsTo(ImportTask::class);
    }

    /**
     * ステータスラベルの取得
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => '待機中',
            self::STATUS_PROCESSING => '処理中',
            self::STATUS_COMPLETED => '完了',
            self::STATUS_FAILED => '失敗',
            default => '不明',
        };
    }

    /**
     * ステータスに応じたラベルクラスを取得
     */
    public function getStatusClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PROCESSING => 'bg-blue-100 text-blue-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_FAILED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
