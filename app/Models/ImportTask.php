<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportTask extends Model
{
    /**
     * インポート処理のステータス定数
     */
    const STATUS_PENDING = 'pending';      // 待機中
    const STATUS_PROCESSING = 'processing'; // 処理中
    const STATUS_COMPLETED = 'completed';   // 完了（エラーなし）
    const STATUS_COMPLETED_WITH_ERRORS = 'completed_with_errors';  // 完了（エラーあり）
    const STATUS_FAILED = 'failed';        // 失敗

    /**
     * データタイプの定数
     */
    const DATA_TYPE_CUSTOMER = 'customer'; // 顧客データ
    const DATA_TYPE_ITEM = 'item';        // 商品データ

    protected $fillable = [
        'task_code',
        'type',
        'status',
        'status_message',
        'file_path',
        'site_id',
        'created_by',
        'total_records',
        'processed_records',
        'success_records',
        'error_records',
        'error_message',
        'data_type',
        'uploaded_at',
        'imported_by'
    ];

    protected $casts = [
        'total_records' => 'integer',
        'processed_records' => 'integer',
        'success_records' => 'integer',
        'error_records' => 'integer',
    ];

    /**
     * インポートタスクレコードとのリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function importTaskRecords(): HasMany
    {
        return $this->hasMany(ImportTaskRecord::class, 'import_task_id');
    }

    /**
     * タスクに紐づくレコードを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function records(): HasMany
    {
        return $this->hasMany(ImportTaskRecord::class, 'import_task_id');
    }

    /**
     * タスクが完了しているかチェック
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_COMPLETED,
            self::STATUS_COMPLETED_WITH_ERRORS,
            self::STATUS_FAILED
        ]);
    }
}
