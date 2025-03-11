<?php

declare(strict_types=1);

namespace App\Services\Order\Analytics;

use App\Models\Order;
use App\Services\Order\DTOs\AreaOrderData;
use App\Services\ServiceErrorHandler;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * エリア別注文数の分析クラス
 */
final class AreaOrderAnalytics
{
    use ServiceErrorHandler;

    /**
     * エリア別の注文数を取得
     * 
     * 地域は「都道府県＋市区町村」でグループ化される
     * 例：東京都渋谷区、大阪府大阪市など
     *
     * @return array<string, int>
     */
    public function getOrdersByArea(): array
    {
        return $this->tryCatchWrapper(
            callback: function () {
                $orders = Order::select('orders.user_id', DB::raw('count(*) as count'))
                    ->join('users', 'orders.user_id', '=', 'users.id')
                    ->whereDate('orders.created_at', now()->toDateString())
                    ->groupBy('orders.user_id')
                    ->with('user:id,address')
                    ->get();
                
                // 都道府県と市区町村を抽出し、エリアごとにカウントを集計
                $areaOrders = [];
                foreach ($orders as $order) {
                    $address = $order->user->address ?? '';
                    $area = $this->extractPrefectureAndCity($address);
                    
                    if (!isset($areaOrders[$area])) {
                        $areaOrders[$area] = 0;
                    }
                    $areaOrders[$area] += $order->count;
                }

                // 未設定の場合は最後に表示されるようにする
                if (isset($areaOrders['未設定'])) {
                    $undefined = $areaOrders['未設定'];
                    unset($areaOrders['未設定']);
                    $areaOrders['未設定'] = $undefined;
                }

                // 地域名でソートして返却（「未設定」以外）
                ksort($areaOrders);
                
                return $areaOrders;
            },
            errorMessage: 'エリア別注文数の取得に失敗しました'
        );
    }

    /**
     * 住所文字列から都道府県と市区町村を抽出する
     * 
     * @param string $address 住所文字列
     * @return string "○○県○○市" 形式のエリア名
     */
    private function extractPrefectureAndCity(string $address): string
    {
        if (empty($address)) {
            return '未設定';
        }

        // 都道府県のリスト
        $prefectures = [
            '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
            '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
            '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県',
            '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県',
            '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県',
            '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県',
            '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
        ];

        // 都道府県を検出
        $prefecture = '';
        foreach ($prefectures as $pref) {
            if (mb_strpos($address, $pref) !== false) {
                $prefecture = $pref;
                break;
            }
        }

        if (empty($prefecture)) {
            return '未設定';
        }

        // 都道府県の後の文字列から市区町村を抽出
        $afterPrefecture = mb_substr($address, mb_strpos($address, $prefecture) + mb_strlen($prefecture));
        $city = '';
        
        // 市を検出
        if (preg_match('/^(.+?市|.+?区|.+?町|.+?村)/', trim($afterPrefecture), $matches)) {
            $city = $matches[1];
        }

        // 市区町村が検出できない場合
        if (empty($city)) {
            return $prefecture;
        }

        return $prefecture . $city;
    }

    /**
     * 月間の注文総数を取得
     *
     * @return int
     */
    public function getMonthlyOrderCount(): int
    {
        return $this->tryCatchWrapper(
            callback: function () {
                $now = Carbon::now();
                return Order::whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month)
                    ->count();
            },
            errorMessage: '月間注文数の取得に失敗しました'
        );
    }
}
