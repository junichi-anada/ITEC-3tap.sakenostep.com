{{-- ハンバーガーメニュー --}}
<!-- hamberger menu -->
<div class="absolute top-1 left-2">
    <button id="hamburger" type="button" class="fixed z-20">
        <span id="bars" class="material-symbols-outlined">menu </span>
        <span id="xmark" class="material-symbols-outlined hidden">close</span>
    </button>
</div>
<!-- //hamberger menu -->

<!-- nav -->
<div class="bg-[#F4CF41] absolute px-5 py-2 top-0 transition-all ease-linear -translate-x-full" style="width: 100%; z-index: 10;"
    id="menu">
    <div>
        <div class="w-[22px] ml-auto">
            @if(config('services.line.url'))
            <a href="{{ config('services.line.url') }}" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('image/user/mymenu/svg/line.svg') }}" alt="LINE">
            </a>
            @endif
        </div>
    </div>
    <ul class="flex flex-col gap-y-1 pb-3">
        <li class="border-b border-gray-400 py-2 px-2">
            <a href="{{ route('user.history.list') }}" class="inline-block w-full"><span class="text-base">注文履歴</span></a>
        </li>
        <li class="border-b border-gray-400 pb-2 px-2">
            <a href="{{ route('user.category.list') }}" class="inline-block  w-full"><span class="text-base">商品一覧</span></a>
        </li>
        {{-- categoriesが空の場合 --}}
        @if (empty($categories))
            <ul>
                <li class="text-lg font-bold">カテゴリがありません。</li>
            </ul>
        @elseif (count($categories) === 0)
            <ul>
                <li class="text-lg font-bold">カテゴリがありません。</li>
            </ul>
        @else
            <ul class="flex flex-row" style="flex-wrap: wrap;gap: 0.25em 0.5em">
                @foreach($categories as $category)
                <!-- liを1行2列で表現する。 -->
                <li class="border-b border-gray-400 pb-1 px-1" style="width: calc(50% - 0.25em);">
                    <a href="{{ route('user.category.item.list', ['code' => $category->category_code]) }}" class="inline-block w-full">
                        <span class="text-sm">{{ $category->name }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        @endif
    </ul>

    <div class="flex justify-center gap-x-4">
        <div>
            <a href="{{ route('user.about.order') }}" class="bg-white flex flex-col items-center border border-gray-300 gap-y-3 py-3 px-3">
                <span class="material-symbols-outlined text-3xl">description</span>
                <span class="text-xs">注文について</span>
            </a>
        </div>
        <div>
            <a href="{{ route('user.about.delivery') }}" class="bg-white flex flex-col items-center border border-gray-300 gap-y-3 py-3 px-3">
                <span class="material-symbols-outlined text-3xl">local_shipping</span>
                <span class="text-xs">配送について</span>
            </a>
        </div>
    </div>
</div>
<!-- //nav -->

<!-- Script -->
<script src="{{ asset('js/menu.js') }}"></script>
