<div x-data="notificationSystem()" x-init="initNotifications()" class="relative inline-block text-left">
    <!-- Notification Bell Button -->
    <button @click="open = !open" 
            type="button" 
            class="relative p-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white focus:outline-none">
        <!-- Bell Icon -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin=" His" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <!-- Total Unread/Pending Badge Counter -->
        <template x-if="totalCount > 0">
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white"
                  x-text="totalCount > 99 ? '99+' : totalCount">
            </span>
        </template>
    </button>

    <!-- Notification Dropdown Menu -->
    <div x-show="open" 
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-72 rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 dark:divide-gray-700"
         style="display: none;">
        
        <div class="p-3 font-semibold text-gray-700 dark:text-gray-200 text-sm">
            Notifications
        </div>

        <div class="py-1 text-sm">
            <!-- Pending Lobby Requests -->
            <a href="/lobbies/pending" class="flex items-center justify-between px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Pending Lobbies</span>
                </div>
                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200"
                      x-text="pending"></span>
            </a>

            <!-- Unread Chat Messages -->
            <a href="/channels" class="flex items-center justify-between px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Unread Messages</span>
                </div>
                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                      x-text="unread"></span>
            </a>
        </div>
    </div>
</div>

<script>
function notificationSystem() {
    return {
        open: false,
        pending: 0,
        unread: 0,
        
        get totalCount() {
            return this.pending + this.unread;
        },

        initNotifications() {
            // 1. Initial immediate HTTP fetch when page renders
            fetch('/notifications/counts', {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                this.pending = data.pending;
                this.unread = data.unread;
            })
            .catch(err => console.error('Initial notification fetch failed', err));

            // 2. Start SSE Stream for live real-time updates
            const eventSource = new EventSource('/notifications/stream');

            eventSource.addEventListener('update', (e) => {
                const data = JSON.parse(e.data);
                this.pending = data.pending;
                this.unread = data.unread;
            });

            eventSource.onerror = (err) => {
                console.warn('SSE stream disconnected, reconnecting...');
            };
        }
    }
}
</script>