<?php

namespace App\Services\Item;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemDeleteService
{
    /**
     * 商品を削除する
     *
     * @param Item $item 削除対象の商品モデル
     * @param mixed $auth 認証情報
     * @return array
     */
    public function deleteItem(Item $item, $auth)
    {
        try {
            DB::beginTransaction();

            // 商品を論理削除
            $item->delete();

            DB::commit();

            Log::info('商品の削除が完了しました', [
                'item_id' => $item->id,
                'item_code' => $item->item_code
            ]);

            return [
                'message' => 'success'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('商品の削除に失敗しました', [
                'item_id' => $item->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'message' => 'error',
                'reason' => $this->getErrorMessage($e->getMessage())
            ];
        }
    }

    /**
     * エラーメッセージを取得
     *
     * @param string $message
     * @return string
     */
    private function getErrorMessage($message)
    {
        // 既知のエラーメッセージをユーザーフレンドリーなメッセージに変換
        $errorMessages = [
            'No query results' => '指定された商品が見つかりません。',
            'Foreign key violation' => 'この商品は他の機能で使用されているため削除できません。',
        ];

        foreach ($errorMessages as $key => $value) {
            if (strpos($message, $key) !== false) {
                return $value;
            }
        }

        // 未知のエラーの場合
        return 'システムエラーが発生しました。管理者に連絡してください。';
    }
}
