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
    use HasFactory, SoftDeletes;

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

    protected $dates = [
        'followed_at',
        'unfollowed_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * �イトとのリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * ユーザーとのリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * アカウント連携を完了する
     *
     * @param string $lineUserId
     * @return bool
     */
    public function completeLink(string $lineUserId): bool
    {
        return $this->update([
            'line_user_id' => $lineUserId,
            'is_linked' => true,
            'nonce' => null,  // nonceは使用後にクリア
            'followed_at' => now()
        ]);
    }

    /**
     * アカウント連携を解除する
     *
     * @return bool
     */
    public function unlink(): bool
    {
        return $this->update([
            'is_linked' => false,
            'followed_at' => null,
            'unfollowed_at' => now()
        ]);
    }

    /**
     * 新しいnonceを生成する
     *
     * @return string
     */
    public function refreshNonce(): string
    {
        $nonce = bin2hex(random_bytes(16));
        $this->update(['nonce' => $nonce]);
        return $nonce;
    }
}
