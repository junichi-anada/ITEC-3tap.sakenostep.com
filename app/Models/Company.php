<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;

    use SoftDeletes;

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

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function operators()
    {
        return $this->hasMany(Operator::class);
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
