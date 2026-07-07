<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemini Chatbot Notifier</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 h-screen flex flex-col justify-center items-center">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg flex flex-col h-[600px] overflow-hidden">
        <div class="bg-blue-600 p-4 text-white font-bold text-center shadow-md">
            🤖 Gemini Chatbot Assistant
        </div>

        <div id="chat-box" class="flex-1 p-4 overflow-y-auto space-y-4 bg-gray-50">
            <div class="flex items-start">
                <div class="bg-blue-100 text-blue-800 p-3 rounded-lg max-w-[80%] shadow-sm">
                    Xin chào! Tôi có thể giúp gì cho bạn? Bạn có thể thử ra lệnh: <i>"Gửi mail báo là Long đang tìm tài
                        liệu"</i> nhé.
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-t border-gray-200 flex gap-2">
            <input type="text" id="user-input" placeholder="Nhập tin nhắn ở đây..."
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
            <button id="send-btn"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-sm">
                Gửi
            </button>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');

        function appendMessage(sender, text, isError = false) {
            const messageWrapper = document.createElement('div');
            messageWrapper.className = sender === 'user' ? 'flex justify-end' : 'flex justify-start';

            let bgClass = 'bg-blue-100 text-blue-800'; 
            if (sender === 'user') {
                bgClass = 'bg-blue-600 text-white';
            } else if (isError) {
                bgClass = 'bg-red-100 text-red-800';
            } else if (text.includes('mail thông báo') || text.includes('gửi mail')) {
                bgClass = 'bg-green-100 text-green-800 font-semibold';
            }

            messageWrapper.innerHTML = `
                <div class="${bgClass} p-3 rounded-lg max-w-[80%] shadow-sm whitespace-pre-line">
                    ${text}
                </div>
            `;
            chatBox.appendChild(messageWrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        async function sendMessage() {
            const message = userInput.value.trim();
            if (!message) return;

            appendMessage('user', message);
            userInput.value = '';

            const loadingId = 'loading-' + Date.now();
            const loadingWrapper = document.createElement('div');
            loadingWrapper.id = loadingId;
            loadingWrapper.className = 'flex justify-start';
            loadingWrapper.innerHTML =
                `<div class="bg-gray-200 text-gray-600 p-3 rounded-lg max-w-[80%] animate-pulse">Đang suy nghĩ...</div>`;
            chatBox.appendChild(loadingWrapper);
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                const response = await fetch('/api/chatbot/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        message: message
                    })
                });

                const data = await response.json();

                const loadingElement = document.getElementById(loadingId);
                if (loadingElement) {
                    loadingElement.remove();
                }

                appendMessage('bot', data.reply);

            } catch (error) {
                const loadingElement = document.getElementById(loadingId);
                if (loadingElement) {
                    loadingElement.remove();
                }
                appendMessage('bot', 'Lỗi: Không thể nhận phản hồi từ server.', true);
            }
        }

        sendBtn.addEventListener('click', sendMessage);

        userInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>

</html>
