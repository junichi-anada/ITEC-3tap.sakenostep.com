<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">注文管理ツール</h2>
        </div>
        <div class="flex flex-col gap-y-4 items-start">
            <!-- <div class="flex flex-col gap-y-2 text-sm w-full">
                <p class="font-bold">顧客手動登録</p>
                <p>顧客情報を手動で登録します。<br><span class="text-red-400">※POSレジには反映しません。</span></p>
                <div class="flex justify-end">
                    <a href="{{ route('operator.customer.create') }}" class="bg-red-500 text-white px-4 py-1 rounded-md inline">顧客手動登録</a>
                </div>
            </div> -->
            <div class="flex flex-col gap-y-2 text-sm w-full">
                <p class="font-bold">注文CSVデータ書き出し</p>
                <p>上記の検索条件に合致したデータをCSVデータとして書き出します。</p>
                <p class="text-red-400">書き出したデータは自動的に本システム上では、受注済みとして登録されます。</p>
                <div class="flex justify-end">
                    <button class="bg-red-500 text-white px-4 py-1 rounded-md" id="customer_upload">注文データを書き出す</a>
                </div>
            </div>
        </div>
    </div>
</div>
