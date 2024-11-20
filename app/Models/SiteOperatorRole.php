<?php
/**
 * サイトオペレーター権限モデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteOperatorRole extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function siteOperators()
    {
        return $this->hasMany(SiteOperator::class, 'role_id');
    }
}
