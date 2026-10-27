<div x-data="notificationToastSystem()" 
     x-init="initNotifications()" 
     class="fixed top-5 right-5 z-50 flex flex-col space-y-3 max-w-sm w-full pointer-events-none">
    
    <!-- Toast List Container -->
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full opacity-0 scale-90"
             x-transition:enter-end="translate-x-0 opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0 opacity-100 scale-100"
             x-transition:leave-end="translate-x-full opacity-0 scale-90"
             class="pointer-events-auto flex items-start p-4 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 relative overflow-hidden">
            
            <!-- Toast Icon Accent Bar & Icon -->
            <div class="flex-shrink-0 mr-3">
                <template x-if="toast.type === 'pending'">
                    <div class="p-2 rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'unread'">
                    <div class="p-2 rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </template>
            </div>

            <!-- Toast Content Body -->
            <div class="flex-1 min-w-0 pr-4">
                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="toast.title"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="toast.message"></p>
                
                <a :href="toast.link" 
                   class="inline-flex items-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline mt-2">
                    View Details
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Dismiss Button -->
            <button @click="dismiss(toast.id)" 
                    type="button" 
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
function notificationToastSystem() {
    return {
        toasts: [],
        lastPending: null,
        lastUnread: null,
        isInitialLoad: true,

        initNotifications() {
            // Connect to your StreamedResponse SSE Endpoint
            const eventSource = new EventSource("{{ route('notifications.stream') }}");

            eventSource.addEventListener('update', (e) => {
                const data = JSON.parse(e.data);

                // Prevent triggering pop-ups on the initial page load baseline
                if (!this.isInitialLoad) {
                    // Check if pending lobby requests increased
                    if (data.pending > this.lastPending) {
                        const diff = data.pending - this.lastPending;
                        this.addToast({
                            type: 'pending',
                            title: 'New Member Request!',
                            message: `You have ${diff} new pending lobby request${diff > 1 ? 's' : ''}.`,
                            link: "{{ route('lobbies.owned') }}"
                        });
                    }

                    // Check if unread chat messages increased
                    if (data.unread > this.lastUnread) {
                        const diff = data.unread - this.lastUnread;
                        this.addToast({
                            type: 'unread',
                            title: 'New Message Received',
                            message: `You have ${diff} unread message${diff > 1 ? 's' : ''} waiting.`,
                            link: "{{ route('lobbies.joined') }}"
                        });
                    }
                } else {
                    this.isInitialLoad = false;
                }

                this.lastPending = data.pending;
                this.lastUnread = data.unread;
            });

            eventSource.onerror = (err) => {
                console.warn('Notification SSE connection interrupted, retrying...');
            };
        },

        addToast({ type, title, message, link }) {
            const id = Date.now() + Math.random();
            const toast = { id, type, title, message, link, visible: true };
            
            this.toasts.push(toast);

            // Auto dismiss card after 6 seconds
            setTimeout(() => {
                this.dismiss(id);
            }, 6000);
        },

        dismiss(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                // Remove from array after transition finishes
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    }
}
</script>