document.addEventListener('DOMContentLoaded', function () {
    const chatWidget = document.getElementById('chat-widget');
    const chatToggle = document.getElementById('chat-toggle');
    const chatBox = document.getElementById('chat-box');
    const chatClose = document.getElementById('chat-close');
    const chatMessages = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');

    if (!chatWidget || !chatToggle || !chatBox || !chatClose || !chatMessages || !messageInput || !sendBtn) {
        console.warn('Chat AI elements not found.');
        return;
    }

    const isLoggedIn = chatWidget.getAttribute('data-logged-in') === 'true';
    const guestToken = isLoggedIn ? null : getOrCreateGuestToken();
    let historyLoaded = false;

    // Toggle chat visibility
    chatToggle.addEventListener('click', function () {
        chatBox.classList.toggle('hidden');
        if (!chatBox.classList.contains('hidden')) {
            if (!historyLoaded) {
                loadChatHistory();
            }
            messageInput.focus();
        }
    });

    // Close chat
    chatClose.addEventListener('click', function () {
        chatBox.classList.add('hidden');
    });

    // Send message on button click
    sendBtn.addEventListener('click', sendMessage);

    // Send message on Enter key
    messageInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function getOrCreateGuestToken() {
        let token = localStorage.getItem('chat_guest_token');
        if (!token) {
            token = 'guest_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now();
            localStorage.setItem('chat_guest_token', token);
        }
        return token;
    }

    function loadChatHistory() {
        let url = '/chat/history';
        if (!isLoggedIn && guestToken) {
            url += '?guest_token=' + encodeURIComponent(guestToken);
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                historyLoaded = true;
                if (data.status === 'success' && data.messages.length > 0) {
                    chatMessages.innerHTML = ''; // clear initial welcome/placeholder
                    data.messages.forEach(msg => {
                        appendMessage(msg.sender, msg.message);
                    });
                } else {
                    // Show welcoming message if no history
                    appendMessage('bot', 'Xin chào! Cảm ơn bạn đã ghé thăm Veggie. Tôi có thể giúp gì cho bạn hôm nay? 🌿');
                }
            })
            .catch(error => {
                console.error('Error loading chat history:', error);
                appendMessage('bot', 'Xin chào! Cảm ơn bạn đã ghé thăm Veggie. Tôi có thể giúp gì cho bạn hôm nay? 🌿');
            });
    }

    function sendMessage() {
        const text = messageInput.value.trim();
        if (text === '') return;

        // Display user's message immediately
        appendMessage('user', text);
        messageInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

        // Send to backend
        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                message: text,
                guest_token: guestToken
            })
        })
        .then(response => response.json())
        .then(data => {
            hideTypingIndicator();
            if (data.status === 'success') {
                appendMessage('bot', data.bot_message.message);
            } else {
                appendMessage('bot', 'Xin lỗi, hệ thống đang gặp sự cố. Vui lòng thử lại sau! 😢');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            hideTypingIndicator();
            appendMessage('bot', 'Lỗi kết nối máy chủ. Vui lòng kiểm tra lại kết nối mạng! 📡');
        });
    }

    function appendMessage(sender, text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}`;
        
        // Support formatting with line breaks in responses
        messageDiv.innerHTML = text.replace(/\n/g, '<br>');
        
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function showTypingIndicator() {
        if (document.getElementById('typing-indicator')) return;

        const indicator = document.createElement('div');
        indicator.id = 'typing-indicator';
        indicator.className = 'typing-indicator';
        indicator.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        `;
        chatMessages.appendChild(indicator);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
