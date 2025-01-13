<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 会社モデル
 */
class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'company_code',
        'company_name',
        'name',
        'postal_code',
        'address',
        'phone',
        'phone2',
        'fax',
    ];

    /**
     * サイトとの関連を取得
     *
     * @return HasMany
     */
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

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
