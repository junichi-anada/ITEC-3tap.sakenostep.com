<x-mail::message>
# ご注文ありがとうございます

この度はご注文いただき、誠にありがとうございます。
ご注文内容の詳細は以下の通りです。

## ご注文情報
- **注文コード:** {{ $order->order_code }}
- **ご注文日時:** {{ $order->created_at->format('Y年m月d日 H:i') }}

## ご注文商品
<x-mail::table>
| 商品名 | 数量 |
| :----- | :--: |
@if($order->orderDetails && $order->orderDetails->count() > 0)
@foreach($order->orderDetails as $detail)
@php
    $quantity = $detail->quantity ?? 0;
@endphp
| {{ $detail->item_name ?? ($detail->item->name ?? 'N/A') }} | {{ $quantity }} |
@endforeach
@else
| 商品情報がありません | - |
@endif
</x-mail::table>

今後ともご愛顧賜りますようお願い申し上げます。

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
