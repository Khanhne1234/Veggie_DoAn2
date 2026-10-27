<link rel="stylesheet"
    href="{{ asset('assets/clients/css/chat.css') }}?v={{ filemtime(public_path('assets/clients/css/chat.css')) }}">

<!-- Floating Chat -->
<div id="chat-widget"
    data-logged-in="{{ Auth::check() ? 'true' : 'false' }}"
    data-history-url="{{ route('chat.history') }}"
    data-send-url="{{ route('chat.send') }}">
    <button id="chat-toggle" type="button" aria-label="Mở hỗ trợ trực tuyến"
        aria-controls="chat-box" aria-expanded="false">
        <span aria-hidden="true">💬</span>
    </button>

    <div id="chat-box" class="hidden" role="dialog" aria-label="Hỗ trợ trực tuyến" aria-hidden="true">
        <div id="chat-header">
            <span>Hỗ trợ trực tuyến</span>
            <button id="chat-close" type="button" aria-label="Đóng cửa sổ trò chuyện">&times;</button>
        </div>
        <div id="chat-messages">
            <div class="chat-message bot">
                Xin chào! Tôi là trợ lý ảo của Veggie. Tôi có thể giúp bạn tìm kiếm sản phẩm, kiểm tra đơn hàng hoặc
                tư vấn dinh dưỡng.
            </div>
        </div>
        <div id="chat-input">
            <input type="text" id="message-input" placeholder="Nhập tin nhắn..." maxlength="1000"
                autocomplete="off" aria-label="Nội dung tin nhắn">
            <button id="send-btn" type="button">Gửi</button>
        </div>
    </div>
</div>

<script src="{{ asset('assets/clients/js/chat.js') }}?v={{ filemtime(public_path('assets/clients/js/chat.js')) }}"></script>
