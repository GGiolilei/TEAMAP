<x-app-layout>
    <div class="bg-[#F7F7F5] min-h-screen text-[#242524] p-6 flex flex-col overflow-hidden relative selection:bg-[#202120] selection:text-white">

        {{-- Top Workspace Header Controls --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 shrink-0">
            <div>
                <a href="{{ route('chat.index', ['lobby' => $lobby->id, 'channel' => $currentChannel->id]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-[#EEEEEC] border border-[#E1E1DE] text-[#747674] hover:text-[#202120] rounded-xl transition text-xs font-semibold">
                    ← Back to Chat Lobby
                </a>
                <h2 class="text-2xl font-bold text-[#202120] tracking-tight mt-3">{{ $lobby->name }} <span class="text-[#9A9C9A] font-medium">/ Workspace</span></h2>
                <p class="text-xs text-[#747674] mt-1">Focus target: <span class="text-[#202120] font-semibold">{{ $lobby->project_goal }}</span></p>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="toggleCalendarSidebar()"
                        class="px-3.5 py-2 bg-white hover:bg-[#EEEEEC] border border-[#E1E1DE] text-[#3A3B3A] rounded-xl text-xs font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#747674]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"/></svg>
                    <span id="calendar-toggle-text">Hide Calendar</span>
                </button>
                <button onclick="openNewTaskModal()"
                        class="px-4 py-2 bg-[#252625] hover:bg-[#3A3B3A] text-white rounded-xl text-xs font-semibold transition">
                    + Add New Task
                </button>
            </div>
        </div>

        {{-- Main Workspace Splitting Layout --}}
        <div class="flex flex-1 gap-5 overflow-hidden w-full relative">

            {{-- LEFT SIDEBAR: Lobby Members --}}
            <aside class="w-64 bg-white border border-[#E1E1DE] rounded-2xl p-4 flex flex-col shrink-0 h-full">
                <div class="border-b border-[#E1E1DE] pb-3 mb-4">
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-[#747674]">Lobby Partners</h3>
                    <p class="text-[10px] text-[#9A9C9A] mt-1 leading-relaxed">Drag a partner onto a task card to assign them instantly.</p>
                </div>

                <div class="flex-1 space-y-2 overflow-y-auto pr-1">
                    @foreach($lobby->members as $member)
                        <div
                            draggable="true"
                            ondragstart="dragMember(event, '{{ $member->id }}', '{{ $member->name }}')"
                            ondragend="dragEndMember(event)"
                            class="flex items-center gap-3 p-2.5 bg-[#F7F7F5] border border-[#E1E1DE] hover:border-[#9A9C9A] hover:bg-[#EEEEEC] rounded-xl cursor-grab transition group"
                        >
                            <div class="w-7 h-7 rounded-lg bg-[#E9E9E7] text-[#3A3B3A] text-xs font-bold flex items-center justify-center uppercase">
                                {{ substr($member->name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[#242524] truncate">{{ $member->name }}</p>
                                <span class="text-[9px] text-[#9A9C9A] font-mono">ID: {{ $member->id }}</span>
                            </div>
                            <div class="text-[#9A9C9A] group-hover:text-[#3A3B3A] transition-colors text-xs">⋮⋮</div>
                        </div>
                    @endforeach
                </div>
            </aside>

            {{-- CENTER WORKSPACE: The Three Column Kanban Grid --}}
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4 overflow-y-auto pb-6 h-full">

                {{-- Column: Todo (Backlog) --}}
                <div class="flex flex-col bg-white border border-[#E1E1DE] rounded-2xl p-4 space-y-4 h-full">
                    <div class="flex items-center justify-between border-b border-[#E1E1DE] pb-3 shrink-0">
                        <span class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-[#747674]">
                            <span class="w-2 h-2 rounded-full bg-[#9A9C9A]"></span> Backlog
                        </span>
                        <span id="count-todo" class="px-2 py-0.5 bg-[#F4F4F2] border border-[#E1E1DE] text-[#747674] text-xs rounded-md font-mono font-semibold">{{ collect($lobby->tasks)->where('status', 'todo')->count() }}</span>
                    </div>
                    <div id="col-todo" class="flex-1 space-y-3 overflow-y-auto min-h-[250px] rounded-xl transition-colors duration-150" ondragover="allowColumnDrop(event)" ondragleave="leaveColumnDrop(event)" ondrop="dropTaskOnColumn(event, 'todo')">
                        @foreach(collect($lobby->tasks)->where('status', 'todo') as $task)
                            @include('task.card', ['task' => $task])
                        @endforeach
                    </div>
                </div>

                {{-- Column: In Progress --}}
                <div class="flex flex-col bg-white border border-[#E1E1DE] rounded-2xl p-4 space-y-4 h-full">
                    <div class="flex items-center justify-between border-b border-[#E1E1DE] pb-3 shrink-0">
                        <span class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-[#747674]">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> In Progress
                        </span>
                        <span id="count-progress" class="px-2 py-0.5 bg-[#F4F4F2] border border-[#E1E1DE] text-[#747674] text-xs rounded-md font-mono font-semibold">{{ collect($lobby->tasks)->where('status', 'progress')->count() }}</span>
                    </div>
                    <div id="col-progress" class="flex-1 space-y-3 overflow-y-auto min-h-[250px] rounded-xl transition-colors duration-150" ondragover="allowColumnDrop(event)" ondragleave="leaveColumnDrop(event)" ondrop="dropTaskOnColumn(event, 'progress')">
                        @foreach(collect($lobby->tasks)->where('status', 'progress') as $task)
                            @include('task.card', ['task' => $task])
                        @endforeach
                    </div>
                </div>

                {{-- Column: Done --}}
                <div class="flex flex-col bg-white border border-[#E1E1DE] rounded-2xl p-4 space-y-4 h-full">
                    <div class="flex items-center justify-between border-b border-[#E1E1DE] pb-3 shrink-0">
                        <span class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-[#747674]">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Completed
                        </span>
                        <span id="count-done" class="px-2 py-0.5 bg-[#F4F4F2] border border-[#E1E1DE] text-[#747674] text-xs rounded-md font-mono font-semibold">{{ collect($lobby->tasks)->where('status', 'done')->count() }}</span>
                    </div>
                    <div id="col-done" class="flex-1 space-y-3 overflow-y-auto min-h-[250px] rounded-xl transition-colors duration-150" ondragover="allowColumnDrop(event)" ondragleave="leaveColumnDrop(event)" ondrop="dropTaskOnColumn(event, 'done')">
                        @foreach(collect($lobby->tasks)->where('status', 'done') as $task)
                            @include('task.card', ['task' => $task])
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDEBAR: Drop Target Calendar View (Collapsible) --}}
            <aside id="calendar-sidebar" class="w-80 bg-white border border-[#E1E1DE] rounded-2xl flex flex-col h-full shrink-0 overflow-hidden transition-all duration-300 ease-in-out">
                <div class="p-4 border-b border-[#E1E1DE] flex justify-between items-center">
                    <h2 class="font-bold text-[#202120] text-sm tracking-tight">July 2026</h2>
                    <span class="text-[10px] text-[#747674] font-semibold bg-[#F4F4F2] border border-[#E1E1DE] px-2 py-0.5 rounded-md">Timeline Drops</span>
                </div>

                <div class="grid grid-cols-7 text-center text-[10px] font-semibold text-[#9A9C9A] border-b border-[#E1E1DE] py-2 bg-[#F7F7F5]">
                    <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
                </div>

                <div class="grid grid-cols-7 flex-1 bg-white divide-x divide-y divide-[#E1E1DE] border-b border-[#E1E1DE]">
                    {{-- Calendar Padding Days --}}
                    <div class="bg-[#F7F7F5] p-1 text-[#9A9C9A] text-[10px] min-h-[55px]">29</div>
                    <div class="bg-[#F7F7F5] p-1 text-[#9A9C9A] text-[10px] min-h-[55px]">30</div>

                    {{-- July Days Grid --}}
                    @for ($day = 1; $day <= 19; $day++)
                        @php
                            $dateString = "2026-07-" . sprintf('%02d', $day);
                            $isToday = ($day === 15);
                        @endphp
                        <div
                            class="p-1 min-h-[55px] bg-white text-[10px] flex flex-col justify-between transition-colors duration-150 calendar-cell {{ $isToday ? 'ring-2 ring-inset ring-[#202120] z-10' : '' }}"
                            data-date="{{ $dateString }}"
                            ondragover="allowCalendarDrop(event)"
                            ondragleave="leaveCalendarDrop(event)"
                            ondrop="dropTaskOnCalendar(event, '{{ $dateString }}')"
                        >
                            <span class="{{ $isToday ? 'text-[#202120] font-bold' : 'text-[#747674]' }}">{{ $day }}</span>
                            <div class="calendar-markers-container space-y-1 mt-1" data-date-container="{{ $dateString }}">
                                @foreach(collect($lobby->tasks)->where('due_date', $dateString) as $t)
                                    <div id="cal-marker-{{ $t->id }}" class="bg-[#252625] text-white font-medium text-[8px] px-1 py-0.5 rounded truncate" title="{{ $t->title }}">
                                        {{ $t->title }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>
            </aside>
        </div>
    </div>

    {{-- MUTABLE TASK MODAL (Handles both Create & Edit dynamically) --}}
    <div id="task-modal" class="fixed inset-0 bg-[#202120]/40 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
        <div class="bg-white border border-[#E1E1DE] w-full max-w-md rounded-2xl p-6 shadow-2xl shadow-black/10">
            <h3 id="modal-title" class="text-base font-bold text-[#202120] mb-5">Create New Board Task</h3>
            <form id="task-form" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div>
                    <label class="block text-xs font-semibold text-[#3A3B3A] uppercase tracking-wider mb-1.5">Title</label>
                    <input type="text" name="title" id="form-title" required
                           class="w-full bg-white border border-[#E1E1DE] focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none rounded-xl px-3.5 py-2.5 text-sm text-[#242524] placeholder-[#9A9C9A] transition"
                           placeholder="Provide distinct task objective...">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#3A3B3A] uppercase tracking-wider mb-1.5">Assigned Member</label>
                    <select name="user_id" id="form-user-id" required
                            class="w-full bg-white border border-[#E1E1DE] focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none rounded-xl px-3.5 py-2.5 text-sm text-[#242524] transition">
                        <option value="">Select a verified partner...</option>
                        @foreach($lobby->members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#E1E1DE]">
                    <button type="button" onclick="closeTaskModal()" class="px-4 py-2 bg-white border border-[#E1E1DE] text-[#747674] rounded-xl text-xs font-semibold hover:bg-[#EEEEEC] hover:text-[#202120] transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#252625] hover:bg-[#3A3B3A] text-white font-semibold rounded-xl text-xs transition">Save Task</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Frontend Interaction State Engine --}}
    <script>
        let activeDragType = null;
        let draggedData = {};

        // 1. SIDEBAR TOGGLE MECHANICS
        function toggleCalendarSidebar() {
            const sidebar = document.getElementById('calendar-sidebar');
            const toggleText = document.getElementById('calendar-toggle-text');
            if (sidebar.classList.contains('w-80')) {
                sidebar.classList.remove('w-80', 'p-4', 'border');
                sidebar.classList.add('w-0', 'opacity-0', 'overflow-hidden', 'border-0');
                toggleText.innerText = "Show Calendar";
            } else {
                sidebar.classList.remove('w-0', 'opacity-0', 'border-0');
                sidebar.classList.add('w-80', 'p-4', 'border');
                toggleText.innerText = "Hide Calendar";
            }
        }

        // 2. KANBAN TASK DRAG & DROP
        function dragTask(ev, taskId) {
            activeDragType = 'task';
            draggedData.taskId = taskId;
            ev.dataTransfer.setData("text/plain", taskId);
            ev.dataTransfer.effectAllowed = "move";

            const cardElement = document.getElementById(`task-card-${taskId}`);
            if (cardElement) {
                setTimeout(() => cardElement.classList.add('opacity-40'), 0);
            }
        }

        function dragEndTask(taskId) {
            const cardElement = document.getElementById(`task-card-${taskId}`);
            if (cardElement) {
                cardElement.classList.remove('opacity-40');
            }
            clearDragContext();
        }

        function allowColumnDrop(ev) {
            if (activeDragType === 'task') {
                ev.preventDefault();
                ev.currentTarget.classList.add('bg-[#EEEEEC]');
            }
        }

        function leaveColumnDrop(ev) {
            ev.currentTarget.classList.remove('bg-[#EEEEEC]');
        }

        async function dropTaskOnColumn(ev, targetStatus) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('bg-[#EEEEEC]');

            if (activeDragType !== 'task') return;
            const taskId = draggedData.taskId;
            const card = document.getElementById(`task-card-${taskId}`);

            if (!card) return;
            ev.currentTarget.appendChild(card);

            updateColumnCounters();
            await sendStateMutation(`/tasks/${taskId}/status`, { status: targetStatus });
        }

        function updateColumnCounters() {
            ['todo', 'progress', 'done'].forEach(status => {
                const column = document.getElementById(`col-${status}`);
                const counter = document.getElementById(`count-${status}`);
                if (column && counter) {
                    counter.innerText = column.children.length;
                }
            });
        }

        // 3. MEMBER DRAG & DROP
        function dragMember(ev, memberId, memberName) {
            activeDragType = 'member';
            draggedData.memberId = memberId;
            draggedData.memberName = memberName;
            ev.dataTransfer.setData("application/json", JSON.stringify({ id: memberId, name: memberName }));
            ev.dataTransfer.effectAllowed = "copyMove";
        }

        function dragEndMember(ev) {
            clearDragContext();
        }

        function clearDragContext() {
            activeDragType = null;
            draggedData = {};
        }

        function allowMemberDropOnCard(ev) {
            if (activeDragType === 'member') {
                ev.preventDefault();
                ev.currentTarget.classList.add('border-[#202120]', 'bg-[#EEEEEC]');
            }
        }

        function leaveMemberDropOnCard(ev) {
            ev.currentTarget.classList.remove('border-[#202120]', 'bg-[#EEEEEC]');
        }

        async function dropMemberOnCard(ev, taskId) {
            ev.preventDefault();
            ev.stopPropagation();
            ev.currentTarget.classList.remove('border-[#202120]', 'bg-[#EEEEEC]');

            if (activeDragType !== 'member') return;

            const memberId = draggedData.memberId;
            const memberName = draggedData.memberName;

            const wrapperElement = document.getElementById(`task-card-${taskId}`);
            if (wrapperElement) {
                const assigneePlaceholder = wrapperElement.querySelector('.task-assignee-name');
                if (assigneePlaceholder) {
                    assigneePlaceholder.textContent = "👤 " + memberName;
                    assigneePlaceholder.className = 'task-assignee-name text-[#202120] font-semibold text-xs';
                }
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                await fetch(`/tasks/${taskId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        user_id: memberId
                    })
                });
            } catch (err) {
                console.error("Communication failure synchronizing mutations:", err);
            }
        }

        // 4. CALENDAR DROPS
        function allowCalendarDrop(ev) {
            if (activeDragType === 'task') {
                ev.preventDefault();
                ev.currentTarget.classList.add('bg-[#EEEEEC]');
            }
        }

        function leaveCalendarDrop(ev) {
            ev.currentTarget.classList.remove('bg-[#EEEEEC]');
        }

        async function dropTaskOnCalendar(ev, targetDate) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('bg-[#EEEEEC]');

            if (activeDragType !== 'task') return;
            const taskId = draggedData.taskId;
            const card = document.getElementById(`task-card-${taskId}`);
            if (!card) return;

            const existingMarker = document.getElementById(`cal-marker-${taskId}`);
            if (existingMarker) existingMarker.remove();

            const labelContainer = ev.currentTarget.querySelector('.calendar-markers-container');
            if (labelContainer) {
                const titleText = card.querySelector('.task-card-title')?.innerText || 'Task';
                const pillHtml = `<div id="cal-marker-${taskId}" class="bg-[#252625] text-white font-medium text-[8px] px-1 py-0.5 rounded truncate" title="${titleText}">${titleText}</div>`;
                labelContainer.insertAdjacentHTML('beforeend', pillHtml);
            }

            await sendStateMutation(`/tasks/${taskId}/due-date`, { due_date: targetDate });
        }

        // 5. MODAL ACTIONS (CREATE & EDIT TRELLO STYLE)
        function openNewTaskModal() {
            document.getElementById('modal-title').innerText = "Create New Board Task";
            document.getElementById('task-form').action = "{{ route('lobbies.tasks.store', $lobby->id) }}";
            document.getElementById('form-method').value = "POST";
            document.getElementById('form-title').value = "";
            document.getElementById('form-user-id').value = "";
            document.getElementById('task-modal').classList.remove('hidden');
        }

        function openEditTaskModal(taskId, currentTitle, currentUserId) {
            document.getElementById('modal-title').innerText = "Edit Task Properties";
            document.getElementById('task-form').action = `/tasks/${taskId}`;
            document.getElementById('form-method').value = "PUT";
            document.getElementById('form-title').value = currentTitle;
            document.getElementById('form-user-id').value = currentUserId;
            document.getElementById('task-modal').classList.remove('hidden');
        }

        function closeTaskModal() {
            document.getElementById('task-modal').classList.add('hidden');
        }

        // 6. DELETE ACTION
        async function deleteTaskInline(taskId) {
            if (!confirm('Are you sure you want to permanently delete this task card?')) return;

            const card = document.getElementById(`task-card-${taskId}`);
            const marker = document.getElementById(`cal-marker-${taskId}`);

            if (card) card.remove();
            if (marker) marker.remove();
            updateColumnCounters();

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                await fetch(`/tasks/${taskId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({ '_method': 'DELETE' })
                });
            } catch (err) {
                console.error("Failed to delete task resource:", err);
            }
        }

        // Global Fetch Pipeline Wrapper
        async function sendStateMutation(url, payload) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });
            } catch (err) {
                console.error("Communication failure synchronizing mutations:", err);
            }
        }
    </script>
</x-app-layout>