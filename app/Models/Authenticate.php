<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Authenticate extends Authenticatable
{
    use HasFactory, HasApiTokens;

    use SoftDeletes;

    use Notifiable;

    protected $fillable = [
        'auth_code',
        'site_id',
        'entity_type',
        'entity_id',
        'login_code',
        'password',
        'expires_at',
    ];

    protected $dates = ['expires_at', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * 関連するSiteモデルとのリレーション
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * ポリモーフィックリレーションの設定
     */
    public function entity()
    {
        return $this->morphTo(__FUNCTION__, 'entity_type', 'entity_id');
    }

    /**
     * 認証に使用するフィールドを指定
     */
    public function getAuthIdentifierName()
    {
        return 'login_code';  // 認証に使用するフィールド
    }

    /**
     * 認証に使用する識別子を取得
     */
    public function getAuthIdentifier()
    {
        return $this->login_code;
    }

    /**
     * パスワードフィールドの取得
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * 認証処理メソッド（任意）->Laravelの標準認証を使用するので未使用
     */
    public function authenticateUser($loginCode, $password)
    {
        $auth = self::where('login_code', $loginCode)->first();

        if ($auth && Hash::check($password, $auth->password)) {
            return $auth;
        }

        return null;
    }
}
