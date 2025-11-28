<!-- Chatbot Widget -->
<style>
    /* Chatbot Styles */
    #chatbot-toggle {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #4CAF50, #66BB6A);
        border-radius: 50%;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 999;
        transition: transform 0.3s;
    }
    
    #chatbot-toggle:hover {
        transform: scale(1.1);
    }
    
    #chatbot-toggle i {
        color: white;
        font-size: 28px;
    }
    
    #chatbot-window {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 380px;
        height: 550px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.3);
        display: none;
        flex-direction: column;
        z-index: 999;
        overflow: hidden;
    }
    
    #chatbot-window.show {
        display: flex;
    }
    
    .chatbot-header {
        background: linear-gradient(135deg, #4CAF50, #66BB6A);
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chatbot-header h4 {
        margin: 0;
        font-size: 18px;
    }
    
    .chatbot-header-actions {
        display: flex;
        gap: 10px;
    }
    
    .chatbot-header-actions button {
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 18px;
        padding: 5px;
    }
    
    #chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f8f9fa;
    }
    
    .chat-message {
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
        animation: fadeIn 0.3s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .user-message {
        justify-content: flex-end;
    }
    
    .bot-message {
        justify-content: flex-start;
    }
    
    .bot-avatar {
        width: 35px;
        height: 35px;
        background: #4CAF50;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }
    
    .message-content {
        max-width: 70%;
        background: white;
        padding: 12px 15px;
        border-radius: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .user-message .message-content {
        background: #4CAF50;
        color: white;
    }
    
    .message-content p {
        margin: 0;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .message-time {
        font-size: 10px;
        opacity: 0.7;
        display: block;
        margin-top: 5px;
    }
    
    .typing-indicator .message-content {
        background: white;
        padding: 15px 20px;
    }
    
    .typing-dots {
        display: flex;
        gap: 5px;
    }
    
    .typing-dots span {
        width: 8px;
        height: 8px;
        background: #4CAF50;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }
    
    .typing-dots span:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .typing-dots span:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-10px); }
    }
    
    .chatbot-input-area {
        padding: 15px;
        background: white;
        border-top: 1px solid #e0e0e0;
        display: flex;
        gap: 10px;
    }
    
    #chatbot-input {
        flex: 1;
        border: 2px solid #e0e0e0;
        border-radius: 25px;
        padding: 10px 15px;
        font-size: 14px;
        outline: none;
        resize: none;
    }
    
    #chatbot-input:focus {
        border-color: #4CAF50;
    }
    
    #chatbot-send {
        background: #4CAF50;
        color: white;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
    }
    
    #chatbot-send:hover {
        background: #45a049;
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        #chatbot-window {
            width: 100%;
            height: 100%;
            bottom: 0;
            right: 0;
            border-radius: 0;
        }
        
        #chatbot-toggle {
            bottom: 20px;
            right: 20px;
        }
    }
</style>

<!-- Chatbot Toggle Button -->
<div id="chatbot-toggle">
    <i class="fas fa-comments"></i>
</div>

<!-- Chatbot Window -->
<div id="chatbot-window">
    <!-- Header -->
    <div class="chatbot-header">
        <div>
            <h4><i class="fas fa-robot"></i> BotaniBot</h4>
            <small>Your Wellness Assistant</small>
        </div>
        <div class="chatbot-header-actions">
            <button id="chatbot-minimize" title="Minimize">
                <i class="fas fa-minus"></i>
            </button>
            <button id="chatbot-close" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <!-- Messages -->
    <div id="chatbot-messages"></div>
    
    <!-- Input Area -->
    <div class="chatbot-input-area">
        <textarea id="chatbot-input" 
                  rows="1" 
                  placeholder="Type your message..."></textarea>
        <button id="chatbot-send">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<!-- Load Chatbot JS -->
<script src="js/chatbot.js"></script>