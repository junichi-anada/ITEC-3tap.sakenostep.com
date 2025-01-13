<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * インポート/エクスポートログモデル
 */
class ImportExportLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'log_code',
        'site_id',
        'operator_id',
        'operation_type',
        'target_type',
        'file_name',
        'record_count',
        'detail',
        'processed_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'record_count' => 'integer',
        'processed_at' => 'datetime',
    ];

    /**
     * サイトとの関連を取得
     *
     * @return BelongsTo
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * オペレータとの関連を取得
     *
     * @return BelongsTo
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
