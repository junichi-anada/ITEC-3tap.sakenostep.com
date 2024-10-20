{{-- エリア別売り上げ --}}
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">エリア別売上</h2>
            <p class="mt-2 text-sm text-right">{{ \Carbon\Carbon::now()->format('Y年n月j日') }}現在</p>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="flex items-center gap-x-10">
                <p class="flex-1">青森県八戸市</p>
                <p><span class="text-3xl font-bold pr-1">30</span>件</p>
                <p><span class="text-3xl font-bold pr-1">123,000</span>円</p>
            </div>
            <div class="flex items-center gap-x-10">
                <p class="flex-1">青森県十和田市</p>
                <p><span class="text-3xl font-bold pr-1">30</span>件</p>
                <p><span class="text-3xl font-bold pr-1">123,000</span>円</p>
            </div>
            <div class="flex items-center gap-x-10">
                <p class="flex-1">青森県五戸市</p>
                <p><span class="text-3xl font-bold pr-1">30</span>件</p>
                <p><span class="text-3xl font-bold pr-1">123,000</span>円</p>
            </div>
        </div>
    </div>
</div>
