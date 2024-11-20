<?php
/**
 * オペレーターランクモデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatorRank extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'priority',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function operators()
    {
        return $this->hasMany(Operator::class);
    }
}
