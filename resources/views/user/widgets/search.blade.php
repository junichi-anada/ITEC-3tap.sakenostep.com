{{-- 検索ウィンドウ --}}
<!-- Search Window Wrapper -->
<div class="flex flex-col pt-2 px-2 bg-[#F6F6F6]">
    <form action="{{ route('user.search.item.list') }}" method="POST">
        @csrf
        <div class="flex items-center justify-between border border-gray-400">
                <button class="w-[30px] bg-white py-0.5 px-2" type="submit">
                    <span class="material-symbols-outlined text-xl font-bold">search</span>
                </button>
                <input type="text" class="text-sm w-full py-1.5 pl-2" placeholder="商品名・商品コードで検索" name="keyword" @if(!empty(trim($keyword ?? ''))) value="{{ trim($keyword) }}" @endif>
        </div>
    </form>
</div>
<!-- //Search Window Wrapper -->
