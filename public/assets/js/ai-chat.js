// AI Chat Widget JavaScript - Enhanced Version
(function() {
    'use strict';

    // DOM Elements
    const chatWidget = document.getElementById('ai-chat-widget');
    const chatButton = document.getElementById('ai-chat-button');
    const chatBox = document.getElementById('ai-chat-box');
    const chatClose = document.getElementById('ai-chat-close');
    const chatClear = document.getElementById('ai-chat-clear');
    const chatMinimize = document.getElementById('ai-chat-minimize');
    const chatMessages = document.getElementById('ai-chat-messages');
    const chatTextarea = document.getElementById('ai-chat-textarea');
    const chatSend = document.getElementById('ai-chat-send');
    const quickSuggestions = document.getElementById('ai-quick-suggestions');

    if (!chatWidget || !chatButton || !chatBox) {
        console.warn('AI Chat Widget elements not found');
        return;
    }

    // State
    let isChatOpen = false;
    let isProcessing = false;
    let conversationHistory = [];
    let isMinimized = false;

    // Lưu lịch sử chat (tối đa 10 tin) vào localStorage với TTL 24h
    const HISTORY_KEY = 'ai_chat_history_v2';
    const HISTORY_TTL_MS = 24 * 60 * 60 * 1000;

    // Giải mã HTML entities (vd: &lt;br&gt;)
    function decodeEntities(text) {
        if (!text) return '';
        const textarea = document.createElement('textarea');
        textarea.innerHTML = text;
        return textarea.value;
    }

    // Chuẩn hóa và loại bỏ HTML thừa từ phản hồi AI để tránh hiển thị thẻ thô
    function sanitizeText(text) {
        if (!text) return '';
        let clean = decodeEntities(String(text));
        clean = clean.replace(/<br\s*\/?\>/gi, '\n');
        clean = clean.replace(/<\/p\s*>/gi, '\n');
        clean = clean.replace(/<p[^>]*>/gi, '');
        clean = clean.replace(/<\/(?:strong|b)[^>]*>/gi, '**');
        clean = clean.replace(/<(?:strong|b)[^>]*>/gi, '**');
        clean = clean.replace(/<\/(?:em|i)[^>]*>/gi, '*');
        clean = clean.replace(/<(?:em|i)[^>]*>/gi, '*');
        clean = clean.replace(/&nbsp;/gi, ' ');
        clean = clean.replace(/<[^>]+>/g, '');
        // Gỡ markdown heading (#, ##) và danh sách để tránh hiển thị ký hiệu thô
        clean = clean.replace(/^\s*#{1,6}\s*/gm, '');
        clean = clean.replace(/^\s*[-*]\s+/gm, '• ');
        // Loại bỏ mọi ký tự < > còn sót
        clean = clean.replace(/[<>]/g, '');
        clean = clean.replace(/\r\n|\r/g, '\n');
        clean = clean.replace(/\n{3,}/g, '\n\n');
        return clean.trim();
    }

    function saveHistory() {
        if (!window.localStorage) return;
        try {
            const items = conversationHistory.slice(-10);
            localStorage.setItem(HISTORY_KEY, JSON.stringify({ ts: Date.now(), items }));
        } catch (err) {
            console.warn('Không thể lưu lịch sử chat:', err);
        }
    }

    function loadHistory() {
        if (!window.localStorage) return;
        try {
            const raw = localStorage.getItem(HISTORY_KEY);
            if (!raw) return;
            const payload = JSON.parse(raw);
            if (!payload || !payload.items || !payload.ts) {
                localStorage.removeItem(HISTORY_KEY);
                return;
            }
            if (Date.now() - payload.ts > HISTORY_TTL_MS) {
                localStorage.removeItem(HISTORY_KEY);
                return;
            }
            conversationHistory = payload.items.map(msg => {
                if (!msg || !msg.text) return msg;
                return { ...msg, text: sanitizeText(msg.text) };
            });
            conversationHistory.forEach(msg => {
                if (msg && msg.text && msg.role) {
                    addMessage(msg.text, msg.role === 'user');
                }
            });
        } catch (err) {
            console.warn('Không thể khôi phục lịch sử chat:', err);
        }
    }

    // Khôi phục lịch sử chat nếu có
    loadHistory();
    renderPageSuggestions();

    // Gợi ý nhanh theo trang
    function getPageSuggestions() {
        const ctx = getPageContext();
        if (ctx.includes('Gói lưu trữ')) {
            return [
                'Các gói lưu trữ có gì?',
                'Cách nâng cấp gói và thanh toán',
                'Dùng thử gói Premium thế nào?'
            ];
        }
        if (ctx.includes('Nhóm')) {
            return [
                'Cách tạo nhóm làm việc',
                'Mời thành viên vào nhóm',
                'Chia sẻ file cho nhóm ra sao?'
            ];
        }
        if (ctx.includes('Quản lý File') || ctx.includes('Quản lý Thư mục')) {
            return [
                'Cách upload nhiều file cùng lúc?',
                'Tạo thư mục mới và đổi màu',
                'Chia sẻ file bằng link công khai'
            ];
        }
        if (ctx.includes('File được chia sẻ')) {
            return [
                'Xem ai đã chia sẻ file cho tôi',
                'Cách tải file được chia sẻ',
                'Cách dừng chia sẻ một file'
            ];
        }
        if (ctx.includes('Yêu thích')) {
            return [
                'Cách đánh dấu file yêu thích',
                'Gỡ khỏi danh sách yêu thích',
                'Tìm nhanh file quan trọng'
            ];
        }
        if (ctx.includes('Thùng rác')) {
            return [
                'Khôi phục file đã xóa',
                'Xóa vĩnh viễn file/thư mục',
                'Thùng rác giữ file bao lâu?'
            ];
        }
        if (ctx.includes('Dashboard')) {
            return [
                'Xem nhanh dung lượng còn lại',
                'Những file gần đây là gì?',
                'Cách xem thống kê hoạt động'
            ];
        }
        return [
            'Làm thế nào để upload file?',
            'Cách chia sẻ file bằng link',
            'Cách tạo nhóm và mời thành viên'
        ];
    }

    function renderPageSuggestions() {
        if (!quickSuggestions) return;
        const items = getPageSuggestions();
        quickSuggestions.innerHTML = '';
        items.forEach(text => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'suggestion-btn';
            btn.setAttribute('data-message', text);
            btn.textContent = text;
            quickSuggestions.appendChild(btn);
        });
        quickSuggestions.style.display = items.length ? 'flex' : 'none';
    }

    // Toggle chat box
    function toggleChat() {
        isChatOpen = !isChatOpen;
        chatBox.style.display = isChatOpen ? 'flex' : 'none';
        
        if (isChatOpen) {
            chatTextarea.focus();
            scrollToBottom();
        }
    }

    // Close chat
    function closeChat() {
        isChatOpen = false;
        isMinimized = false;
        chatBox.style.display = 'none';
        chatBox.classList.remove('minimized');
    }

    // Toggle minimize
    function toggleMinimize() {
        isMinimized = !isMinimized;
        if (isMinimized) {
            chatBox.classList.add('minimized');
            chatMinimize.innerHTML = '<i class="las la-window-maximize"></i>';
            chatMinimize.title = 'Mở rộng';
        } else {
            chatBox.classList.remove('minimized');
            chatMinimize.innerHTML = '<i class="las la-minus"></i>';
            chatMinimize.title = 'Thu nhỏ';
        }
    }

    // Clear chat history
    function clearChat() {
        if (confirm('Bạn có chắc muốn xóa toàn bộ lịch sử chat?')) {
            const messages = chatMessages.querySelectorAll('.ai-message, .user-message');
            messages.forEach((msg, index) => {
                if (index > 0) {
                    msg.remove();
                }
            });
            
            if (quickSuggestions) {
                renderPageSuggestions();
            }
            
            conversationHistory = [];
            if (window.localStorage) {
                localStorage.removeItem('ai_chat_history');
                localStorage.removeItem('ai_chat_history_v2');
            }
            scrollToBottom();
        }
    }

    // Scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Add message (full render)
    function addMessage(content, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = isUser ? 'user-message' : 'ai-message';
        
        const avatarDiv = document.createElement('div');
        avatarDiv.className = isUser ? 'user-avatar' : 'ai-avatar';
        avatarDiv.innerHTML = isUser ? '<i class="las la-user"></i>' : '<i class="las la-robot"></i>';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = isUser ? 'user-message-content' : 'ai-message-content';
        contentDiv.innerHTML = formatMessage(content);
        
        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(contentDiv);
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    // Add message with streaming effect for AI
    function addMessageStreaming(content) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'ai-message';

        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'ai-avatar';
        avatarDiv.innerHTML = '<i class="las la-robot"></i>';

        const contentDiv = document.createElement('div');
        contentDiv.className = 'ai-message-content';
        contentDiv.innerHTML = '<p></p>';

        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(contentDiv);
        chatMessages.appendChild(messageDiv);
        scrollToBottom();

        const target = contentDiv.querySelector('p');
        const formatted = formatMessage(content).replace(/^<p>|<\/p>$/g, '');
        let idx = 0;
        const tick = () => {
            if (idx >= formatted.length) return;
            target.innerHTML = formatted.substring(0, idx + 1);
            idx += 1;
            scrollToBottom();
            if (idx < formatted.length) {
                requestAnimationFrame(tick);
            }
        };
        requestAnimationFrame(tick);
    }

    // Format message
    function formatMessage(text) {
        let safeText = sanitizeText(text);
        safeText = safeText.replace(/\n/g, '<br>');
        safeText = safeText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        safeText = safeText.replace(/\*(.*?)\*/g, '<em>$1</em>');
        safeText = safeText.replace(/`([^`]+)`/g, '<code style="background: #f0f0f0; padding: 2px 6px; border-radius: 4px;">$1</code>');
        return '<p>' + safeText + '</p>';
    }

    // Show typing indicator
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'ai-message';
        typingDiv.id = 'typing-indicator';
        
        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'ai-avatar';
        avatarDiv.innerHTML = '<i class="las la-robot"></i>';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'ai-message-content';
        contentDiv.innerHTML = '<div class="ai-typing-indicator"><span></span><span></span><span></span></div>';
        
        typingDiv.appendChild(avatarDiv);
        typingDiv.appendChild(contentDiv);
        chatMessages.appendChild(typingDiv);
        scrollToBottom();
    }

    // Remove typing indicator
    function removeTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    // Get page context
    function getPageContext() {
        const path = window.location.pathname;
        if (path.includes('/dashboard')) return 'Dashboard';
        if (path.includes('/files')) return 'Quản lý File';
        if (path.includes('/folders')) return 'Quản lý Thư mục';
        if (path.includes('/shared')) return 'File được chia sẻ';
        if (path.includes('/favorites')) return 'Yêu thích';
        if (path.includes('/trash')) return 'Thùng rác';
        if (path.includes('/groups')) return 'Nhóm làm việc';
        if (path.includes('/storage/plans')) return 'Gói lưu trữ';
        if (path.includes('/profile')) return 'Hồ sơ cá nhân';
        return '';
    }

    // Send message
    async function sendMessage() {
        const message = chatTextarea.value.trim();
        
        if (!message || isProcessing) return;

        if (quickSuggestions) {
            quickSuggestions.style.display = 'none';
        }

        addMessage(message, true);
        conversationHistory.push({ role: 'user', text: message });
        if (conversationHistory.length > 10) {
            conversationHistory = conversationHistory.slice(-10);
        }
        saveHistory();
        
        chatTextarea.value = '';
        chatTextarea.style.height = 'auto';
        
        isProcessing = true;
        chatSend.disabled = true;
        chatTextarea.disabled = true;
        showTypingIndicator();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const response = await fetch('/ai-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    message: message,
                    history: conversationHistory,
                    context: getPageContext()
                })
            });

            const data = await response.json();
            removeTypingIndicator();

            if (data.success) {
                const reply = sanitizeText(data.reply);
                try {
                    addMessageStreaming(reply);
                } catch (e) {
                    addMessage(reply);
                }
                conversationHistory.push({ role: 'model', text: reply });
                saveHistory();
            } else {
                addMessage(data.message || 'Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại sau.');
            }
        } catch (error) {
            console.error('AI Chat Error:', error);
            removeTypingIndicator();
            addMessage('Xin lỗi, không thể kết nối với AI. Vui lòng thử lại sau.');
        } finally {
            isProcessing = false;
            chatSend.disabled = false;
            chatTextarea.disabled = false;
            chatTextarea.focus();
        }
    }

    // Auto-resize textarea
    function autoResizeTextarea() {
        chatTextarea.style.height = 'auto';
        chatTextarea.style.height = Math.min(chatTextarea.scrollHeight, 100) + 'px';
    }

    // Event Listeners
    chatButton.addEventListener('click', toggleChat);
    chatClose.addEventListener('click', closeChat);
    
    if (chatClear) {
        chatClear.addEventListener('click', clearChat);
    }
    
    if (chatMinimize) {
        chatMinimize.addEventListener('click', toggleMinimize);
    }
    
    chatSend.addEventListener('click', sendMessage);

    // Quick suggestions
    if (quickSuggestions) {
        quickSuggestions.addEventListener('click', function(e) {
            const btn = e.target.closest('.suggestion-btn');
            if (btn) {
                const message = btn.getAttribute('data-message');
                if (message) {
                    chatTextarea.value = message;
                    chatTextarea.focus();
                    sendMessage();
                }
            }
        });
    }

    // Textarea events
    chatTextarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    chatTextarea.addEventListener('input', autoResizeTextarea);

    // Escape to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isChatOpen) {
            closeChat();
        }
    });

    console.log('✅ AI Chat Widget Enhanced initialized');
})();
