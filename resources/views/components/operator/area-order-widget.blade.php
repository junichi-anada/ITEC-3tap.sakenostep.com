<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">エリア別注文数</h2>
            <p class="mt-2 text-sm text-right">{{ \Carbon\Carbon::now()->format('Y年n月j日') }}現在</p>
        </div>
        <div class="flex flex-col gap-y-4">
            @if (empty($ordersByArea))
                <p>エリア別の注文はありません。</p>
            @else
                @foreach($ordersByArea as $key => $val)
                    <div class="flex items-center gap-x-10">
                        <p class="flex-1">{{ $key }}</p>
                        <p><span class="text-3xl font-bold pr-1">{{ $val }}</span>件</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
