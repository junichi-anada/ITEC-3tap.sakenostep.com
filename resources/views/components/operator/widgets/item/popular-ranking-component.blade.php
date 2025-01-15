<?php
/**
 * 人気商品表示ウィジェット
 * 
 * @var \App\Models\Item[]|\Illuminate\Database\Eloquent\Collection $popularItems
 */
?>
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">人気商品</h2>
            <p class="mt-2 text-sm text-right">{{ \Carbon\Carbon::now()->format('Y年n月j日') }}現在</p>
        </div>
        <div class="flex flex-col gap-y-3">
            @if(!empty($popularItems) && count($popularItems) > 0)
                @foreach($popularItems as $index => $popularItem)
                    @if($index >= 5)
                        @break
                    @endif
                    
                    @if ($index == count($popularItems) - 1)
                    <div class="flex items-center justify-between">
                    @else
                    <div class="flex items-center justify-between border-b border-b-[#a6a6a6] pb-2">
                    @endif
                        <a href="{{ route('operator.item.show', ['id' => $popularItem->id]) }}" class="flex items-center justify-start gap-x-3 w-full">
                            @if ($index == 0)
                                <span class="material-symbols-outlined text-2xl text-[#d29e44]">looks_one</span>
                            @elseif ($index == 1)
                                <span class="material-symbols-outlined text-2xl text-[#d29e44]">looks_two</span>
                            @elseif ($index == 2)
                                <span class="material-symbols-outlined text-2xl text-[#d29e44]">looks_3</span>
                            @endif
                            <span class="text-sm">{{ $popularItem->name }}</span>
                            <span class="material-symbols-outlined text-2xl ml-auto">chevron_right</span>
                        </a>
                    </div>
                @endforeach
            @else
                <div class="text-gray-500 text-center py-4">
                    ランキングデータがありません
                </div>
            @endif
        </div>
    </div>
</div>
