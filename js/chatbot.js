document.addEventListener('DOMContentLoaded', function() {
    // Escape HTML to prevent XSS
    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }

    // Secure Markdown Parser
    function parseMarkdown(text) {
        if (!text) return '';
        let html = escapeHTML(text);

        // Parse Bold (**text** or __text__)
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/__(.*?)__/g, '<strong>$1</strong>');

        // Parse Italic (*text* or _text_)
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        html = html.replace(/_(.*?)_/g, '<em>$1</em>');

        // Parse Headers (e.g., ### Header)
        html = html.replace(/### (.*?)(?:\n|$)/g, '<h5 style="margin: 0.5rem 0 0.25rem 0; font-weight: 700; font-size: 0.92rem;">$1</h5>');
        html = html.replace(/## (.*?)(?:\n|$)/g, '<h4 style="margin: 0.75rem 0 0.35rem 0; font-weight: 700; font-size: 0.98rem;">$1</h4>');
        html = html.replace(/# (.*?)(?:\n|$)/g, '<h3 style="margin: 1rem 0 0.5rem 0; font-weight: 800; font-size: 1.05rem;">$1</h3>');

        // Parse Bullet Lists (- item or * item)
        const lines = html.split('\n');
        let inList = false;
        let listHtml = [];

        for (let line of lines) {
            let trimmed = line.trim();
            if (trimmed.startsWith('- ') || trimmed.startsWith('* ')) {
                if (!inList) {
                    inList = true;
                    listHtml.push('<ul style="margin: 0.5rem 0 0.5rem 1.25rem; padding: 0; list-style-type: disc; display: flex; flex-direction: column; gap: 0.25rem;">');
                }
                listHtml.push(`<li>${trimmed.substring(2)}</li>`);
            } else {
                if (inList) {
                    inList = false;
                    listHtml.push('</ul>');
                }
                listHtml.push(line);
            }
        }
        if (inList) {
            listHtml.push('</ul>');
        }

        html = listHtml.join('\n');

        // Replace newlines with <br>
        html = html.replace(/\n/g, '<br>');
        return html;
    }

    // Text-to-Speech (TTS) Read Aloud Helper
    let currentlyPlayingBtn = null;

    function cleanTextForSpeech(text) {
        if (!text) return '';
        let cleaned = text;

        // 1. Decode HTML entities (e.g., &quot;, &#39;, &amp;)
        cleaned = cleaned
            .replace(/&amp;/g, '&')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&quot;/g, '"')
            .replace(/&#39;/g, "'")
            .replace(/&nbsp;/g, ' ');

        // 2. Strip emojis and pictographs safely WITHOUT stripping numbers (0-9, #, *)
        // Note: NEVER use \p{Emoji_Component} because in Unicode digits 0-9 have Emoji_Component=Yes
        try {
            cleaned = cleaned.replace(/\p{Extended_Pictographic}|\p{Emoji_Presentation}/gu, '');
        } catch (e) {
            // Fallback for older JS engines
            cleaned = cleaned.replace(/([\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF])/g, '');
        }

        // 3. Convert currency symbols and abbreviations for natural pronunciation
        cleaned = cleaned.replace(/₹\s*/g, ' Rupees ');
        cleaned = cleaned.replace(/\bRs\.?\s*/gi, ' Rupees ');
        cleaned = cleaned.replace(/\$\s*/g, ' Dollars ');
        cleaned = cleaned.replace(/€\s*/g, ' Euros ');
        cleaned = cleaned.replace(/%/g, ' percent ');

        // 4. Strip markdown formatting characters while preserving numbers, text, and punctuation
        cleaned = cleaned.replace(/^#{1,6}\s+/gm, ''); // Headers e.g. "### "
        cleaned = cleaned.replace(/\*\*(.*?)\*\*/g, '$1'); // Bold **text**
        cleaned = cleaned.replace(/__(.*?)__/g, '$1'); // Bold __text__
        cleaned = cleaned.replace(/\*(.*?)\*/g, '$1'); // Italic *text*
        cleaned = cleaned.replace(/_(.*?)_/g, '$1'); // Italic _text_
        cleaned = cleaned.replace(/~~(.*?)~~/g, '$1'); // Strikethrough
        cleaned = cleaned.replace(/`{1,3}(.*?)`{1,3}/gs, '$1'); // Code blocks
        cleaned = cleaned.replace(/\[(.*?)\]\(.*?\)/g, '$1'); // Links [text](url) -> text

        // 5. Clean bullet list markers at line start without stripping numbered lists like "1. " or "2. "
        cleaned = cleaned.replace(/^[\s\-*\u2022]+\s*/gm, '');

        // 6. Remove HTML tags
        cleaned = cleaned.replace(/<[^>]*>/g, '');

        // 7. Clean multiple newlines and extra whitespace
        cleaned = cleaned.replace(/\s+/g, ' ').trim();
        return cleaned;
    }

    function stopSpeaking() {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
        if (currentlyPlayingBtn) {
            currentlyPlayingBtn.innerHTML = '<i class="fas fa-volume-high"></i> <span>Listen</span>';
            currentlyPlayingBtn.classList.remove('speaking');
            currentlyPlayingBtn.title = "Read Aloud";
            currentlyPlayingBtn = null;
        }
    }

    function speakText(rawText, btnElement) {
        if (!('speechSynthesis' in window)) {
            alert("Sorry, your browser does not support audio speech output.");
            return;
        }

        // If clicking the currently playing button, toggle stop
        if (currentlyPlayingBtn === btnElement && window.speechSynthesis.speaking) {
            stopSpeaking();
            return;
        }

        // Stop any previous speech
        stopSpeaking();

        const cleanText = cleanTextForSpeech(rawText);
        if (!cleanText) return;

        const utterance = new SpeechSynthesisUtterance(cleanText);
        
        // Slightly higher pitch and crisp conversational tempo
        utterance.rate = 1.05;
        utterance.pitch = 1.08;
        utterance.volume = 1.0;

        // Smart Voice Selection: Prioritize clear, natural, and expressive voices
        const voices = window.speechSynthesis.getVoices();
        
        // Priority 1: Natural / Neural online voices
        let selectedVoice = voices.find(v => 
            v.lang.startsWith('en') && (v.name.includes('Natural') || v.name.includes('Neural') || v.name.includes('Online'))
        );

        // Priority 2: High-definition Google or Microsoft expressive voices (Zira, Jenny, Aria, Sonia)
        if (!selectedVoice) {
            selectedVoice = voices.find(v => 
                v.lang.startsWith('en') && (v.name.includes('Zira') || v.name.includes('Jenny') || v.name.includes('Aria') || v.name.includes('Google') || v.name.includes('Sonia') || v.name.includes('Neerja'))
            );
        }

        // Priority 3: Any English voice with regional accents
        if (!selectedVoice) {
            selectedVoice = voices.find(v => v.lang.includes('en-IN') || v.lang.includes('en-US') || v.lang.includes('en-GB')) 
                         || voices.find(v => v.lang.startsWith('en'));
        }

        if (selectedVoice) {
            utterance.voice = selectedVoice;
        }

        btnElement.innerHTML = '<i class="fas fa-circle-stop"></i> <span>Stop</span>';
        btnElement.classList.add('speaking');
        btnElement.title = "Stop Reading";
        currentlyPlayingBtn = btnElement;

        utterance.onend = function() {
            stopSpeaking();
        };

        utterance.onerror = function() {
            stopSpeaking();
        };

        window.speechSynthesis.speak(utterance);
    }

    // Ensure voices are loaded for browsers that populate them asynchronously
    if ('speechSynthesis' in window && window.speechSynthesis.onvoiceschanged !== undefined) {
        window.speechSynthesis.onvoiceschanged = function() {
            window.speechSynthesis.getVoices();
        };
    }

    // Add HTML structure to body with Read Aloud and Audio/Voice Input support
    const chatHTML = `
    <style>
        .chat-tts-btn {
            background: rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.25);
            color: var(--primary);
            font-size: 0.76rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
            margin-top: 5px;
            outline: none;
            width: fit-content;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .chat-tts-btn:hover {
            color: #ffffff;
            border-color: var(--primary);
            background: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(37, 99, 235, 0.25);
        }
        .chat-tts-btn.speaking {
            color: #ffffff !important;
            border-color: #ef4444 !important;
            background: #ef4444 !important;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);
            animation: pulseSpeaking 1.5s infinite;
        }
        @keyframes pulseSpeaking {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .chat-audio-btn {
            background: rgba(249, 115, 22, 0.1);
            border: 1px solid rgba(249, 115, 22, 0.3);
            color: #ea580c;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            flex-shrink: 0;
            font-size: 1rem;
        }
        .chat-audio-btn:hover {
            color: #ffffff;
            background: #ea580c;
            border-color: #ea580c;
            transform: scale(1.05);
        }
        .chat-audio-btn.listening {
            color: #ffffff !important;
            border-color: #ef4444 !important;
            background: #ef4444 !important;
            animation: micPulse 1.5s infinite;
        }
        @keyframes micPulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
    </style>

    <!-- Floating Chat Button -->
    <div id="chat-btn" style="position: fixed; bottom: 25px; right: 25px; background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4); z-index: 1000; transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
        <i class="fas fa-robot fa-lg"></i>
    </div>

    <!-- Chat Window Container -->
    <div id="chat-window" style="display: none; opacity: 0; transform: translateY(20px); transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: fixed; bottom: 95px; right: 25px; width: 340px; height: 460px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow-lg); z-index: 1000; flex-direction: column; overflow: hidden; font-family: inherit; color: var(--text);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%); color: white; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="background: rgba(255,255,255,0.2); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.95rem;">BharatBuy Assistant</h4>
                    <span style="font-size: 0.7rem; color: #93c5fd; display: flex; align-items: center; gap: 0.25rem;">
                        <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%; display: inline-block;"></span> Online
                    </span>
                </div>
            </div>
            <i class="fas fa-times close-chat" style="cursor: pointer; opacity: 0.8; transition: opacity 0.2s;" id="close-chat-btn"></i>
        </div>

        <!-- Chat Log Area -->
        <div id="chat-logs" style="flex: 1; padding: 1.25rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; background: var(--bg);">
            <!-- Initial Greeting with TTS -->
            <div class="bot-msg-wrapper" style="display: flex; flex-direction: column; align-self: flex-start; max-width: 85%;">
                <div style="background: var(--border); color: var(--text); padding: 0.75rem 1rem; border-radius: 4px 12px 12px 12px; font-size: 0.88rem; line-height: 1.4; font-weight: 500;">
                    Hello! I'm BharatBuy, your Smart Shopping Assistant. Ask me <b>'how it works'</b>, about <b>'recommendations'</b>, or type <b>'help'</b> for tips!
                </div>
                <button class="chat-tts-btn" title="Read Aloud" data-msg="Hello! I am BharatBuy, your Smart Shopping Assistant. Ask me how it works, about recommendations, or type help for tips!">
                    <i class="fas fa-volume-high"></i> <span>Listen</span>
                </button>
            </div>
        </div>

        <!-- Input Section with Audio/Voice Input Button -->
        <div style="padding: 0.85rem 1rem; border-top: 1px solid var(--border); display: flex; gap: 0.5rem; background: var(--card-bg); align-items: center;">
            <input type="text" id="chat-input" placeholder="Type a message or use audio..." style="flex: 1; padding: 0.75rem 0.9rem; border: 1px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 0.88rem; outline: none; transition: border-color 0.2s; background: var(--bg); color: var(--text);">
            <button id="audio-chat-btn" class="chat-audio-btn" title="Audio Voice Input">
                <i class="fas fa-microphone"></i>
            </button>
            <button id="send-chat" class="btn btn-primary" title="Send message" style="width: 42px; height: 42px; padding: 0; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', chatHTML);

    const chatBtn = document.getElementById('chat-btn');
    const chatWin = document.getElementById('chat-window');
    const chatInput = document.getElementById('chat-input');
    const chatLogs = document.getElementById('chat-logs');
    const closeChatBtn = document.getElementById('close-chat-btn');
    const sendBtn = document.getElementById('send-chat');
    const audioBtn = document.getElementById('audio-chat-btn');

    if (!chatBtn || !chatWin || !chatInput || !chatLogs || !closeChatBtn || !sendBtn || !audioBtn) return;

    // Speech-to-Text / Audio Input Engine
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition = null;
    let isListening = false;

    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = true;
        recognition.lang = 'en-IN';
        
        recognition.onstart = function() {
            isListening = true;
            audioBtn.classList.add('listening');
            audioBtn.innerHTML = '<i class="fas fa-microphone-lines"></i>';
            audioBtn.title = "Listening... Click to stop";
            chatInput.placeholder = "Listening... Speak now 🎙️";
        };

        recognition.onresult = function(event) {
            let transcript = '';
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                transcript += event.results[i][0].transcript;
            }
            if (transcript) {
                chatInput.value = transcript;
            }
        };

        recognition.onspeechend = function() {
            if (recognition) {
                recognition.stop();
            }
        };

        recognition.onend = function() {
            isListening = false;
            audioBtn.classList.remove('listening');
            audioBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            audioBtn.title = "Audio Voice Input";
            chatInput.placeholder = "Type a message or use audio...";
            
            // Auto submit speech if recognized
            if (chatInput.value.trim().length > 0) {
                setTimeout(() => {
                    sendMsg();
                }, 400);
            }
        };

        recognition.onerror = function(event) {
            console.warn("Audio recognition error:", event.error);
            isListening = false;
            audioBtn.classList.remove('listening');
            audioBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            audioBtn.title = "Audio Voice Input";
            chatInput.placeholder = "Type a message or use audio...";
            if (event.error === 'not-allowed') {
                alert("Microphone access was denied. Please allow microphone permissions in your browser to use the audio voice feature.");
            }
        };
    }

    // Toggle Audio Recording
    audioBtn.addEventListener('click', function() {
        if (!SpeechRecognition) {
            alert("Audio voice recognition is not supported in this browser. Please try Google Chrome, Microsoft Edge, or Safari.");
            return;
        }

        stopSpeaking(); // Stop any bot speech playback when recording user audio

        if (isListening) {
            recognition.stop();
        } else {
            try {
                chatInput.value = '';
                recognition.start();
            } catch (err) {
                console.error("Audio start error:", err);
            }
        }
    });

    // Delegate Click for Read Aloud buttons
    chatLogs.addEventListener('click', function(e) {
        const btn = e.target.closest('.chat-tts-btn');
        if (btn) {
            const rawText = btn.getAttribute('data-msg') || '';
            speakText(rawText, btn);
        }
    });

    // Pulse transition on button hover
    chatBtn.addEventListener('mouseenter', () => {
        chatBtn.style.transform = 'scale(1.08)';
    });
    chatBtn.addEventListener('mouseleave', () => {
        chatBtn.style.transform = 'scale(1)';
    });

    // Toggle Chat Window Visibility
    chatBtn.addEventListener('click', toggleChat);
    closeChatBtn.addEventListener('click', toggleChat);

    function toggleChat() {
        if (chatWin.style.display === 'none' || !chatWin.style.display) {
            chatWin.style.display = 'flex';
            // Force redraw/reflow for transition
            chatWin.offsetHeight;
            chatWin.style.opacity = '1';
            chatWin.style.transform = 'translateY(0)';
            chatInput.focus();
        } else {
            stopSpeaking(); // Stop any audio speech when closing chat window
            if (isListening && recognition) {
                recognition.stop();
            }
            chatWin.style.opacity = '0';
            chatWin.style.transform = 'translateY(20px)';
            // Wait for transition animation
            setTimeout(() => {
                chatWin.style.display = 'none';
            }, 300);
        }
    }

    // Trigger Send
    sendBtn.addEventListener('click', sendMsg);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMsg();
        }
    });

    // Focus input styles
    chatInput.addEventListener('focus', () => {
        chatInput.style.borderColor = 'var(--primary)';
    });
    chatInput.addEventListener('blur', () => {
        chatInput.style.borderColor = 'var(--border)';
    });

    function sendMsg() {
        const text = chatInput.value.trim();
        if (!text) return;

        // Append User Message with speech bubble styling
        chatLogs.innerHTML += `
            <div class="user-msg-wrapper" style="align-self: flex-end; max-width: 80%; background: var(--primary); color: white; padding: 0.75rem 1rem; border-radius: 12px 12px 4px 12px; font-size: 0.88rem; line-height: 1.4; box-shadow: 0 2px 4px rgba(37,99,235,0.1);">
                ${escapeHTML(text)}
            </div>
        `;
        
        chatInput.value = '';
        chatLogs.scrollTop = chatLogs.scrollHeight;

        // Fetch Response from AJAX chatbot endpoint
        fetch(`ajax/chatbot.php?msg=${encodeURIComponent(text)}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Chatbot network failure');
                }
                return res.json();
            })
            .then(data => {
                const answerText = data.answer || '';
                const encodedMsg = escapeHTML(answerText);

                // Append Bot Response with bubble styling and TTS button
                chatLogs.innerHTML += `
                    <div class="bot-msg-wrapper" style="display: flex; flex-direction: column; align-self: flex-start; max-width: 85%;">
                        <div style="background: var(--card-bg); border: 1px solid var(--border); color: var(--text); padding: 0.75rem 1rem; border-radius: 4px 12px 12px 12px; font-size: 0.88rem; line-height: 1.4; box-shadow: var(--shadow);">
                            ${parseMarkdown(answerText)}
                        </div>
                        <button class="chat-tts-btn" title="Read Aloud" data-msg="${encodedMsg}">
                            <i class="fas fa-volume-high"></i> <span>Listen</span>
                        </button>
                    </div>
                `;
                chatLogs.scrollTop = chatLogs.scrollHeight;
            })
            .catch(err => {
                console.error(err);
                const errorMsg = "I encountered an error trying to connect. Please try again.";
                chatLogs.innerHTML += `
                    <div class="bot-msg-wrapper" style="display: flex; flex-direction: column; align-self: flex-start; max-width: 85%;">
                        <div style="background: var(--error-light); border: 1px solid var(--error); color: var(--error); padding: 0.75rem 1rem; border-radius: 4px 12px 12px 12px; font-size: 0.88rem; line-height: 1.4;">
                            ${errorMsg}
                        </div>
                        <button class="chat-tts-btn" title="Read Aloud" data-msg="${errorMsg}">
                            <i class="fas fa-volume-high"></i> <span>Listen</span>
                        </button>
                    </div>
                `;
                chatLogs.scrollTop = chatLogs.scrollHeight;
            });
    }
});
