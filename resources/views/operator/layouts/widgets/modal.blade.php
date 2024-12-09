<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div id="modalContent" class="bg-white p-6 rounded-lg shadow-lg w-11/12 max-w-md">
        <h2 class="text-2xl font-bold mb-4" id="modalTitle">モーダルタイトル</h2>
        <div class="mb-4" id="modalBody">これはモーダルの内容です。ここに必要な情報や操作を追加してください。</div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="flex justify-center gap-x-8" id="modalFooter">
            <button id="execModal" class="bg-[#F4CF41] text-white px-4 py-2 rounded hover:bg-[#E5C03C]">
                実行ボタン
            </button>
            <button id="cancelModal" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                キャンセルボタン
            </button>
        </div>
    </div>
</div>
<!-- //Modal -->
