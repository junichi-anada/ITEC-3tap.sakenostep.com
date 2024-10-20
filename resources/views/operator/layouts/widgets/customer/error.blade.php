<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-7.5rem)]">
    <div class="px-4 flex flex-col gap-y-4 h-full w-full">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold  text-xl">エラーが発生しました</h2>
        </div>
            <div class="flex flex-col gap-y-8 h-full" id="customer_regist_data">
                <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">エラー情報</h3>
                <p>{{ session('error_message') }}</p>
                <div class="flex justify-end mt-auto gap-x-4 items-center">
                    <div>
                        <a href="{{ route('operator.dashboard') }}" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">ダッシュボードに戻る</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
