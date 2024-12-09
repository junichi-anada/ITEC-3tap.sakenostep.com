<?php

namespace App\Services\Item;

use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use Ramsey\Uuid\Lazy\LazyUuidFromString;

class ItemRegistrationService
{
    /**
     * 商品を登録する
     *
     * @param mixed $request
     * @param mixed $auth
     * @return array
     */
    public function registItem($request, $auth)
    {
        try {
            // リクエストデータの準備
            $input = $request->all();

            // UUIDオブジェクトを文字列に変換
            if (isset($input['item_code']) && $input['item_code'] instanceof LazyUuidFromString) {
                $input['item_code'] = $input['item_code']->toString();
            }

            // バリデーション
            $validator = Validator::make($input, [
                'item_code' => ['required', 'max:64', 'unique:items'],
                'category_id' => ['required', 'string', 'regex:/^\d+$/', 'exists:item_categories,id'],
                'name' => ['required', 'string', 'max:64'],
                'description' => ['nullable', 'string'],
                'is_recommended' => ['nullable', 'in:0,1'],
                'published_at' => ['nullable', 'date'],
                'capacity' => ['nullable', 'string', 'regex:/^\d*\.?\d*$/'],
                'quantity_per_unit' => ['nullable', 'string', 'regex:/^\d+$/']
            ], [
                'item_code.required' => '商品コードは必須です。',
                'item_code.max' => '商品コードは64文字以内で入力してください。',
                'item_code.unique' => 'この商品コードは既に使用されています。',
                'category_id.required' => 'カテゴリーは必須です。',
                'category_id.regex' => 'カテゴリーの値が不正です。',
                'category_id.exists' => '選択されたカテゴリーは存在しません。',
                'name.required' => '商品名は必須です。',
                'name.max' => '商品名は64文字以内で入力してください。',
                'is_recommended.in' => 'おすすめフラグの値が不正です。',
                'published_at.date' => '公開日時の形式が正しくありません。',
                'capacity.regex' => '容量は数値で入力してください。',
                'quantity_per_unit.regex' => 'ケース入数は整数で入力してください。'
            ]);

            if ($validator->fails()) {
                Log::warning('商品登録のバリデーションに失敗しました', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $input
                ]);
                return [
                    'message' => 'error',
                    'reason' => $validator->errors()->first()
                ];
            }

            // トランザクション開始
            return DB::transaction(function () use ($input, $auth) {
                // 商品を作成
                $item = new Item();
                $item->item_code = $input['item_code'];
                $item->site_id = $auth->site_id;
                $item->category_id = (int)$input['category_id'];
                $item->name = $input['name'];
                $item->description = $input['description'];
                $item->from_source = 'MANUAL';
                $item->is_recommended = $input['is_recommended'] ?? false;
                $item->published_at = $input['published_at'];
                $item->capacity = isset($input['capacity']) && $input['capacity'] !== '' ? (float)$input['capacity'] : null;
                $item->quantity_per_unit = isset($input['quantity_per_unit']) && $input['quantity_per_unit'] !== '' ? (int)$input['quantity_per_unit'] : null;

                if (!$item->save()) {
                    throw new Exception('商品情報の保存に失敗しました。');
                }

                Log::info('商品登録が完了しました', [
                    'item_id' => $item->id,
                    'item_code' => $item->item_code
                ]);

                return [
                    'message' => 'success',
                    'item_id' => $item->id
                ];
            });

        } catch (Exception $e) {
            Log::error('商品登録に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
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
            '商品情報の保存に失敗しました。' => '商品情報の登録に失敗しました。',
            'Duplicate entry' => 'この商品コードは既に登録されています。',
            'Foreign key violation' => '関連する情報が存在しないため、登録できません。',
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
