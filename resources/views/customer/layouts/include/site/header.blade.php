<!-- header -->
<div class="bg-[#F4CF41] py-1 relative">
    <!-- logout -->
    <div class="absolute top-1 right-2">
        <a href="{{ route('logout') }}"><span class="material-symbols-outlined">logout</span></a>
    </div>
    <!-- //logout -->

    <div class="flex flex-col justify-center items-center">
        <a href="{{ route('user.order.item.list') }}" class="tracking-widest text-xl font-extrabold">
            <div class="w-32">
                <img src="{{ asset('image/step_logo.png') }}" alt="酒のステップ">
            </div>
            {{-- <span class=" text-[#DC2626]">酒</span><span class="text-sm text-[#DC2626]">の</span>ステップ --}}
        </a>
    
        {{-- お客様の名前を表示 --}}
        @isset($userName)
            <div class="text-center text-sm font-semibold mt-1">{{ $userName }} 様</div>
        @endisset
    </div>


    @component('customer.widgets.menu')
        {{-- categoriesが定義されていたら、引き渡し --}}
        @if (isset($categories))
            @slot('categories', $categories)
        @endif
    @endcomponent

</div>
<!-- //header -->

@include('customer.widgets.modal')

@include('customer.widgets.search')

@include('customer.widgets.tabnavi')
