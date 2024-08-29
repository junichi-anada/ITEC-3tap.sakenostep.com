<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory;
    // use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //     ];
    // }

    use SoftDeletes;

    protected $fillable = [
        'user_code',
        'site_id',
        'name',
        'postal_code',
        'address',
        'phone',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function authenticates()
    {
        return $this->hasMany(Authenticate::class, 'entity_id')->where('entity_type', self::class);
    }

    public function authenticateOauths()
    {
        return $this->hasMany(AuthenticateOauth::class, 'entity_id')->where('entity_type', self::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favoriteItems()
    {
        return $this->hasMany(FavoriteItem::class);
    }

    public function notificationReceivers()
    {
        return $this->hasMany(NotificationReceiver::class);
    }

    public function userExternalCodes()
    {
        return $this->hasMany(UserExternalCode::class);
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
