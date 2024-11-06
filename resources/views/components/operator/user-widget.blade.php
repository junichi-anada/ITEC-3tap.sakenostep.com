{{--
    ユーザー登録状況ウィジェット
    components.operator.user-widget
--}}
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">ユーザー登録状況</h2>
            <p class="mt-2 text-sm text-right">{{ \Carbon\Carbon::now()->format('Y年n月j日') }}現在</p>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="flex items-center justify-between">
                <p>全ユーザー数</p>
                <p><span class="text-3xl font-bold pr-1">{{ $userCount }}</span>件</p>
            </div>
            <div class="flex items-center justify-between">
                <p>新規顧客</p>
                <p><span class="text-3xl font-bold pr-1">{{ $newUserCount }}</span>件</p>
            </div>
            <div class="flex items-center justify-between">
                <p>LINE連携済</p>
                <p><span class="text-3xl font-bold pr-1">{{ $lineUserCount }}</span>件</p>
            </div>
        </div>
    </div>
</div>

