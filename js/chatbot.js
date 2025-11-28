/**
 * Chatbot Widget JavaScript
 */

$(document).ready(function() {
    
    // Initialize chatbot
    initChatbot();
    
    /**
     * Initialize chatbot widget
     */
    function initChatbot() {
        // Add welcome message
        addBotMessage("Hello! I'm BotaniBot 🤖🌿, your wellness assistant! Ask me about products, health tips, orders, or anything else!");
    }
    
    /**
     * Toggle chatbot window
     */
    $('#chatbot-toggle').on('click', function() {
        $('#chatbot-window').toggleClass('show');
        
        // Focus on input when opened
        if ($('#chatbot-window').hasClass('show')) {
            $('#chatbot-input').focus();
        }
    });
    
    /**
     * Close chatbot
     */
    $('#chatbot-close').on('click', function() {
        $('#chatbot-window').removeClass('show');
    });
    
    /**
     * Minimize chatbot
     */
    $('#chatbot-minimize').on('click', function() {
        $('#chatbot-window').removeClass('show');
    });
    
    /**
     * Send message on Enter key
     */
    $('#chatbot-input').on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    /**
     * Send message on button click
     */
    $('#chatbot-send').on('click', function() {
        sendMessage();
    });
    
    /**
     * Send message to server
     */
    function sendMessage() {
        const message = $('#chatbot-input').val().trim();
        
        if (message === '') {
            return;
        }
        
        // Add user message to chat
        addUserMessage(message);
        
        // Clear input
        $('#chatbot-input').val('');
        
        // Show typing indicator
        showTypingIndicator();
        
        // Send to server
        $.ajax({
            url: 'actions/chatbot_action.php',
            type: 'POST',
            data: { message: message },
            dataType: 'json',
            success: function(response) {
                // Remove typing indicator
                removeTypingIndicator();
                
                if (response.success) {
                    // Add bot response
                    addBotMessage(response.response);
                } else {
                    addBotMessage("Sorry, I encountered an error. Please try again! 😅");
                }
            },
            error: function() {
                removeTypingIndicator();
                addBotMessage("Oops! Something went wrong. Please try again later.");
            }
        });
    }
    
    /**
     * Add user message to chat
     */
    function addUserMessage(message) {
        const timestamp = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        const html = `
            <div class="chat-message user-message">
                <div class="message-content">
                    <p>${escapeHtml(message)}</p>
                    <span class="message-time">${timestamp}</span>
                </div>
            </div>
        `;
        
        $('#chatbot-messages').append(html);
        scrollToBottom();
    }
    
    /**
     * Add bot message to chat
     */
    function addBotMessage(message) {
        const timestamp = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        const html = `
            <div class="chat-message bot-message">
                <div class="bot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <p>${message}</p>
                    <span class="message-time">${timestamp}</span>
                </div>
            </div>
        `;
        
        $('#chatbot-messages').append(html);
        scrollToBottom();
    }
    
    /**
     * Show typing indicator
     */
    function showTypingIndicator() {
        const html = `
            <div class="chat-message bot-message typing-indicator" id="typing-indicator">
                <div class="bot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        
        $('#chatbot-messages').append(html);
        scrollToBottom();
    }
    
    /**
     * Remove typing indicator
     */
    function removeTypingIndicator() {
        $('#typing-indicator').remove();
    }
    
    /**
     * Scroll chat to bottom
     */
    function scrollToBottom() {
        const chatMessages = $('#chatbot-messages');
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }
    
    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
    
    /**
     * Quick action buttons
     */
    $(document).on('click', '.quick-action-btn', function() {
        const action = $(this).data('action');
        $('#chatbot-input').val(action);
        sendMessage();
    });
});