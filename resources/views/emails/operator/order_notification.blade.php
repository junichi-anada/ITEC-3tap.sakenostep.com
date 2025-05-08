<x-mail::message>
# 新規注文のお知らせ

新しい注文が入りました。詳細は以下の通りです。

## 注文情報
- **注文ID:** {{ $order->id }}
- **注文日時:** {{ $order->created_at->format('Y年m月d日 H:i') }}
@if($order->customer)
- **顧客名:** {{ $order->customer->name ?? 'N/A' }} (ID: {{ $order->customer->id ?? 'N/A' }})
@else
- **顧客情報:** 登録されていません
@endif

## 注文商品
<x-mail::table>
| 商品名 | 単価 | 数量 | 小計 |
| :----- | :---: | :--: | :--: |
@if($order->orderDetails && $order->orderDetails->count() > 0)
@foreach($order->orderDetails as $detail)
| {{ $detail->item_name ?? ($detail->item->name ?? 'N/A') }} | ¥{{ number_format($detail->price_at_ordering ?? ($detail->item->price ?? 0)) }} | {{ $detail->quantity ?? 'N/A' }} | ¥{{ number_format(($detail->price_at_ordering ?? ($detail->item->price ?? 0)) * ($detail->quantity ?? 0)) }} |
@endforeach
@else
| 商品情報がありません | - | - | - |
@endif
</x-mail::table>

## 合計金額
**¥{{ number_format($order->total_amount ?? 0) }}**

ご確認よろしくお願いいたします。

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
