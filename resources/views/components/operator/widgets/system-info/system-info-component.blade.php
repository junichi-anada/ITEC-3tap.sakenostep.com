<?php
/**
 * システム管理者からのお知らせ表示ウィジェット
 * 
 * @var Collection $systemInfos
 */
?>
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">システム管理者からのお知らせ</h2>
            <p class="mt-2 text-sm text-right">{{ \Carbon\Carbon::now()->format('Y年n月j日') }}現在</p>
        </div>
        <div class="flex flex-col gap-y-4">
            @if ($systemInfos->isEmpty())
                <p>現在お知らせはありません。</p>
            @else
                @foreach($systemInfos as $info)
                    <div class="border-b pb-4">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-bold">{{ $info->title }}</h3>
                            <span class="text-sm text-gray-500">{{ $info->createdAt->format('Y/m/d') }}</span>
                        </div>
                        <p class="whitespace-pre-wrap">{{ $info->content }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
