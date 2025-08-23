<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://jules.agent
 * @since      0.1.0
 *
 * @package    Offline_AI_Chatbot
 * @subpackage Offline_AI_Chatbot/public/partials
 */
?>

<!-- Chatbot -->
<div id="oac-chat-container" class="oac-chat-container">
    <div id="oac-chat-header" class="oac-chat-header">
        <span>AI Assistant</span>
        <button id="oac-close-chat" class="oac-close-chat">&times;</button>
    </div>
    <div id="oac-chat-body" class="oac-chat-body">
        <!-- Messages will be appended here -->
        <div class="oac-message oac-message-bot">
            <p>Hello! How can I help you today?</p>
        </div>
    </div>
    <div id="oac-chat-footer" class="oac-chat-footer">
        <input type="text" id="oac-chat-input" placeholder="Ask a question...">
        <button id="oac-send-btn">Send</button>
    </div>
</div>

<button id="oac-open-chat-btn" class="oac-open-chat-btn">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
</button>
