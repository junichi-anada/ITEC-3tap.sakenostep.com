<?php
/**
 * サイトモデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

 namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'site_code',
        'company_id',
        'url',
        'name',
        'description',
        'is_btob',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function operators()
    {
        return $this->hasMany(SiteOperator::class);
    }

    public function authenticates()
    {
        return $this->hasMany(Authenticate::class);
    }

    public function authenticateOauths()
    {
        return $this->hasMany(AuthenticateOauth::class);
    }

    public function usableSites()
    {
        return $this->hasMany(UsableSite::class);
    }

    public function siteAuthProviders()
    {
        return $this->hasMany(SiteAuthProvider::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
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

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function userExternalCodes()
    {
        return $this->hasMany(UserExternalCode::class);
    }
}
