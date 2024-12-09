<?php
/**
 * 注文ツールボックスウィジェット
 */
?>
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b p-4 w-full">
    <div class="flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold text-xl">注文ツールボックス</h2>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="flex flex-col gap-y-2">
                <h3 class="font-medium">CSVデータ書き出し</h3>
                <div class="flex gap-x-4">
                    <button id="export_csv" class="bg-[#F4CF41] px-4 py-2 rounded hover:bg-[#E4BF31] flex items-center gap-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        CSV書出
                    </button>
                </div>
                <p class="text-sm text-gray-600">※検索条件に該当する注文データをCSVファイルとして書き出します</p>
            </div>
        </div>
    </div>
</div>
