<!-- Tab Navi Wrapper -->
<div class="flex flex-col pt-2 px-2 bg-[#F6F6F6]">
    <div class="h-[44px]">
        <div class="overflow-x-scroll hide-scrollbar">
            <div class="flex flex-nowrap">
                <a href="{{ route('user.order.item.list') }}" class="py-2 px-3 whitespace-nowrap block {{ request()->is('order') ? 'bg-white rounded-t-xl': 'bg-gray-300 rounded-t-lg' }}">
                    <div class="flex items-center gap-x-1">
                        <span class="material-symbols-outlined text-xl text-[#F4CF41]">shopping_cart</span>
                        <span class="text-xs">注文リスト</span>
                    </div>
                </a>
                <a href="{{ route('user.favorite.item.list') }}" class="py-2 px-3 whitespace-nowrap block {{ request()->is('favorites') ? 'bg-white rounded-t-xl': 'bg-gray-300 rounded-t-lg' }}">
                    <div class="flex items-center gap-x-1">
                        <span class="material-symbols-outlined text-xl text-[#F4CF41]">favorite</span>
                        <span class="text-xs">お気に入り</span>
                    </div>
                </a>
                <a href="{{ route('user.recommended.item.list') }}" class="py-2 px-3 whitespace-nowrap block {{ request()->is('recommendations') ? 'bg-white rounded-t-xl': 'bg-gray-300 rounded-t-lg' }}">
                    <div class="flex items-center gap-x-2">
                        <span class="material-symbols-outlined text-xl text-[#F4CF41]">thumb_up</span>
                        <span class="text-xs">おすすめ</span>
                    </div>
                </a>
                <a href="{{ route('user.category.list') }}" class="py-2 px-3 whitespace-nowrap block bg-gray-300 rounded-t-lg">
                    <div class="flex items-center gap-x-2">
                        <span
                            class="material-symbols-outlined text-xl text-[#F4CF41]">format_list_bulleted</span>
                        <span class="text-xs">商品一覧</span>
                    </div>
                </a>
                <a href="{{ route('user.search.item.list') }}" id="search-tab" class="py-2 px-3 whitespace-nowrap block {{ request()->is('search') ? 'bg-white rounded-t-xl': 'bg-gray-300 rounded-t-lg' }}">
                    <div class="flex items-center gap-x-2">
                        <span class="material-symbols-outlined text-xl text-[#F4CF41]">search</span>
                        <span class="text-xs">検索結果</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- //Tab Navi Wrapper -->
