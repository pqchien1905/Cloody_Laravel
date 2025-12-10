<!-- AI Chat Widget -->
<div id="ai-chat-widget">
    <!-- Chat Button -->
    <button id="ai-chat-button" class="ai-chat-btn" aria-label="Open AI Chat">
        <i class="las la-comments"></i>
    </button>

    <!-- Chat Box -->
    <div id="ai-chat-box" class="ai-chat-box" style="display: none;">
        <div class="ai-chat-header">
            <div class="d-flex align-items-center">
                <i class="las la-robot mr-2"></i>
                <h6 class="mb-0">AI Assistant</h6>
            </div>
            <div class="d-flex align-items-center" style="gap: 8px;">
                <button id="ai-chat-clear" class="btn-header-action" aria-label="Clear chat" title="Xóa lịch sử chat">
                    <i class="las la-trash"></i>
                </button>
                <button id="ai-chat-minimize" class="btn-header-action" aria-label="Minimize" title="Thu nhỏ">
                    <i class="las la-minus"></i>
                </button>
                <button id="ai-chat-close" class="btn-close-chat" aria-label="Close chat">
                    <i class="las la-times"></i>
                </button>
            </div>
        </div>

        <div id="ai-chat-messages" class="ai-chat-messages">
            <div class="ai-message">
                <div class="ai-avatar">
                    <i class="las la-robot"></i>
                </div>
                <div class="ai-message-content">
                    <p>Xin chào! Tôi là trợ lý AI của Cloody. Tôi có thể giúp gì cho bạn?</p>
                </div>
            </div>
            
            <!-- Quick Suggestions -->
            <div class="ai-quick-suggestions" id="ai-quick-suggestions">
                <button class="suggestion-btn" data-message="Làm thế nào để upload file?">
                    <i class="las la-upload"></i> Upload file
                </button>
                <button class="suggestion-btn" data-message="Hướng dẫn tạo nhóm làm việc">
                    <i class="las la-users"></i> Tạo nhóm
                </button>
                <button class="suggestion-btn" data-message="Các gói lưu trữ có gì?">
                    <i class="las la-box"></i> Gói lưu trữ
                </button>
                <button class="suggestion-btn" data-message="Cách chia sẻ file">
                    <i class="las la-share"></i> Chia sẻ
                </button>
            </div>
        </div>

        <div class="ai-chat-input">
            <textarea 
                id="ai-chat-textarea" 
                placeholder="Nhập tin nhắn..." 
                rows="1"
                maxlength="2000"
            ></textarea>
            <button id="ai-chat-send" class="btn-send-chat" aria-label="Send message">
                <i class="las la-paper-plane"></i>
            </button>
        </div>

        <div class="ai-chat-footer">
            <small class="text-muted">Powered by Google Gemini AI</small>
        </div>
    </div>
</div>

<style>
/* AI Chat Widget Styles */
#ai-chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.ai-chat-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-chat-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.ai-chat-btn:active {
    transform: scale(0.95);
}

.ai-chat-box {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    max-width: calc(100vw - 40px);
    height: 500px;
    max-height: calc(100vh - 140px);
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
    transition: height 0.3s ease;
}

.ai-chat-box.minimized {
    height: 60px;
}

.ai-chat-box.minimized .ai-chat-messages,
.ai-chat-box.minimized .ai-chat-input,
.ai-chat-box.minimized .ai-chat-footer {
    display: none;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.ai-chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
}

.ai-chat-header h6 {
    font-weight: 600;
    font-size: 16px;
}

.btn-close-chat, .btn-header-action {
    background: transparent;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s ease;
}

.btn-close-chat:hover, .btn-header-action:hover {
    background: rgba(255, 255, 255, 0.2);
}

.btn-header-action {
    font-size: 18px;
}

.ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
}

.ai-quick-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 16px;
    padding: 0 4px;
}

.suggestion-btn {
    background: white;
    border: 1px solid #e0e0e0;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #555;
    font-family: var(--font-family-base);
}

.suggestion-btn:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.suggestion-btn i {
    font-size: 16px;
}

.ai-message, .user-message {
    display: flex;
    margin-bottom: 16px;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.user-message {
    flex-direction: row-reverse;
}

.ai-avatar, .user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.ai-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-right: 12px;
}

.user-avatar {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    margin-left: 12px;
}

.ai-message-content, .user-message-content {
    background: white;
    padding: 12px 16px;
    border-radius: 12px;
    max-width: 75%;
    word-wrap: break-word;
}

.user-message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.ai-message-content p, .user-message-content p {
    margin: 0;
    line-height: 1.5;
    font-size: 14px;
}

.ai-chat-input {
    padding: 16px;
    background: white;
    border-top: 1px solid #e9ecef;
    display: flex;
    align-items: flex-end;
    gap: 12px;
}

#ai-chat-textarea {
    flex: 1;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    padding: 10px 16px;
    font-size: 14px;
    resize: none;
    max-height: 100px;
    overflow-y: auto;
    font-family: var(--font-family-base);
    transition: border-color 0.2s ease;
}

#ai-chat-textarea:focus {
    outline: none;
    border-color: #667eea;
}

.btn-send-chat {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.btn-send-chat:hover {
    transform: scale(1.1);
}

.btn-send-chat:active {
    transform: scale(0.95);
}

.btn-send-chat:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.ai-chat-footer {
    padding: 8px 16px;
    background: white;
    border-top: 1px solid #e9ecef;
    text-align: center;
}

.ai-typing-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 12px 16px;
}

.ai-typing-indicator span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #667eea;
    animation: typing 1.4s infinite;
}

.ai-typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.ai-typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        opacity: 0.3;
        transform: translateY(0);
    }
    30% {
        opacity: 1;
        transform: translateY(-10px);
    }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    #ai-chat-widget {
        bottom: 15px;
        right: 15px;
    }

    .ai-chat-box {
        width: calc(100vw - 30px);
        height: calc(100vh - 120px);
        bottom: 70px;
    }

    .ai-chat-btn {
        width: 56px;
        height: 56px;
        font-size: 26px;
    }
}

/* Scrollbar Styling */
.ai-chat-messages::-webkit-scrollbar {
    width: 6px;
}

.ai-chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.ai-chat-messages::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 3px;
}

.ai-chat-messages::-webkit-scrollbar-thumb:hover {
    background: #764ba2;
}
</style>
