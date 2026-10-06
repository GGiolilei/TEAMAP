<x-app-layout :hide-navbar="true">
    {{-- --- GLOBAL NOTIFICATION AUDIO --- --}}
    <audio id="chatNotificationSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

    <script>
        // Flag to track if the browser allows audio yet
        let audioContextUnlocked = false;

        // Wake up the audio engine on the first user interaction
        function unlockAudioEngine() {
            if (audioContextUnlocked) return;

            const sound = document.getElementById('chatNotificationSound');
            if (sound) {
                // Play and immediately pause to verify permissions with the browser
                sound.play()
                    .then(() => {
                        sound.pause();
                        sound.currentTime = 0;
                        audioContextUnlocked = true;
                        console.log('🔊 Chat notification engine successfully unlocked.');

                        // Clean up listeners
                        document.removeEventListener('click', unlockAudioEngine);
                        document.removeEventListener('keydown', unlockAudioEngine);
                    })
                    .catch(err => console.debug('Waiting for a more definitive user action...'));
            }
        }

        // Assign listeners to capture early layout interaction
        document.addEventListener('click', unlockAudioEngine);
        document.addEventListener('keydown', unlockAudioEngine);

        function playNotificationSound() {
            const sound = document.getElementById('chatNotificationSound');
            if (sound) {
                sound.currentTime = 0;

                const playPromise = sound.play();
                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.warn('Playback blocked. Click anywhere on the page to enable sound notifications:', error);
                    });
                }
            }
        }
    </script>

    {{-- Fetch the currently highlighted channel from the URL query string, fallback to the first channel --}}
    @php
        $activeChannelId = request('channel', $lobby->channels->first()?->id);
        $currentChannel = $lobby->channels->firstWhere('id', $activeChannelId) ?? $lobby->channels->first();
    @endphp

    <style>
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        .animate-bounce-slow-1 { animation: bounce-slow 1s infinite 0.1s; }
        .animate-bounce-slow-2 { animation: bounce-slow 1s infinite 0.2s; }
        .animate-bounce-slow-3 { animation: bounce-slow 1s infinite 0.3s; }

        @keyframes slide-in-up {
            0% { opacity: 0; transform: translateY(6px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up { animation: slide-in-up 0.2s ease-out forwards; }

        /* Slim scrollbar for the timeline */
        #chat-timeline::-webkit-scrollbar { width: 8px; }
        #chat-timeline::-webkit-scrollbar-thumb { background: #E1E1DE; border-radius: 9999px; }
        #chat-timeline::-webkit-scrollbar-thumb:hover { background: #9A9C9A; }

        /* Dynamic CSS variables for instant theme switching */
        .theme-blue {
            --brand-primary: 37 38 37;        /* #252625 soft black */
            --brand-primary-hover: 58 59 58;  /* #3A3B3A */
            --brand-bg-accent: 233 233 231;   /* #E9E9E7 */
            --brand-border: 225 225 222;      /* #E1E1DE */
            --brand-text: 32 33 32;           /* #202120 */
        }
        .theme-rose {
            --brand-primary: 244 63 94;       /* Rose-500 */
            --brand-primary-hover: 225 29 72; /* Rose-600 */
            --brand-bg-accent: 255 228 230;   /* Rose-100 */
            --brand-border: 254 205 211;      /* Rose-200 */
            --brand-text: 225 29 72;          /* Rose-600 */
        }
    </style>

    {{-- Root node forced to exactly screen height and forbidden to scroll on a global page level --}}
    <div id="theme-root" class="theme-blue bg-white h-screen h-[100dvh] text-[#242524] flex flex-col lg:flex-row selection:bg-[#202120] selection:text-white overflow-hidden relative">

        {{-- --- FIXED MOBILE HEADER --- --}}
        <div class="flex items-center justify-between px-4 py-3 lg:hidden bg-white border-b border-[#E1E1DE] shrink-0">
            <h2 class="text-sm font-bold text-[#202120] truncate">{{ $lobby->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('tasks.index', $lobby->id) }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-[#EEEEEC] border border-[#E1E1DE] text-[#3A3B3A] text-xs font-medium rounded-lg transition active:scale-95">
                    <svg class="w-3.5 h-3.5 text-[#747674]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Tasks
                </a>

                @if($currentChannel)
                <a href="{{ route('chat.huddle', ['channel' => $currentChannel->id]) }}"
                   class="flex items-center gap-2 px-3 py-1.5 bg-[#252625] hover:bg-[#3A3B3A] text-white text-xs font-semibold rounded-lg transition active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Huddle
                </a>
                @endif
            </div>
        </div>

        {{-- --- SIDEBAR --- --}}
        <div class="w-full lg:w-72 bg-[#F7F7F5] border-b lg:border-b-0 lg:border-r border-[#E1E1DE] p-5 flex flex-col justify-between shrink-0 overflow-y-auto max-h-[40vh] lg:max-h-none lg:h-full">
            <div class="space-y-5">
                <div class="flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 bg-white hover:bg-[#EEEEEC] border border-[#E1E1DE] text-[#747674] hover:text-[#202120] rounded-lg transition text-xs font-semibold flex items-center gap-1.5">
                        ← Dashboard
                    </a>

                    <button id="theme-toggle-btn" class="p-1.5 bg-white border border-[#E1E1DE] hover:bg-[#EEEEEC] rounded-lg text-[#747674] hover:text-[#202120] transition" title="Switch Theme">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M4.098 19.902a3.75 3.75 0 015.304-5.304l6.401-6.401m-11.705 11.705l-.71-.71m7.115-7.114l-.71-.71m11.705-11.705a3.75 3.75 0 115.304 5.304l-6.401 6.401m5.304-5.304l.71-.71m-7.115 7.114l.71-.71M11 6.22a6.75 6.75 0 00-6.75 6.75m13.5 0a6.75 6.75 0 01-6.75 6.75m0-13.5A6.75 6.75 0 0117.75 13.5m-13.5 0A6.75 6.75 0 0011 20.25" />
                        </svg>
                    </button>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-[#202120] tracking-tight">{{ $lobby->name }}</h2>
                    <p class="text-[11px] text-[#747674] mt-1 leading-relaxed">
                        Goal: <span class="text-[#202120] font-semibold">{{ $lobby->project_goal }}</span>
                    </p>
                </div>

                {{-- Unified Actions Area --}}
                <div class="hidden lg:flex flex-col gap-2">
                    <a href="{{ route('tasks.index', $lobby->id) }}"
                       class="flex items-center justify-center gap-2 w-full py-2 bg-white hover:bg-[#EEEEEC] border border-[#E1E1DE] text-[#3A3B3A] text-xs font-semibold rounded-lg transition active:scale-95">
                        <svg class="w-4 h-4 text-[#747674]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Workspace Task Board
                    </a>
                </div>

                <div class="h-px bg-[#E1E1DE]"></div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-[11px] font-bold text-[#747674] uppercase tracking-wider">Channels</h3>
                        @if(auth()->id() === $lobby->owner_id)
                            <a href="{{ route('lobbies.channels.create', $lobby->id) }}" class="p-1 hover:bg-[#EEEEEC] rounded text-[#747674] hover:text-[#202120] transition-colors" title="Create New Channel">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <div class="space-y-0.5">
                        @forelse($lobby->channels as $roomChannel)
                            @php $isActive = $currentChannel?->id === $roomChannel->id; @endphp
                            <a href="{{ route('chat.index', ['lobby' => $lobby->id, 'channel' => $roomChannel->id]) }}"
                               class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition text-[13px] {{ $isActive ? 'bg-[#E5E5E2] text-[#202120] font-semibold' : 'text-[#747674] hover:text-[#202120] hover:bg-[#EEEEEC] font-medium' }}">
                                <span class="{{ $isActive ? 'text-[#202120]' : 'text-[#9A9C9A]' }} font-mono">#</span>
                                {{ $roomChannel->name }}
                            </a>
                        @empty
                            <div class="text-[11px] text-[#9A9C9A] p-3 bg-white rounded-lg text-center border border-dashed border-[#E1E1DE]">
                                No active channels found.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="h-px bg-[#E1E1DE]"></div>

                {{-- --- ROSTER SECTION (COLLAPSIBLE) --- --}}
                <div>
                    <button id="roster-toggle-btn" class="flex items-center justify-between w-full text-[11px] font-bold text-[#747674] uppercase tracking-wider mb-2 hover:text-[#202120] transition-colors focus:outline-none">
                        <span>Members ({{ $lobby->members->count() }})</span>
                        <svg id="roster-arrow" class="w-3.5 h-3.5 transform transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div id="roster-collapse-wrapper" class="space-y-0.5 max-h-32 lg:max-h-48 overflow-y-auto pr-1">
                        @foreach($lobby->members as $member)
                            <div class="flex items-center justify-between px-2 py-1.5 rounded-lg hover:bg-[#EEEEEC] transition-colors">
                                <div class="flex items-center gap-2.5 truncate">
                                    <div class="w-7 h-7 rounded-md flex items-center justify-center font-bold text-[10px] uppercase shrink-0 {{ $member->id == 2 ? 'bg-[rgb(var(--brand-primary))] text-white' : 'bg-[#E9E9E7] text-[#3A3B3A]' }}">
                                        {{ substr($member->name, 0, 2) }}
                                    </div>
                                    <div class="truncate">
                                        <h5 class="text-xs font-semibold text-[#242524] truncate">{{ $member->name }}</h5>
                                        <p class="text-[9px] text-[#9A9C9A] uppercase tracking-wide">
                                            @if($member->id == 2)
                                                AI Companion
                                            @else
                                                {{ $member->id === $lobby->owner_id ? 'Organizer' : 'Partner' }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $member->id == 2 ? 'bg-[#9A9C9A]' : 'bg-emerald-500' }}"></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-[#E1E1DE] mt-4 lg:mt-0 text-[10px] text-[#9A9C9A] text-center font-mono tracking-wider uppercase">
                Ref ID: #{{ $lobby->id }}
            </div>
        </div>

        {{-- --- MAIN CHAT SYSTEM PANEL --- --}}
        <div class="flex-1 flex flex-col min-h-0 h-full bg-white relative">

            @if($currentChannel)
                {{-- --- DESKTOP HEADER --- --}}
                <div class="hidden lg:flex px-6 h-16 bg-white border-b border-[#E1E1DE] items-center justify-between shrink-0">
                    <div class="flex items-baseline gap-3 min-w-0">
                        <h3 class="text-base font-bold text-[#202120] tracking-tight shrink-0">
                            <span class="text-[#9A9C9A] font-mono font-medium">#</span> {{ $currentChannel->name }}
                        </h3>
                        <span class="w-px h-4 bg-[#E1E1DE] self-center"></span>
                        <p class="text-xs text-[#747674] truncate">{{ $currentChannel->description ?? 'Secure team sync for verified partners' }}</p>
                    </div>

                    <a href="{{ route('chat.huddle', ['channel' => $currentChannel->id]) }}"
                       class="flex items-center gap-2 px-3.5 py-2 bg-[#252625] hover:bg-[#3A3B3A] text-white text-xs font-semibold rounded-lg transition active:scale-95 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Join Huddle
                    </a>
                </div>

                {{-- --- THE CHAT TIMELINE --- --}}
                <div id="chat-timeline" class="flex-1 overflow-y-auto min-h-0 scroll-smooth bg-[#F7F7F5]">
                    <div class="max-w-3xl mx-auto w-full px-4 sm:px-6 py-6">

                        <div id="messages-wrapper" class="space-y-1">
                            @forelse($currentChannel->messages ?? [] as $message)
                                @php
                                    $isMe = $message->user_id === auth()->id();
                                    $isTimmy = $message->user_id == 2;
                                @endphp

                                <div class="message-bubble group flex items-start gap-3 px-3 py-2.5 -mx-3 rounded-xl hover:bg-white transition-colors">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-xs uppercase shrink-0 select-none
                                        {{ $isMe ? 'bg-[rgb(var(--brand-primary))] text-white' : ($isTimmy ? 'bg-[#3A3B3A] text-white' : 'bg-[#E9E9E7] text-[#3A3B3A]') }}">
                                        {{ substr($message->user->name ?? '?', 0, 2) }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-baseline gap-2">
                                            <span class="sender-name-label text-[13px] font-bold text-[#202120] truncate">{{ $isMe ? 'You' : ($message->user->name ?? 'Anonymous') }}</span>
                                            @if($isTimmy)
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-[#747674] bg-[#E9E9E7] px-1.5 py-0.5 rounded">AI</span>
                                            @endif
                                            <span class="text-[11px] text-[#9A9C9A] shrink-0">{{ $message->created_at->format('g:i A') }}</span>
                                        </div>

                                        <p class="mt-0.5 text-sm leading-relaxed text-[#242524] break-words">{{ $message->content }}</p>
                                    </div>
                                </div>
                            @empty
                                <div id="empty-history-notice" class="flex flex-col items-center text-center py-20">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-[#E1E1DE] flex items-center justify-center text-xl font-mono text-[#9A9C9A] mb-4">#</div>
                                    <p class="text-sm font-semibold text-[#202120]">Welcome to #{{ $currentChannel->name }}</p>
                                    <p class="text-xs text-[#747674] mt-1 max-w-xs leading-relaxed">
                                        This channel is quiet. Send a message or tag <span class="text-[#202120] font-semibold">@timmy</span> to start the discussion.
                                    </p>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>

                {{-- --- COMPOSER --- --}}
                <div class="bg-[#F7F7F5] px-4 sm:px-6 pb-4 pt-1 shrink-0">
                    <div class="max-w-3xl mx-auto w-full">

                        {{-- Typing notifier --}}
                        <div id="typing-container" class="hidden h-5 mb-1.5 px-1">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-0.5">
                                    <span class="w-1 h-1 bg-[#747674] rounded-full animate-bounce-slow-1"></span>
                                    <span class="w-1 h-1 bg-[#747674] rounded-full animate-bounce-slow-2"></span>
                                    <span class="w-1 h-1 bg-[#747674] rounded-full animate-bounce-slow-3"></span>
                                </div>
                                <span id="typing-text" class="text-[11px] text-[#747674]">You are typing...</span>
                            </div>
                        </div>

                        <form id="chat-form" action="{{ route('messages.store', ['channel' => $currentChannel->id]) }}" method="POST"
                              class="flex items-center gap-2 bg-white border border-[#E1E1DE] rounded-2xl pl-4 pr-2 py-2 shadow-sm transition focus-within:border-[#202120] focus-within:ring-1 focus-within:ring-[#202120]/10">
                            @csrf
                            <input id="message-input" name="content" type="text" autocomplete="off" required
                                   placeholder="Message #{{ $currentChannel->name }}"
                                   class="flex-1 min-w-0 bg-transparent border-0 shadow-none focus:ring-0 focus:outline-none text-sm text-[#242524] placeholder-[#9A9C9A] py-1.5 px-0" />
                            <button type="submit" title="Send"
                                    class="w-9 h-9 shrink-0 flex items-center justify-center rounded-xl bg-[rgb(var(--brand-primary))] hover:bg-[rgb(var(--brand-primary-hover))] text-white transition active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6"/>
                                </svg>
                            </button>
                        </form>

                        <p class="mt-2 px-1 text-[10px] text-[#9A9C9A]">
                            Press <span class="font-semibold text-[#747674]">Enter</span> to send · tag <span class="font-semibold text-[#747674]">@timmy</span> to ask the AI companion
                        </p>
                    </div>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-[#9A9C9A] text-sm p-6 bg-[#F7F7F5]">
                    <svg class="w-12 h-12 text-[#E1E1DE] mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <span>This workspace has no active feeds. Click the <strong class="text-[#202120] font-bold">+</strong> button in the sidebar to create one.</span>
                </div>
            @endif

        </div>

    </div>

    <script>
        // --- Roster Expandable/Collapsible Toggle Module ---
        document.addEventListener('DOMContentLoaded', () => {
            const rosterToggle = document.getElementById('roster-toggle-btn');
            const rosterWrapper = document.getElementById('roster-collapse-wrapper');
            const rosterArrow = document.getElementById('roster-arrow');

            if (rosterToggle && rosterWrapper && rosterArrow) {
                rosterToggle.addEventListener('click', () => {
                    if (rosterWrapper.classList.contains('hidden')) {
                        rosterWrapper.classList.remove('hidden');
                        rosterArrow.classList.remove('rotate-180');
                    } else {
                        rosterWrapper.classList.add('hidden');
                        rosterArrow.classList.add('rotate-180');
                    }
                });
            }
        });

        // --- 1. Immediate Theme Engine Initialization ---
        const themeRoot = document.getElementById('theme-root');
        const themeToggleBtn = document.getElementById('theme-toggle-btn');

        const savedTheme = localStorage.getItem('chat-workspace-theme') || 'theme-blue';
        if (themeRoot) {
            themeRoot.className = themeRoot.className.replace('theme-blue', savedTheme);
        }

        if (themeToggleBtn && themeRoot) {
            themeToggleBtn.addEventListener('click', () => {
                if (themeRoot.classList.contains('theme-blue')) {
                    themeRoot.classList.remove('theme-blue');
                    themeRoot.classList.add('theme-rose');
                    localStorage.setItem('chat-workspace-theme', 'theme-rose');
                } else {
                    themeRoot.classList.remove('theme-rose');
                    themeRoot.classList.add('theme-blue');
                    localStorage.setItem('chat-workspace-theme', 'theme-blue');
                }
            });
        }

        // --- 2. Chat Timeline Layout Listeners ---
        document.addEventListener('DOMContentLoaded', () => {
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const typingContainer = document.getElementById('typing-container');
            const typingText = document.getElementById('typing-text');
            const chatTimeline = document.getElementById('chat-timeline');

            if (!chatTimeline) return;

            // Lock initial viewport viewing focus directly onto the bottom entries
            chatTimeline.scrollTop = chatTimeline.scrollHeight;

            // Channel Context Variable safely set up for the read endpoint snippet
            const channelId = @json($currentChannel?->id ?? null);

            // --- 🔄 1-SECOND BACKGROUND AUTO-REFRESH ENGINE ---
            const currentUrl = window.location.href;

            setInterval(async () => {
                try {
                    // Fetch current workspace snapshot silently
                    const response = await fetch(currentUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (response.ok) {
                        const htmlText = await response.text();

                        // Parse incoming string to access nodes cleanly
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(htmlText, 'text/html');
                        const incomingWrapper = doc.getElementById('messages-wrapper');
                        const currentWrapper = document.getElementById('messages-wrapper');

                        if (incomingWrapper && currentWrapper) {
                            if (incomingWrapper.innerHTML.trim() !== currentWrapper.innerHTML.trim()) {

                                // 🔊 AUDIO RING ENGINE (By message count difference)
                                const currentCount = currentWrapper.querySelectorAll('.message-bubble').length;
                                const incomingBubbles = incomingWrapper.querySelectorAll('.message-bubble');
                                const incomingCount = incomingBubbles.length;

                                if (incomingCount > currentCount) {
                                    const lastIncomingBubble = incomingBubbles[incomingCount - 1];
                                    const nameSpan = lastIncomingBubble.querySelector('.sender-name-label');

                                    // Read text value directly. If it says "You", do not make sound.
                                    const isMessageFromMe = nameSpan ? nameSpan.innerText.trim() === 'You' : false;

                                    if (!isMessageFromMe) {
                                        playNotificationSound();

                                        // ==========================================
                                        // 📝 MARK-READ SNIPPET INSERTED HERE
                                        // ==========================================
                                        if (channelId) {
                                            fetch(`/channels/${channelId}/read`, {
                                                method: 'POST',
                                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                                            }).catch(err => console.error("Error updating read state:", err));
                                        }
                                    }
                                }

                                // Detect if user is reading older history logs
                                const isScrolledToBottom = (chatTimeline.scrollHeight - chatTimeline.clientHeight - chatTimeline.scrollTop) < 60;

                                // Inject new messages node
                                currentWrapper.innerHTML = incomingWrapper.innerHTML;

                                // Snap down if they were already at the bottom
                                if (isScrolledToBottom) {
                                    chatTimeline.scrollTop = chatTimeline.scrollHeight;
                                }
                            }
                        }
                    }
                } catch (err) {
                    console.debug('Background sync cycle paused temporarily.');
                }
            }, 1000); // 1000ms = 1 Second Loop Rate

            // --- AJAX FORM MESSAGE SUBMISSION ---
            if (chatForm && messageInput) {
                chatForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const textValue = messageInput.value.trim();
                    if (!textValue) return;

                    const formData = new FormData(chatForm);
                    const actionUrl = chatForm.getAttribute('action');

                    if (textValue.toLowerCase().includes('@timmy') && typingText && typingContainer) {
                        typingText.innerText = "Timmy is thinking...";
                        typingContainer.classList.remove('hidden');
                    }

                    messageInput.value = '';

                    try {
                        await fetch(actionUrl, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        chatTimeline.scrollTop = chatTimeline.scrollHeight;
                    } catch (error) {
                        console.error('Failed to dispatch feed entry data package.', error);
                    }
                });
            }

            // Monitor keyboard interactions for typing indicators
            let typingTimeout;
            if (messageInput && typingContainer && typingText) {
                messageInput.addEventListener('input', () => {
                    if (messageInput.value.trim().length > 0) {
                        const isTimmy = messageInput.value.toLowerCase().includes('@timmy');
                        typingText.innerText = isTimmy ? "Timmy is listening..." : "You are typing...";
                        typingContainer.classList.remove('hidden');
                    } else {
                        typingContainer.classList.add('hidden');
                    }

                    clearTimeout(typingTimeout);
                    typingTimeout = setTimeout(() => {
                        typingContainer.classList.add('hidden');
                    }, 2000);
                });
            }
        });
    </script>
</x-app-layout>