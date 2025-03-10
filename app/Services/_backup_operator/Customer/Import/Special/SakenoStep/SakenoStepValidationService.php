<?php

namespace App\Services\Operator\Customer\Import\Special\SakenoStep;

use Illuminate\Support\Facades\Validator;

/**
 * SakenoStepバリデーションサービスクラス
 *
 * このクラスはSakenoStep仕様のデータバリデーションを提供します。
 */
class SakenoStepValidationService
{
    /**
     * バリデーションルールを取得する
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            '取引先コード' => 'required|numeric',
            '取引先名称' => 'required|string|max:255',
            '郵便番号' => 'nullable|numeric',
            '都道府県名' => 'nullable|string|max:255',
            '市区郡町村名称' => 'nullable|string|max:255',
            '番地' => 'nullable|string|max:255',
            '電話番号1_1' => 'nullable|string|max:255',
            '電話番号2_1' => 'nullable|string|max:255',
            '検索カナ' => 'required|string|max:255',
            'FAX番号1_1' => 'nullable|string|max:255',
            '取引先・掛/現金印字区分' => 'required|numeric',
            '掛/現金区分' => 'required|numeric|in:0,1',
            '更新日' => 'required|numeric',
            '削除日' => 'required|numeric',
            '記念日コード1' => 'nullable|string|max:255',
            '記念日_年_1' => 'nullable|string|max:255',
            '記念日_月日_1' => 'nullable|string|max:255',
            '記念日コード2' => 'nullable|string|max:255',
            '記念日_年_2' => 'nullable|string|max:255',
            '記念日_月日_2' => 'nullable|string|max:255',
            '記念日コード3' => 'nullable|string|max:255',
            '記念日_年_3' => 'nullable|string|max:255',
            '記念日_月日_3' => 'nullable|string|max:255',
            '記念日コード4' => 'nullable|string|max:255',
            '記念日_年_4' => 'nullable|string|max:255',
            '記念日_月日_4' => 'nullable|string|max:255',
            '記念日コード5' => 'nullable|string|max:255',
            '記念日_年_5' => 'nullable|string|max:255',
            '記念日_月日_5' => 'nullable|string|max:255',
            '請求書用紙区分' => 'nullable|numeric',
            'ＤＭ発行区分' => 'nullable|numeric',
            '店頭売区分' => 'nullable|numeric',
            'Column1' => 'nullable|numeric',
            '税区分' => 'nullable|numeric',
            '_1' => 'nullable|numeric',
            '_2' => 'nullable|numeric',
            '_3' => 'nullable|numeric',
            '_4' => 'nullable|string|max:255',
        ];
    }

    /**
     * ファイル内容をバリデーションする
     *
     * @param array $rows 検証対象のデータ行
     * @return array バリデーションエラーの配列
     */
    public function validateFileContent(array $rows): array
    {
        // バリデーションエラーを格納する配列を初期化
        $validationErrors = [];

        // 各行をバリデート
        foreach ($rows as $index => $row) {
            // バリデータインスタンスを作成
            $validator = \Validator::make($row, $this->getValidationRules());

            // バリデーション失敗時にエラーを収集
            if ($validator->fails()) {
                $validationErrors[$index] = $validator->errors()->all();
            }
        }

        // バリデーションエラー配列を返す
        return $validationErrors;
    }

}
