<!-- header -->
<div class="bg-[#F4CF41] py-1 relative">
    <!-- logout -->
    <div class="absolute top-1 right-2">
        <a href="{{ route('logout') }}"><span class="material-symbols-outlined">logout</span></a>
    </div>
    <!-- //logout -->

    <div class="flex justify-center">
        <a href="{{ route('user.order.item.list') }}" class="tracking-widest text-xl font-extrabold">
            <div class="w-32">
                <img src="{{ asset('image/step_logo.png') }}" alt="酒のステップ">
            </div>
            {{-- <span class=" text-[#DC2626]">酒</span><span class="text-sm text-[#DC2626]">の</span>ステップ --}}
        </a>
    </div>

    @include('user.layouts.widgets.menu')

</div>
<!-- //header -->

@include('user.layouts.widgets.modal')

@include('user.layouts.widgets.search')

@include('user.layouts.widgets.tabnavi')

