<!-- Alumni Platform AI Assistant -->
<div x-data="chatbot()" class="fixed bottom-6 right-6 z-50">

    <!-- Chat Window -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="mb-4 w-96 bg-white rounded-2xl shadow-2xl
                border border-gray-100 flex flex-col overflow-hidden"
         style="height: 520px;">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-600
                    px-5 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white bg-opacity-20 rounded-full
                            flex items-center justify-center text-white font-bold text-sm">
                    AI
                </div>
                <div>
                    <p class="text-white text-sm font-semibold leading-tight">
                        Alumni Assistant
                    </p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full
                                     animate-pulse inline-block"></span>
                        <p class="text-blue-200 text-xs">Online • Powered by Gemini</p>
                    </div>
                </div>
            </div>
            <button @click="open = false"
                    class="text-white text-opacity-70 hover:text-opacity-100
                           w-8 h-8 flex items-center justify-center rounded-lg
                           hover:bg-white hover:bg-opacity-10 transition text-lg">
                ✕
            </button>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50"
             id="chat-messages">

            <!-- Welcome message -->
            <div class="flex gap-2 items-end">
                <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center
                            justify-center text-white text-xs font-bold flex-shrink-0">
                    AI
                </div>
                <div class="bg-white rounded-2xl rounded-bl-sm px-4 py-3
                            shadow-sm border border-gray-100 max-w-xs">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        👋 Hi! I'm your Alumni Platform assistant.
                    </p>
                    <p class="text-sm text-gray-700 leading-relaxed mt-1">
                        I can help you with profiles, events, announcements,
                        and anything about this platform!
                    </p>
                </div>
            </div>

            <!-- Quick suggestions (shown only at start) -->
            <div x-show="messages.length === 0" class="pl-9">
                <p class="text-xs text-gray-400 mb-2">Quick questions:</p>
                <div class="flex flex-wrap gap-2">
                    <template x-for="suggestion in suggestions">
                        <button @click="sendSuggestion(suggestion)"
                                class="text-xs bg-white border border-blue-200
                                       text-blue-600 px-3 py-1.5 rounded-full
                                       hover:bg-blue-50 transition">
                            <span x-text="suggestion"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Dynamic messages -->
            <template x-for="msg in messages" :key="msg.id">
                <div>
                    <!-- User message -->
                    <div x-show="msg.role === 'user'" class="flex justify-end mb-2">
                        <div class="bg-blue-600 text-white rounded-2xl rounded-br-sm
                                    px-4 py-3 text-sm max-w-xs shadow-sm leading-relaxed">
                            <span x-text="msg.text"></span>
                        </div>
                    </div>

                    <!-- AI message -->
                    <div x-show="msg.role === 'ai'" class="flex gap-2 items-end mb-2">
                        <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center
                                    justify-center text-white text-xs font-bold flex-shrink-0">
                            AI
                        </div>
                        <div class="bg-white rounded-2xl rounded-bl-sm px-4 py-3
                                    shadow-sm border border-gray-100 max-w-xs">
                            <p class="text-sm text-gray-700 leading-relaxed"
                               x-text="msg.text"></p>
                        </div>
                    </div>

                    <!-- Error message -->
                    <div x-show="msg.role === 'error'" class="flex gap-2 items-end mb-2">
                        <div class="w-7 h-7 bg-red-100 rounded-full flex items-center
                                    justify-center text-red-500 text-xs flex-shrink-0">
                            !
                        </div>
                        <div class="bg-red-50 border border-red-100 rounded-2xl
                                    rounded-bl-sm px-4 py-3 max-w-xs">
                            <p class="text-sm text-red-500 leading-relaxed"
                               x-text="msg.text"></p>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Typing indicator -->
            <div x-show="loading" class="flex gap-2 items-end">
                <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center
                            justify-center text-white text-xs font-bold flex-shrink-0">
                    AI
                </div>
                <div class="bg-white rounded-2xl rounded-bl-sm px-4 py-3
                            shadow-sm border border-gray-100">
                    <div class="flex gap-1 items-center h-4">
                        <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce"
                              style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce"
                              style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce"
                              style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Input Area -->
        <div class="border-t border-gray-100 p-3 bg-white flex-shrink-0">
            <div class="flex gap-2 items-end">
                <textarea
                    x-model="userInput"
                    @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                    placeholder="Ask me anything... (Enter to send)"
                    rows="1"
                    :disabled="loading"
                    class="flex-1 border border-gray-200 rounded-xl px-3 py-2
                           text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                           resize-none disabled:opacity-50 bg-gray-50"
                    style="max-height: 80px;"
                    @input="autoResize($event)">
                </textarea>
                <button
                    @click="sendMessage()"
                    :disabled="loading || !userInput.trim()"
                    class="w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white
                           rounded-xl flex items-center justify-center transition
                           disabled:opacity-40 disabled:cursor-not-allowed flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="currentColor" class="w-4 h-4">
                        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75
                                 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94
                                 60.519-22.055a.749.749 0 000-1.390z"/>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-gray-300 mt-1.5 text-center">
                Shift+Enter for new line
            </p>
        </div>

    </div>

    <!-- Toggle Button -->
    <div class="relative">
        <!-- Unread badge -->
        <span x-show="unread > 0 && !open"
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white
                     text-xs rounded-full flex items-center justify-center z-10"
              x-text="unread">
        </span>

        <button @click="toggleChat()"
                class="w-14 h-14 bg-gradient-to-r from-blue-700 to-indigo-600
                       hover:from-blue-800 hover:to-indigo-700 text-white rounded-full
                       shadow-lg flex items-center justify-center transition
                       transform hover:scale-105 active:scale-95">
            <span x-show="!open" class="text-2xl">💬</span>
            <span x-show="open" class="text-xl font-bold">✕</span>
        </button>
    </div>

</div>

<script>
function chatbot() {
    return {
        open: false,
        loading: false,
        userInput: '',
        messages: [],
        msgId: 0,
        unread: 0,

        suggestions: [
            'How do I update my profile?',
            'How do I register for an event?',
            'How do I post something?',
            'What is this platform for?',
        ],

        toggleChat() {
            this.open = !this.open;
            if (this.open) {
                this.unread = 0;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        async sendSuggestion(text) {
            this.userInput = text;
            await this.sendMessage();
        },

        async sendMessage() {
            const text = this.userInput.trim();
            if (!text || this.loading) return;

            this.messages.push({
                id: this.msgId++,
                role: 'user',
                text: text
            });

            this.userInput = '';
            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            try {
                const response = await fetch('/chatbot/ask', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                    },
                    body: JSON.stringify({ message: text })
                });

                if (!response.ok) {
                    throw new Error('Server error');
                }

                const data = await response.json();

                this.messages.push({
                    id: this.msgId++,
                    role: 'ai',
                    text: data.reply
                });

                // Show unread badge if chat is closed
                if (!this.open) this.unread++;

            } catch (error) {
                this.messages.push({
                    id: this.msgId++,
                    role: 'error',
                    text: 'Sorry, I could not connect right now. Please try again.'
                });
            }

            this.loading = false;
            this.$nextTick(() => this.scrollToBottom());
        },

        scrollToBottom() {
            const el = document.getElementById('chat-messages');
            if (el) el.scrollTop = el.scrollHeight;
        },

        autoResize(event) {
            const el = event.target;
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 80) + 'px';
        }
    }
}
</script>