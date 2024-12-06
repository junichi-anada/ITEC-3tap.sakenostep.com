<?php

namespace App\Services\Operator\Customer\Import;

/**
 * 一般データ整形サービスクラス
 *
 * このクラスは一般仕様のデータ整形を提供します。
 */
class GeneralDataFormatterService
{
    /**
     * データを整形する
     *
     * @param array $data
     * @return array
     */
    public function formatData(array $data): array
    {
        // 一般仕様のデータ整形ロジックを実装
        return array_map(function ($row) {
            return [
                'code' => $row['取引先コード'] ?? null,
                'name' => $row['取引先名称'] ?? null,
                'postal_code' => $row['郵便番号'] ?? null,
                'address' => $this->formatAddress($row),
                'phone' => $row['電話番号1_1'] ?? null,
                'fax' => $row['FAX番号1_1'] ?? null,
            ];
        }, $data);
    }

    /**
     * 住所を整形する
     *
     * @param array $row
     * @return string
     */
    private function formatAddress(array $row): string
    {
        return trim(($row['都道府県名'] ?? '') . ' ' . ($row['市区郡町村名称'] ?? '') . ' ' . ($row['番地'] ?? ''));
    }
}
