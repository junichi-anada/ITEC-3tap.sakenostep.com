<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Services\ServiceErrorHandler;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderExportService
{
    use ServiceErrorHandler;

    /**
     * ログインコードから取引先コードを生成
     *
     * @param string|null $loginCode
     * @return string
     */
    private function generateCustomerCode(?string $loginCode): string
    {
        if (empty($loginCode)) {
            return '';
        }

        // ログインコードの右から5桁を取得
        $code = substr($loginCode, -5);

        // 数値以外の文字を除去
        $code = preg_replace('/[^0-9]/', '', $code);

        // 5桁未満の場合は左側を0で埋める
        return str_pad($code, 5, '0', STR_PAD_LEFT);
    }

    /**
     * 注文データをCSVファイルとして出力
     *
     * @param Collection $orders
     * @return StreamedResponse|null
     */
    public function execute(Collection $orders): ?StreamedResponse
    {
        try {
            $filename = date('Ymd') . '_order.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
            ];

            $callback = function() use ($orders) {
                $file = fopen('php://output', 'w');

                // BOMを付与
                fwrite($file, "\xEF\xBB\xBF");

                // ヘッダー行
                fputcsv($file, [
                    '取引先区分',
                    '伝票日付',
                    '取引先コード',
                    '納品先コード',
                    '伝票行番区分',
                    '商品コード',
                    'JANコード',
                    'ケース／バラ',
                    '数量',
                    '税区分',
                    '単価',
                    '金額',
                    '相手先伝票番号',
                    '相手先商品コード',
                    '相手先商品名'
                ]);

                // データ行
                foreach ($orders as $order) {
                    foreach ($order->orderDetails as $detail) {
                        $item = $detail->item;

                        // 商品名を15文字に制限
                        $itemName = mb_substr($item->name, 0, 15);

                        // ケース／バラ区分の計算
                        $caseBaraType = $item->quantity_per_unit > 0 ? '1' : '0';

                        // 取引先コードの生成
                        $loginCode = $order->user->authenticates->first()->login_code ?? '';
                        $customerCode = $this->generateCustomerCode($loginCode);

                        fputcsv($file, [
                            '1', // 取引先区分: 1固定
                            $order->ordered_at->format('Ymd'), // 伝票日付
                            $customerCode, // 取引先コード
                            '1', // 納品先コード: 1固定
                            '1', // 伝票行番区分: 1固定
                            $item->item_code, // 商品コード
                            $item->jan_code ?? '', // JANコード
                            $caseBaraType, // ケース／バラ
                            $detail->volume, // 数量
                            '', // 税区分
                            '', // 単価
                            '', // 金額
                            $order->order_code, // 相手先伝票番号
                            $item->item_code, // 相手先商品コード
                            $itemName // 相手先商品名
                        ]);
                    }
                }

                fclose($file);
            };

            // CSV出力日時を更新
            $orders->each(function ($order) {
                $order->exported_at = now();
                $order->save();
            });

            return new StreamedResponse($callback, 200, $headers);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->setError('CSVの書き出しに失敗しました。');
            return null;
        }
    }
}
