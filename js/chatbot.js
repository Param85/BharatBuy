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
            border-radius: 6px;
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
            color: #F8E7C9;
            border-color: #06433B;
            background: #06433B;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(6, 67, 59, 0.25);
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
            background: rgba(248, 231, 201, 0.35);
            border: 1px solid rgba(226, 199, 155, 0.8);
            color: #06433B;
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
            color: #F8E7C9;
            background: #06433B;
            border-color: #06433B;
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

    <!-- Floating Chat Button (Emerald Ink & Champagne Luxury Glow) -->
    <div id="chat-btn" style="position: fixed; bottom: 25px; right: 25px; background: linear-gradient(135deg, #06433B 0%, #0A5C52 100%); color: #F8E7C9; width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 10px 25px -4px rgba(6, 67, 59, 0.45); border: 2px solid rgba(248, 231, 201, 0.6); z-index: 1000; transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
        <i class="fas fa-robot fa-lg" style="color: #F8E7C9;"></i>
    </div>

    <!-- Chat Window Container -->
    <div id="chat-window" style="display: none; opacity: 0; transform: translateY(20px); transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: fixed; bottom: 98px; right: 25px; width: 350px; height: 470px; background: var(--card-bg); border: 1.5px solid var(--champagne-border, #E8D3B0); border-radius: 18px; box-shadow: 0 15px 35px rgba(6, 67, 59, 0.15); z-index: 1000; flex-direction: column; overflow: hidden; font-family: inherit; color: var(--text);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #042823 0%, #06433B 60%, #08564B 100%); color: #F8E7C9; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1.5px solid rgba(248, 231, 201, 0.25);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="background: rgba(248, 231, 201, 0.15); border: 1px solid rgba(248, 231, 201, 0.3); width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #F8E7C9;">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 0.95rem; color: #F8E7C9;">BharatBuy Assistant</h4>
                    <span style="font-size: 0.7rem; color: #E8D3B0; display: flex; align-items: center; gap: 0.25rem;">
                        <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%; display: inline-block;"></span> Online
                    </span>
                </div>
            </div>
            <i class="fas fa-times close-chat" style="cursor: pointer; color: #F8E7C9; opacity: 0.85; transition: opacity 0.2s;" id="close-chat-btn"></i>
        </div>

        <!-- Chat Log Area -->
        <div id="chat-logs" style="flex: 1; padding: 1.25rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; background: var(--bg);">
            <!-- Initial Greeting with TTS -->
            <div class="bot-msg-wrapper" style="display: flex; flex-direction: column; align-self: flex-start; max-width: 85%;">
                <div style="background: var(--border); color: var(--text); padding: 0.75rem 1rem; border-radius: 4px 12px 12px 12px; font-size: 0.88rem; line-height: 1.4; font-weight: 500;">
                    Namaste! I'm BharatBuy, your Smart Shopping Assistant. Ask me <b>'how it works'</b>, about <b>'recommendations'</b>, or type <b>'help'</b> for tips!
                </div>
                <button class="chat-tts-btn" title="Read Aloud" data-msg="Namaste! I am BharatBuy, your Smart Shopping Assistant. Ask me how it works, about recommendations, or type help for tips!">
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
            <button id="send-chat" class="btn" title="Send message" style="width: 42px; height: 42px; padding: 0; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: linear-gradient(135deg, #06433B 0%, #08564B 100%); color: #F8E7C9; border: 1px solid rgba(248, 231, 201, 0.4); cursor: pointer;">
                <i class="fas fa-paper-plane" style="color: #F8E7C9;"></i>
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
    let finalTranscript = '';
    let silenceTimer = null;
    let speechDetected = false;

    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.continuous = true; // Continuous listening so pauses don't cut user off prematurely
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;
        // Prioritize user's English locale or Indian English
        recognition.lang = (navigator.language && navigator.language.startsWith('en')) ? navigator.language : 'en-IN';

        function clearSilenceTimer() {
            if (silenceTimer) {
                clearTimeout(silenceTimer);
                silenceTimer = null;
            }
        }

        function restartSilenceTimer() {
            clearSilenceTimer();
            // Automatically finalize and submit 2 seconds after last spoken word
            silenceTimer = setTimeout(() => {
                if (isListening && speechDetected && chatInput.value.trim().length > 0) {
                    try { recognition.stop(); } catch(e) {}
                }
            }, 2000);
        }

        recognition.onstart = function() {
            isListening = true;
            speechDetected = false;
            finalTranscript = '';
            clearSilenceTimer();
            audioBtn.classList.add('listening');
            audioBtn.innerHTML = '<i class="fas fa-microphone-lines fa-beat"></i>';
            audioBtn.title = "Listening... Speak now (Click to stop)";
            audioBtn.style.color = '#ef4444';
            chatInput.placeholder = "Listening... Speak into your microphone...";
        };

        recognition.onresult = function(event) {
            let interimTranscript = '';
            for (let i = 0; i < event.results.length; ++i) {
                const phrase = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    finalTranscript += phrase + ' ';
                } else {
                    interimTranscript += phrase;
                }
            }
            const completeText = (finalTranscript + interimTranscript).trim();
            if (completeText) {
                chatInput.value = completeText;
                speechDetected = true;
                restartSilenceTimer();
            }
        };

        recognition.onend = function() {
            isListening = false;
            clearSilenceTimer();
            audioBtn.classList.remove('listening');
            audioBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            audioBtn.title = "Audio Voice Input";
            audioBtn.style.color = '';
            chatInput.placeholder = "Type a message or use audio...";
            
            // Auto submit speech if recognized
            if (chatInput.value.trim().length > 0 && speechDetected) {
                setTimeout(() => {
                    sendMsg();
                }, 300);
            }
        };

        function showMicrophoneNotice(title, message, isError = false) {
            const existing = document.getElementById('mic-notice');
            if (existing) existing.remove();

            const bgColor = isError ? '#fef2f2' : '#f0fdf4';
            const borderColor = isError ? '#fecaca' : '#bbf7d0';
            const textColor = isError ? '#991b1b' : '#166534';
            const iconClass = isError ? 'fa-microphone-slash' : 'fa-info-circle';

            const noticeHtml = `
                <div id="mic-notice" class="bot-msg-wrapper" style="align-self: center; width: 100%; max-width: 95%; margin: 0.5rem 0;">
                    <div style="background: ${bgColor}; border: 1px solid ${borderColor}; color: ${textColor}; padding: 0.85rem 1rem; border-radius: 10px; font-size: 0.82rem; line-height: 1.45;">
                        <div style="font-weight: 700; display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.35rem;">
                            <i class="fas ${iconClass}"></i> ${title}
                        </div>
                        <div>${message}</div>
                    </div>
                </div>`;
            chatLogs.insertAdjacentHTML('beforeend', noticeHtml);
            chatLogs.scrollTop = chatLogs.scrollHeight;
        }

        recognition.onerror = function(event) {
            console.warn("Audio recognition error:", event.error);
            isListening = false;
            clearSilenceTimer();
            audioBtn.classList.remove('listening');
            audioBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            audioBtn.title = "Audio Voice Input";
            audioBtn.style.color = '';
            chatInput.placeholder = "Type a message or use audio...";

            if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
                showMicrophoneNotice(
                    'Microphone Access Blocked',
                    'Your browser blocked microphone access. Click the <b>Lock or Tune icon (🔒 / ⚙️)</b> on the left of your browser address bar, set <b>Microphone</b> to <b>Allow</b>, and refresh the page.',
                    true
                );
            } else if (event.error === 'no-speech') {
                showMicrophoneNotice(
                    'No Voice Detected',
                    'The microphone did not detect any speech. Please make sure your microphone is not muted and speak directly into the mic.',
                    false
                );
            } else if (event.error === 'network') {
                showMicrophoneNotice(
                    'Speech Recognition Network Error',
                    'Google speech service had a connection timeout. Please check your internet connection and try again.',
                    true
                );
            } else if (event.error === 'audio-capture') {
                showMicrophoneNotice(
                    'No Microphone Found',
                    'No working microphone was detected on your system. Please connect a microphone or headset and try again.',
                    true
                );
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
            try {
                recognition.stop();
            } catch (e) {}
        } else {
            try {
                finalTranscript = '';
                speechDetected = false;
                chatInput.value = '';
                recognition.start();
            } catch (err) {
                console.warn("Audio start error, resetting:", err);
                try {
                    recognition.abort();
                    setTimeout(() => {
                        try { 
                            finalTranscript = '';
                            speechDetected = false;
                            recognition.start(); 
                        } catch (e) {}
                    }, 150);
                } catch (e) {}
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
        chatInput.style.borderColor = '#06433B';
        chatInput.style.boxShadow = '0 0 0 3px rgba(248, 231, 201, 0.6)';
    });
    chatInput.addEventListener('blur', () => {
        chatInput.style.borderColor = 'var(--border)';
        chatInput.style.boxShadow = 'none';
    });

    function sendMsg() {
        const text = chatInput.value.trim();
        if (!text) return;

        // Append User Message with speech bubble styling (Emerald Ink & Champagne)
        chatLogs.innerHTML += `
            <div class="user-msg-wrapper" style="align-self: flex-end; max-width: 80%; background: linear-gradient(135deg, #06433B 0%, #08564B 100%); color: #F8E7C9; padding: 0.75rem 1rem; border-radius: 12px 12px 4px 12px; font-size: 0.88rem; line-height: 1.4; border: 1px solid rgba(248, 231, 201, 0.35); box-shadow: 0 4px 12px rgba(6, 67, 59, 0.15);">
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
