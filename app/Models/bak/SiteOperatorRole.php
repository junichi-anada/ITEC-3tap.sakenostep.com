<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * サイトオペレータロールモデル
 */
class SiteOperatorRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * サイトオペレータとの関連を取得
     *
     * @return HasMany
     */
    public function siteOperators(): HasMany
    {
        return $this->hasMany(SiteOperator::class, 'role_id');
    }
}
