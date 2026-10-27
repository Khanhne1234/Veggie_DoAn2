document.addEventListener('DOMContentLoaded', function () {
    const chatWidget = document.getElementById('chat-widget');
    const chatToggle = document.getElementById('chat-toggle');
    const chatBox = document.getElementById('chat-box');
    const chatClose = document.getElementById('chat-close');
    const chatMessages = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');
    const requiredElements = [chatWidget, chatToggle, chatBox, chatClose, chatMessages, messageInput, sendBtn];

    if (requiredElements.some(element => !element)) {
        console.warn('Chat AI elements not found.');
        return;
    }

    const isLoggedIn = chatWidget.dataset.loggedIn === 'true';
    const guestToken = isLoggedIn ? null : getOrCreateGuestToken();
    const historyUrl = chatWidget.dataset.historyUrl;
    const sendUrl = chatWidget.dataset.sendUrl;
    const welcomeMessage = 'Xin chào! Tôi là trợ lý ảo của Veggie. Tôi có thể giúp bạn tìm kiếm sản phẩm, kiểm tra đơn hàng hoặc tư vấn dinh dưỡng. 🌿';
    let historyPromise = null;
    let isSending = false;

    chatToggle.addEventListener('click', function () {
        const shouldOpen = chatBox.classList.contains('hidden');
        setChatOpen(shouldOpen);

        if (shouldOpen) {
            loadChatHistory();
            messageInput.focus();
        }
    });

    chatClose.addEventListener('click', function () {
        setChatOpen(false);
        chatToggle.focus();
    });

    sendBtn.addEventListener('click', sendMessage);

    messageInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.isComposing) {
            event.preventDefault();
            sendMessage();
        }
    });

    function setChatOpen(isOpen) {
        chatBox.classList.toggle('hidden', !isOpen);
        chatBox.setAttribute('aria-hidden', String(!isOpen));
        chatToggle.setAttribute('aria-expanded', String(isOpen));
        chatToggle.setAttribute('aria-label', isOpen ? 'Đóng hỗ trợ trực tuyến' : 'Mở hỗ trợ trực tuyến');
    }

    function getOrCreateGuestToken() {
        let token = localStorage.getItem('chat_guest_token');

        if (!token) {
            token = 'guest_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now();
            localStorage.setItem('chat_guest_token', token);
        }

        return token;
    }

    function loadChatHistory() {
        if (historyPromise) {
            return historyPromise;
        }

        historyPromise = (async function () {
            try {
                const url = new URL(historyUrl, window.location.origin);

                if (!isLoggedIn && guestToken) {
                    url.searchParams.set('guest_token', guestToken);
                }

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw createHttpError(response.status);
                }

                const data = await response.json();
                chatMessages.replaceChildren();

                if (data.status === 'success' && Array.isArray(data.messages) && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        appendMessage(message.sender, message.message);
                    });
                    return;
                }

                appendMessage('bot', welcomeMessage);
            } catch (error) {
                console.error('Error loading chat history:', error);

                if (!chatMessages.querySelector('.chat-message')) {
                    appendMessage('bot', welcomeMessage);
                }
            }
        })();

        return historyPromise;
    }

    async function sendMessage() {
        const text = messageInput.value.trim();

        if (!text || isSending) {
            return;
        }

        appendMessage('user', text);
        messageInput.value = '';
        setSending(true);
        showTypingIndicator();

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const abortController = new AbortController();
        const timeoutId = window.setTimeout(() => abortController.abort(), 20000);

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken ?? ''
                },
                body: JSON.stringify({
                    message: text,
                    guest_token: guestToken
                }),
                signal: abortController.signal
            });
            const data = await parseJsonResponse(response);

            if (!response.ok) {
                throw createHttpError(response.status, data);
            }

            const reply = data?.bot_message?.message ?? data?.bot_message ?? data?.message;

            if (data?.status !== 'success' || typeof reply !== 'string' || reply.trim() === '') {
                throw createHttpError(502, data);
            }

            appendMessage('bot', reply);
        } catch (error) {
            console.error('Error sending chat message:', error);
            appendMessage('bot', getErrorMessage(error));
        } finally {
            window.clearTimeout(timeoutId);
            hideTypingIndicator();
            setSending(false);
            messageInput.focus();
        }
    }

    async function parseJsonResponse(response) {
        const responseText = await response.text();

        if (!responseText) {
            return {};
        }

        try {
            return JSON.parse(responseText);
        } catch (error) {
            const invalidResponseError = createHttpError(response.status);
            invalidResponseError.code = 'INVALID_JSON';
            throw invalidResponseError;
        }
    }

    function createHttpError(status, data = {}) {
        const error = new Error(data.message ?? 'Chat request failed');
        error.status = status;
        error.data = data;
        return error;
    }

    function getErrorMessage(error) {
        if (error.name === 'AbortError') {
            return 'Phản hồi đang mất nhiều thời gian hơn dự kiến. Bạn vui lòng thử lại nhé! ⏳';
        }

        if (error.status === 419) {
            return 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang rồi gửi lại tin nhắn. 🔄';
        }

        if (error.status === 422) {
            return error.data?.message ?? 'Nội dung tin nhắn chưa hợp lệ. Vui lòng kiểm tra lại.';
        }

        if (error.status >= 500 || error.code === 'INVALID_JSON') {
            return 'Hệ thống hỗ trợ đang tạm thời bận. Vui lòng thử lại sau ít phút. 🌿';
        }

        return 'Không thể kết nối tới hệ thống hỗ trợ. Vui lòng kiểm tra mạng và thử lại. 📡';
    }

    function setSending(sending) {
        isSending = sending;
        sendBtn.disabled = sending;
        sendBtn.setAttribute('aria-busy', String(sending));
        sendBtn.textContent = sending ? 'Đang gửi...' : 'Gửi';
    }

    function appendMessage(sender, text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message ' + (sender === 'user' ? 'user' : 'bot');
        messageDiv.textContent = String(text ?? '');
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function showTypingIndicator() {
        if (document.getElementById('typing-indicator')) {
            return;
        }

        const indicator = document.createElement('div');
        indicator.id = 'typing-indicator';
        indicator.className = 'typing-indicator';
        indicator.setAttribute('aria-label', 'Trợ lý đang trả lời');

        for (let index = 0; index < 3; index += 1) {
            const dot = document.createElement('span');
            dot.className = 'typing-dot';
            indicator.appendChild(dot);
        }

        chatMessages.appendChild(indicator);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        document.getElementById('typing-indicator')?.remove();
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
