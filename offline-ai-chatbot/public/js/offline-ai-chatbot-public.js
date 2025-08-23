(function( $ ) {
	'use strict';

	$(document).ready(function() {
		const chatContainer = $('#oac-chat-container');
		const openChatBtn = $('#oac-open-chat-btn');
		const closeChatBtn = $('#oac-close-chat');
		const chatBody = $('#oac-chat-body');
		const chatInput = $('#oac-chat-input');
		const sendBtn = $('#oac-send-btn');

		// Hide chat window by default
		chatContainer.hide();

		openChatBtn.on('click', function() {
			chatContainer.fadeIn();
			openChatBtn.fadeOut();
		});

		closeChatBtn.on('click', function() {
			chatContainer.fadeOut();
			openChatBtn.fadeIn();
		});

		sendBtn.on('click', function() {
			sendMessage();
		});

		chatInput.on('keypress', function(e) {
			if (e.which === 13) { // Enter key
				e.preventDefault();
				sendMessage();
			}
		});

		function sendMessage() {
			const userMessage = chatInput.val().trim();
			if (userMessage === '') {
				return;
			}

			appendMessage(userMessage, 'user');
			chatInput.val('');
			showTypingIndicator();

			$.ajax({
				url: oac_ajax_object.ajax_url,
				type: 'POST',
				data: {
					action: 'oac_get_bot_response',
					nonce: oac_ajax_object.nonce,
					message: userMessage
				},
				success: function(response) {
					removeTypingIndicator();
					if (response.success && response.data.message) {
						// Using .text() to prevent rendering any HTML that might be in the response
						appendMessage($('<p/>').text(response.data.message).html(), 'bot');
					} else {
						appendMessage('Sorry, something went wrong. Please try again.', 'bot');
					}
				},
				error: function() {
					removeTypingIndicator();
					appendMessage('Error connecting to the server.', 'bot');
				}
			});
		}

		function appendMessage(message, sender) {
			const messageClass = sender === 'user' ? 'oac-message-user' : 'oac-message-bot';
			// The message is already sanitized (or is static text), so we can append it.
			const messageHtml = `<div class="oac-message ${messageClass}"><p>${message}</p></div>`;
			chatBody.append(messageHtml);
			chatBody.scrollTop(chatBody[0].scrollHeight); // Auto-scroll to bottom
		}

		function showTypingIndicator() {
			const typingHtml = `<div id="oac-typing-indicator" class="oac-message oac-message-bot"><p>Typing...</p></div>`;
			chatBody.append(typingHtml);
			chatBody.scrollTop(chatBody[0].scrollHeight);
		}

		function removeTypingIndicator() {
			$('#oac-typing-indicator').remove();
		}
	});

})( jQuery );
