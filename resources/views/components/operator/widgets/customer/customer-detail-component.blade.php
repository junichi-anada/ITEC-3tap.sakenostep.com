<form id="customer_form" class="border py-4 h-full">
    @csrf
    <div class="flex flex-col gap-y-4 h-full w-full px-4">
        <div class="flex flex-col gap-y-8 h-full">
            <div class="flex flex-row items-center gap-x-4">
                <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">顧客情報</h3>
                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-x-2 items-center">
                    <label class="">LINE連携</label>
                    <div class="flex items-center">
                        @if($lineUser && $lineUser->is_linked)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-md">連携済み</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md">未連携</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-x-2 items-center">
                    <label class="">会員状態</label>
                    <div class="flex items-center">
                        @if(!$user->deleted_at)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-md">利用中</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-md">登録解除</span>
                        @endif
                    </div>
                </div>
            </div>

            <input type="hidden" name="user_code" value="{{ $user->user_code }}">
            <input type="hidden" id="user_id" value="{{ $user->id }}">

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="login_code" class="w-full md:w-[5vw]">ログインID</label>
                <input type="text" name="login_code" id="login_code" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $authenticate ? $authenticate->login_code : '（削除済み）' }}" readonly>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="name" class="w-full md:w-[5vw]">顧客名</label>
                <input type="text" name="name" id="name" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->name }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="phone" class="w-full md:w-[5vw]">電話番号</label>
                <input type="text" name="phone" id="phone" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->phone }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="phone2" class="w-full md:w-[5vw]">電話番号2</label>
                <input type="text" name="phone2" id="phone2" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->phone2 }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="fax" class="w-full md:w-[5vw]">FAX番号</label>
                <input type="text" name="fax" id="fax" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->fax }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="postal_code" class="w-full md:w-[5vw]">郵便番号</label>
                <input type="text" name="postal_code" id="postal_code" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->postal_code }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="address" class="w-full md:w-[5vw]">住所</label>
                <input type="text" name="address" id="address" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->address }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex justify-end mt-auto gap-x-4 items-center">
                @if(!$isDeleted())
                    <div>
                        <button type="button" id="customer_delete" class="bg-red-500 text-white px-8 py-1 rounded-md">削除</button>
                    </div>
                    <div>
                        <button type="button" id="customer_update" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md cursor-pointer">更新</button>
                    </div>
                @endif
                <div>
                    <a href="{{ route('operator.customer.index') }}" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- モーダル用の非表示要素 -->
<div id="execMode" class="hidden"></div>

<!-- 共通モーダル -->
@include('operator.layouts.widgets.modal')
