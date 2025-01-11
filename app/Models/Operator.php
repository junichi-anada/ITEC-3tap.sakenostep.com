<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * オペレータモデル
 */
class Operator extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'operator_code',
        'company_id',
        'name',
        'operator_rank_id',
    ];

    /**
     * 会社との関連を取得
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * オペレータランクとの関連を取得
     *
     * @return BelongsTo
     */
    public function operatorRank(): BelongsTo
    {
        return $this->belongsTo(OperatorRank::class);
    }

    /**
     * サイトオペレータとの関連を取得
     *
     * @return HasMany
     */
    public function siteOperators(): HasMany
    {
        return $this->hasMany(SiteOperator::class);
    }
}
