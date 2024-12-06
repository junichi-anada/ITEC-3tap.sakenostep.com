<?php
/**
 * LINEユーザーモデル
 *
 * @category モデル
 * @package App\Models
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LineUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'user_id',
        'line_user_id',
        'nonce',
        'display_name',
        'picture_url',
        'status_message',
        'is_linked',
        'followed_at',
        'unfollowed_at'
    ];

    protected $casts = [
        'is_linked' => 'boolean',
        'followed_at' => 'datetime',
        'unfollowed_at' => 'datetime'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * サイトとのリレーション
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * LINE認証情報とのリレーション
     */
    public function authenticateOauth()
    {
        return $this->hasOne(AuthenticateOauth::class, 'entity_id')
            ->where('entity_type', self::class);
    }

    /**
     * nonceを生成して保存
     */
    public function generateNonce(): string
    {
        $nonce = bin2hex(random_bytes(16));
        $this->update(['nonce' => $nonce]);
        return $nonce;
    }

    /**
     * nonceを検証
     */
    public function verifyNonce(string $nonce): bool
    {
        return $this->nonce === $nonce;
    }

    /**
     * ユーザーとの連携を行う
     */
    public function linkWithUser(User $user): bool
    {
        return $this->update([
            'user_id' => $user->id,
            'is_linked' => true,
            'nonce' => null
        ]);
    }

    /**
     * ユーザーとの連携を解除
     */
    public function unlinkUser(): bool
    {
        return $this->update([
            'user_id' => null,
            'is_linked' => false
        ]);
    }
}
