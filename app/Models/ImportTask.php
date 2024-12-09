<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_code',
        'site_id',
        'data_type',
        'file_path',
        'total_records',
        'processed_records',
        'success_records',
        'error_records',
        'status',
        'status_message',
        'imported_by',
        'uploaded_at',
        'imported_at',
    ];

    protected $dates = [
        'uploaded_at',
        'imported_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * インポート処理のステータス定数
     */
    const STATUS_PENDING = 'pending';      // 待機中
    const STATUS_PROCESSING = 'processing'; // 処理中
    const STATUS_COMPLETED = 'completed';   // 完了
    const STATUS_FAILED = 'failed';        // 失敗

    /**
     * データタイプの定数
     */
    const DATA_TYPE_CUSTOMER = 'customer'; // 顧客データ
    const DATA_TYPE_ITEM = 'item';        // 商品データ

    /**
     * サイトとのリレーション
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * インポート実行者（Authenticate）とのリレーション
     */
    public function importedBy()
    {
        return $this->belongsTo(Authenticate::class, 'imported_by', 'login_code');
    }

    /**
     * 進捗率を計算
     *
     * @return float
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_records === 0) {
            return 0;
        }
        return round(($this->processed_records / $this->total_records) * 100, 1);
    }

    /**
     * ステータスが完了かどうかを判定
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * ステータスが失敗かどうかを判定
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * ステータスが処理中かどうかを判定
     *
     * @return bool
     */
    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * インポート処理のステータスに応じたラベルを取得
     *
     * @return string
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
}
