<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">商品管理ツール</h2>
        </div>
        <div class="flex flex-col gap-y-4 items-start">
            <div class="flex flex-col gap-y-2 text-sm w-full">
                <p class="font-bold">商品手動登録</p>
                <p>商品情報を手動で登録します。<br><span class="text-red-400">※POSレジには反映しません。</span></p>
                <div class="flex justify-end">
                    <a href="{{ route('operator.item.create') }}" class="bg-red-500 text-white px-4 py-1 rounded-md inline">商品手動登録</a>
                </div>
            </div>
            <div class="flex flex-col gap-y-2 text-sm w-full">
                <p class="font-bold">商品CSVデータ読込</p>
                <p>商品情報をCSVデータから読み込み、システムに登録します。</p>
                <div class="flex justify-end">
                    <button type="button" class="bg-red-500 text-white px-4 py-1 rounded-md" id="item_upload">商品データを読み込む</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- モーダル用の非表示要素 -->
<div id="execMode" class="hidden"></div>

<!-- 共通モーダル -->
@include('operator.layouts.widgets.modal')
