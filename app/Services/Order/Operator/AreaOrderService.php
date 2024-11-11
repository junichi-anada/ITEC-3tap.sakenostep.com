<?php

namespace App\Services\Order\Operator;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AreaOrderService
{
    /**
     * 地域ごとの注文数を取得
     *
     * @return array [ key: 地域, value: 注文数 ]
     */
    public function getOrdersByArea()
    {
      // サイトIDを取得
      $site_id = Auth::user()->site_id;

      $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
          ->where('users.site_id', $site_id)
          ->select('orders.*', 'users.name as user_name', 'users.address')
          ->get();

      // 地域ごとの注文数を格納する配列
      $area_order_count = [];

      // 地域ごとの注文数を取得
      foreach ($orders as $order) {
          // 住所から地域を取得
          $area = $this->getArea($order->address);

          // 地域ごとの注文数をカウント
          if (isset($area_order_count[$area])) {
              $area_order_count[$area]++;
          } else {
              $area_order_count[$area] = 1;
          }
      }
      return $area_order_count;
    }

    /**
     * 住所から地域を取得
     *
     * @param string $address
     * @return string
     */
    private function getArea(string $address): string
    {
      if (preg_match('/^(.+?(都|道|府|県).+?(市|区|町|村))/u', $address, $matches) && isset($matches[1])) {
          return $matches[1];
      }
      return '不明';
    }
}