<div class="w-1/2">
    <div class="flex border border-black gap-x-2">
        <div class="w-[20%] bg-[#B8A044]">
            <p class="text-center text-3xl font-bold py-4 text-white leading-6">{{ $day }}<br><span class="font-normal text-xl">日</span></p>
        </div>
        <div class="pt-2">
            <p class="text-sm">本日の注文数</p>
            <p class="text-2xl font-bold">{{ $count }}<span class="text-sm"> 件</span></p>
            <p class="text-sm">CSV出力　未： {{ $pendingExport }} ／ 済： {{ $completedExport }}</p>
        </div>
    </div>
</div>