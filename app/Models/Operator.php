<?php
/**
 * オペレーターモデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'operator_code',
        'company_id',
        'name',
        'operator_rank_id',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function rank()
    {
        return $this->belongsTo(OperatorRank::class, 'operator_rank_id');
    }

    public function siteOperators()
    {
        return $this->hasMany(SiteOperator::class);
    }

    public function authenticates()
    {
        return $this->hasMany(Authenticate::class, 'entity_id')->where('entity_type', self::class);
    }

    public function authenticateOauths()
    {
        return $this->hasMany(AuthenticateOauth::class, 'entity_id')->where('entity_type', self::class);
    }

    public function authenticateUser($loginCode, $password)
    {
        $auth = $this->authenticates()->where('login_code', $loginCode)->first();

        if ($auth && \Hash::check($password, $auth->password)) {
            return $auth;
        }

        return null;
    }

    public function authenticateWithToken($token)
    {
        return $this->authenticateOauths()->where('token', $token)->first();
    }
}
