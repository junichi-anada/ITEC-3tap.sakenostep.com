<?php
/**
 * 商品一覧表示ウィジェット
 *
 * @var \Illuminate\Pagination\LengthAwarePaginator $items
 */
?>
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-5.5rem)]">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold text-xl">商品一覧</h2>
            <p class="text-sm">
                {{ number_format($items->total()) }}件中
                {{ number_format($items->firstItem()) }}件目～{{ number_format($items->lastItem()) }}件目表示
            </p>
        </div>
        <div class="flex flex-col gap-y-4 items-start">
            <div class="w-full flex flex-col">
                <div class="flex">
                    <div class="text-center p-4 flex-1 font-bold">商品コード</div>
                    <div class="text-center p-4 w-1/4 font-bold">商品名</div>
                    <div class="text-center p-4 flex-1 font-bold">カテゴリ</div>
                    <div class="text-center p-4 flex-1 font-bold">容量</div>
                    <div class="text-center p-4 flex-1 font-bold">ケース入数</div>
                    <div class="text-center p-4 flex-1 font-bold">おすすめ</div>
                    <div class="text-center p-4 flex-1 font-bold">公開状態</div>
                    <div class="text-center p-4 flex-1 font-bold">登録方法</div>
                </div>
                @if ($items->count() > 0)
                    <div class="flex flex-col overflow-y-scroll h-[calc(100vh-20rem)]">
                        @foreach ($items as $item)
                            <a href="{{ route('operator.item.show', ['id' => $item->id]) }}">
                                <div class="flex hover:bg-[#F4CF41] hover:bg-opacity-40 cursor-pointer">
                                    <div class="text-center p-4 flex-1 text-sm">{{ $item->item_code }}</div>
                                    <div class="text-center p-4 w-1/4 text-sm">{{ $item->name }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $item->category->name }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $item->capacity }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $item->quantity_per_unit }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">
                                        @if ($item->is_recommended)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                おすすめ
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-center p-4 flex-1 text-sm">
                                        @if ($item->published_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                公開中
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                非公開
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-center p-4 flex-1 text-sm">
                                        @if ($item->from_source === 'MANUAL')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                手動登録
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                インポート
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
</div>
