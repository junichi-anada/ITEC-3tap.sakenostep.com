<x-mail::message>
# 新規注文のお知らせ

新しい注文が入りました。詳細は以下の通りです。

## 注文情報
- **注文コード:** {{ $order->order_code }}
- **注文日時:** {{ $order->created_at->format('Y年m月d日 H:i') }}
@if($order->customer)
- **顧客名:** {{ $order->customer->name ?? '未登録' }}
@else
- **顧客情報:** 登録されていません
@endif

## 注文商品
<x-mail::table>
| 商品名 | 数量 |
| :----- | :--: |
@if($order->orderDetails && $order->orderDetails->count() > 0)
@foreach($order->orderDetails as $detail)
@php
    $volume = $detail->volume ?? 0;
@endphp
| {{ $detail->item_name ?? ($detail->item->name ?? 'N/A') }} | {{ $volume }} |
@endforeach
@else
| 商品情報がありません | - |
@endif
</x-mail::table>

ご確認よろしくお願いいたします。

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
