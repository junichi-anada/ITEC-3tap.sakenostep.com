<div class="border py-4 w-full h-full">
    <div class="flex flex-col gap-y-4 h-full w-full px-4">
        <div class="flex flex-row items-center gap-x-4">
            <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">LINEメッセージ送信</h3>
        </div>
        @if($lineUser && $lineUser->is_linked)
            <div class="flex flex-col gap-y-4">
                <textarea
                    id="line_message"
                    name="line_message"
                    class="w-full h-32 border border-gray-300 rounded-md p-2 resize-none"
                    placeholder="送信するメッセージを入力してください"
                ></textarea>
                <div class="flex justify-end">
                    <button
                        type="button"
                        id="send_line_message"
                        class="bg-[#00B900] text-white px-8 py-2 rounded-md hover:bg-[#009900]"
                    >
                        送信
                    </button>
                </div>
            </div>
        @else
            <!-- <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                <p class="text-yellow-700">
                    Debug Info:<br>
                    lineUser: {{ $lineUser ? 'exists' : 'null' }}<br>
                    @if($lineUser)
                        is_linked: {{ $lineUser->is_linked ? 'true' : 'false' }}
                    @endif
                </p>
            </div> -->
            <div class="bg-gray-50 border border-gray-200 rounded p-4">
                <p class="text-gray-700">このユーザーはLINEと連携されていません。</p>
            </div>
        @endif
    </div>
</div>

@if($lineUser && $lineUser->is_linked)
    <script type="module">
        import { makeLineMessageSuccessModal, makeLineMessageFailModal } from "/js/modal/operator/customer/line-message.js";

        // イベントリスナーを安全に1回だけ登録するためのユーティリティ関数
        function addEventListenerOnce(element, eventType, handler) {
            const handlerId = `_${eventType}Handler`;
            if (!element[handlerId]) {
                element[handlerId] = true;
                const wrappedHandler = async function(event) {
                    // イベントの伝播を停止
                    event.preventDefault();
                    event.stopPropagation();
                    
                    console.log('Handler execution started', {
                        timestamp: new Date().toISOString(),
                        elementId: element.id,
                        handlerId: handlerId
                    });

                    await handler.call(this, event);

                    console.log('Handler execution completed', {
                        timestamp: new Date().toISOString(),
                        elementId: element.id,
                        handlerId: handlerId
                    });
                };
                
                element.addEventListener(eventType, wrappedHandler);
                console.log(`Event listener '${eventType}' added to element`, {
                    elementId: element.id,
                    timestamp: new Date().toISOString(),
                    handlerId: handlerId
                });
            } else {
                console.log(`Event listener '${eventType}' already exists`, {
                    elementId: element.id,
                    timestamp: new Date().toISOString(),
                    handlerId: handlerId
                });
            }
        }

        const sendButton = document.getElementById('send_line_message');
        if (sendButton) {
            addEventListenerOnce(sendButton, 'click', async function(event) {
                // クリックイベントのデバッグログ
                console.log('LINE message button clicked', {
                    timestamp: new Date().toISOString(),
                    buttonId: this.id
                });

                const message = document.getElementById('line_message').value;
                if (!message.trim()) {
                    makeLineMessageFailModal('メッセージを入力してください。');
                    return;
                }

                sendButton.disabled = true;
                sendButton.classList.add('opacity-50');

                try {
                    const userCode = document.querySelector('input[name="user_code"]').value;
                    const token = document.querySelector('input[name="_token"]').value;

                    const response = await fetch('/operator/customer/line/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            user_code: userCode,
                            message: message
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        makeLineMessageSuccessModal();
                        document.getElementById('line_message').value = '';
                    } else {
                        makeLineMessageFailModal(data.message || 'メッセージの送信に失敗しました。');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    makeLineMessageFailModal('エラーが発生しました。');
                } finally {
                    sendButton.disabled = false;
                    sendButton.classList.remove('opacity-50');
                }
            });
        }
    </script>
@endif
