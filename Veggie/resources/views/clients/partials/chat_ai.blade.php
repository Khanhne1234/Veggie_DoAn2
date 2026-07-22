<link rel="stylesheet" href="{{ asset('assets/clients/css/chat.css') }}">

<!-- Floating Chat -->
<div id="chat-widget" data-logged-in="{{ Auth::check() ? 'true' : 'false' }}">
    <div id="chat-toggle">💬</div>

    <div id="chat-box" class="hidden">
        <div id="chat-header">
            <span>Hỗ trợ trực tuyến</span>
            <button id="chat-close">&#9587;</button>
        </div>
        <div id="chat-messages">
        </div>
        <div id="chat-input">
            <input type="text" id="message-input" placeholder="Nhập tin nhắn...">
            <button id="send-btn">Gửi</button>
        </div>
    </div>
</div>

<script src="{{ asset('assets/clients/js/chat.js') }}"></script>
