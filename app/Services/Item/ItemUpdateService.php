<?php

namespace App\Services\Item;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Validation\Rule;

class ItemUpdateService
{
    /**
     * 商品情報を更新する
     *
     * @param int $itemId
     * @param array $data
     * @return array
     */
    public function updateItem($itemId, $data)
    {
        try {
            // バリデーション
            $validator = Validator::make($data, [
                'item_code' => [
                    'required',
                    'string',
                    'max:64',
                    Rule::unique('items')->ignore($itemId)
                ],
                'category_id' => ['required', 'exists:item_categories,id'],
                'name' => ['required', 'string', 'max:64'],
                'description' => ['nullable', 'string'],
                'unit_id' => ['required', 'exists:item_units,id'],
                'is_recommended' => ['nullable', 'in:0,1'],
                'published_at' => ['nullable', 'date'],
                'capacity' => ['nullable', 'numeric', 'min:0'],
                'quantity_per_unit' => ['nullable', 'integer', 'min:0']
            ], [
                'item_code.required' => '商品コードは必須です。',
                'item_code.max' => '商品コードは64文字以内で入力してください。',
                'item_code.unique' => 'この商品コードは既に使用されています。',
                'category_id.required' => 'カテゴリーは必須です。',
                'category_id.exists' => '選択されたカテゴリーは存在しません。',
                'name.required' => '商品名は必須です。',
                'name.max' => '商品名は64文字以内で入力してください。',
                'unit_id.required' => '単位は必須です。',
                'unit_id.exists' => '選択された単位は存在しません。',
                'is_recommended.in' => 'おすすめフラグの値が不正です。',
                'published_at.date' => '公開日時の形式が正しくありません。',
                'capacity.numeric' => '容量は数値で入力してください。',
                'capacity.min' => '容量は0以上で入力してください。',
                'quantity_per_unit.integer' => 'ケース入数は整数で入力してください。',
                'quantity_per_unit.min' => 'ケース入数は0以上で入力してください。'
            ]);

            if ($validator->fails()) {
                Log::warning('商品更新のバリデーションに失敗しました', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $data,
                    'item_id' => $itemId
                ]);
                return [
                    'message' => 'error',
                    'reason' => $validator->errors()->first()
                ];
            }

            // トランザクション開始
            return DB::transaction(function () use ($itemId, $data) {
                $item = Item::findOrFail($itemId);

                // 商品情報を更新
                $item->item_code = $data['item_code'];
                $item->category_id = $data['category_id'];
                $item->name = $data['name'];
                $item->description = $data['description'];
                $item->unit_id = $data['unit_id'];
                $item->is_recommended = $data['is_recommended'] ?? $item->is_recommended;
                $item->published_at = $data['published_at'];
                $item->capacity = $data['capacity'];
                $item->quantity_per_unit = $data['quantity_per_unit'];

                if (!$item->save()) {
                    throw new Exception('商品情報の更新に失敗しました。');
                }

                Log::info('商品情報の更新が完了しました', [
                    'item_id' => $item->id,
                    'item_code' => $item->item_code
                ]);

                return [
                    'message' => 'success'
                ];
            });

        } catch (Exception $e) {
            Log::error('商品更新に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'item_id' => $itemId,
                'data' => $data
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
            '商品情報の更新に失敗しました。' => '商品情報の更新に失敗しました。',
            'No query results' => '指定された商品が見つかりません。',
            'Foreign key violation' => '関連する情報が存在しないため、更新できません。',
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
