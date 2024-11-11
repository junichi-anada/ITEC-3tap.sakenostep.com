<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-5.5rem)]">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold  text-xl">顧客データCSV読込</h2>
            <p class="text-sm">データ件数： {{ $amount }}件</p>
        </div>
        <div class="flex flex-col gap-y-4 items-start">
            <div class="w-full flex flex-col">
                <div class="flex">
                    <div class="text-center p-4 flex-1 font-bold">取引先コード</div>
                    <div class="text-center p-4 flex-1 font-bold">顧客名</div>
                    <div class="text-center p-4 flex-1 font-bold">電話番号</div>
                    <div class="text-center p-4 w-1/4 font-bold">住所</div>
                    <div class="text-center p-4 flex-1 font-bold">処理結果</div>
                </div>
                <div class="flex flex-col overflow-y-scroll h-[70vh]" id="upload-results"></div>
            </div>
        </div>
    </div>
</div>

