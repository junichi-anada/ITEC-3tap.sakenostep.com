<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * オペレータランクモデル
 */
class OperatorRank extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'priority',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'priority' => 'integer',
    ];

    /**
     * オペレータとの関連を取得
     *
     * @return HasMany
     */
    public function operators(): HasMany
    {
        return $this->hasMany(Operator::class);
    }
}
