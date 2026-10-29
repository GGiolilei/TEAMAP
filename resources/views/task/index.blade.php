<x-app-layout>
    <style>
        .t-card { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; }
        .t-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            min-height: 44px; padding: 0 16px; border-radius: 12px;
            font-size: .875rem; font-weight: 600; transition: background .15s, transform .1s, color .15s;
            -webkit-tap-highlight-color: transparent;
        }
        .t-btn:active { transform: scale(.97); }
        .t-btn-primary { background: var(--btn); color: #fff; }
        .t-btn-primary:hover { background: var(--black-2); }
        .t-btn-ghost { background: var(--surface); color: var(--text); border: 1px solid var(--border); }
        .t-btn-ghost:hover { background: var(--hover); }

        .t-field {
            display: block; width: 100%; min-height: 48px; border-radius: 12px; border: 1px solid var(--border);
            background: var(--bg); padding: 0 14px; font-size: 1rem; color: var(--text); outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .t-field::placeholder { color: var(--muted); }
        .t-field:focus { border-color: var(--btn); background: var(--surface); box-shadow: 0 0 0 4px var(--selected); }

        .no-scrollbar { scrollbar-width: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }

        /* Calendar collapse (works at every screen size) */
        #calendar-sidebar[data-collapsed="true"] { display: none; }

        @keyframes sheetIn {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .modal-panel { animation: sheetIn .22s ease-out; }

        .member-chip { transition: border-color .15s, background .15s, transform .1s; -webkit-tap-highlight-color: transparent; }
        .member-chip:hover { border-color: var(--muted); background: var(--hover); }
        .member-chip:active { cursor: grabbing; }

        @media (prefers-reduced-motion: reduce) { .modal-panel { animation: none; } }
    </style>

    <div class="mx-auto flex w-full max-w-[1600px] flex-col px-4 py-5 pb-12 sm:px-6">

        {{-- ===================== HEADER ===================== --}}
        <header class="mb-5 flex shrink-0 flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <a href="{{ route('chat.index', ['lobby' => $lobby->id]) }}"
                   class="inline-flex min-h-10 items-center gap-1.5 rounded-xl border border-(--border) bg-(--surface) px-3.5 text-sm font-semibold text-(--text) transition hover:bg-(--hover)">
                    ← Back to Chat Lobby
                </a>
                <h1 class="mt-3 truncate text-2xl font-extrabold tracking-tight text-(--black) sm:text-3xl">{{ $lobby->name }} <span class="font-semibold text-(--muted)">— Workspace</span></h1>
                <p class="mt-1 text-sm text-(--text-2)">Focus target: <span class="font-semibold text-(--text)">{{ $lobby->project_goal }}</span></p>
            </div>

            <div class="flex items-center gap-2.5">
                <button onclick="toggleCalendarSidebar()" class="t-btn t-btn-ghost flex-1 sm:flex-none">
                    <svg class="h-4 w-4 text-(--text-2)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" /></svg>
                    <span id="calendar-toggle-text">Hide Calendar</span>
                </button>
                <button onclick="openNewTaskModal()" class="t-btn t-btn-primary flex-1 sm:flex-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Add New Task
                </button>
            </div>
        </header>

        {{-- ===================== WORKSPACE ===================== --}}
        <div class="flex flex-1 flex-col gap-4 xl:flex-row xl:items-start">

            {{-- LEFT: Lobby Members (stays in view while the board scrolls) --}}
            <aside class="t-card flex shrink-0 flex-col p-4 xl:sticky xl:top-20 xl:w-64">
                <div class="mb-3 border-b border-(--border) pb-3">
                    <h3 class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-(--text-2)">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Lobby Partners
                    </h3>
                    <p class="mt-1 hidden text-xs leading-relaxed text-(--muted) xl:block">Drag a partner onto a task card to assign them instantly.</p>
                </div>

                <div class="no-scrollbar flex gap-2 overflow-x-auto xl:max-h-[calc(100dvh-15rem)] xl:flex-col xl:overflow-y-auto xl:overflow-x-visible">
                    @foreach($lobby->members as $member)
                        <div
                            draggable="true"
                            ondragstart="dragMember(event, '{{ $member->id }}', {{ \Illuminate\Support\Js::from($member->name) }})"
                            ondragend="dragEndMember(event)"
                            class="member-chip group flex w-48 shrink-0 cursor-grab items-center gap-3 rounded-xl border border-(--border) bg-(--bg) p-2.5 xl:w-auto"
                        >
                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-(--bubble-me) text-xs font-bold uppercase text-(--black)">
                                {{ substr($member->name, 0, 2) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-(--text)">{{ $member->name }}</p>
                                <span class="font-mono text-[11px] text-(--muted)">ID: {{ $member->id }}</span>
                            </div>
                            <svg class="hidden h-4 w-4 shrink-0 text-(--muted) transition group-hover:text-(--text) xl:block" fill="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
                        </div>
                    @endforeach
                </div>
            </aside>

            {{-- BOARD + CALENDAR: side by side from lg up so tasks can always be dropped on the calendar --}}
            <div class="flex min-w-0 flex-1 flex-col gap-4 lg:flex-row lg:items-start">

            {{-- CENTER: Kanban --}}
            <div class="no-scrollbar -mx-4 flex min-w-0 flex-1 snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-1 sm:-mx-6 sm:px-6 lg:mx-0 lg:grid lg:grid-cols-3 lg:overflow-x-visible lg:px-0">

                {{-- Column: Todo (Backlog) --}}
                <div class="t-card flex w-[86%] shrink-0 snap-start flex-col p-4 sm:w-[48%] lg:w-auto">
                    <div class="mb-3 flex shrink-0 items-center justify-between border-b border-(--border) pb-3">
                        <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-(--text-2)">
                            <span class="h-2 w-2 rounded-full bg-(--muted)"></span> Backlog
                        </span>
                        <span id="count-todo" class="rounded-full bg-(--selected) px-2.5 py-0.5 font-mono text-xs font-bold text-(--black)">{{ collect($lobby->tasks)->where('status', 'todo')->count() }}</span>
                    </div>
                    <div id="col-todo" class="min-h-[250px] flex-1 space-y-3 rounded-xl transition-colors duration-150" ondragover="allowColumnDrop(event)" ondragleave="leaveColumnDrop(event)" ondrop="dropTaskOnColumn(event, 'todo')">
                        @foreach(collect($lobby->tasks)->where('status', 'todo') as $task)
                            @include('task.card', ['task' => $task])
                        @endforeach
                    </div>
                </div>

                {{-- Column: In Progress --}}
                <div class="t-card flex w-[86%] shrink-0 snap-start flex-col p-4 sm:w-[48%] lg:w-auto">
                    <div class="mb-3 flex shrink-0 items-center justify-between border-b border-(--border) pb-3">
                        <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-(--text-2)">
                            <span class="h-2 w-2 rounded-full bg-(--info)"></span> In Progress
                        </span>
                        <span id="count-progress" class="rounded-full bg-(--info-bg) px-2.5 py-0.5 font-mono text-xs font-bold text-(--info)">{{ collect($lobby->tasks)->where('status', 'progress')->count() }}</span>
                    </div>
                    <div id="col-progress" class="min-h-[250px] flex-1 space-y-3 rounded-xl transition-colors duration-150" ondragover="allowColumnDrop(event)" ondragleave="leaveColumnDrop(event)" ondrop="dropTaskOnColumn(event, 'progress')">
                        @foreach(collect($lobby->tasks)->where('status', 'progress') as $task)
                            @include('task.card', ['task' => $task])
                        @endforeach
                    </div>
                </div>

                {{-- Column: Done --}}
                <div class="t-card flex w-[86%] shrink-0 snap-start flex-col p-4 sm:w-[48%] lg:w-auto">
                    <div class="mb-3 flex shrink-0 items-center justify-between border-b border-(--border) pb-3">
                        <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-(--text-2)">
                            <span class="h-2 w-2 rounded-full bg-(--success)"></span> Completed
                        </span>
                        <span id="count-done" class="rounded-full bg-(--success-bg) px-2.5 py-0.5 font-mono text-xs font-bold text-(--success)">{{ collect($lobby->tasks)->where('status', 'done')->count() }}</span>
                    </div>
                    <div id="col-done" class="min-h-[250px] flex-1 space-y-3 rounded-xl transition-colors duration-150" ondragover="allowColumnDrop(event)" ondragleave="leaveColumnDrop(event)" ondrop="dropTaskOnColumn(event, 'done')">
                        @foreach(collect($lobby->tasks)->where('status', 'done') as $task)
                            @include('task.card', ['task' => $task])
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT: Calendar (collapsible) --}}
            <aside id="calendar-sidebar" data-collapsed="false" class="t-card flex shrink-0 flex-col overflow-hidden lg:sticky lg:top-20 lg:max-h-[calc(100dvh-6rem)] lg:w-72 xl:w-80">
                <div class="flex items-center justify-between border-b border-(--border) bg-(--bg) px-4 py-3.5">
                    <h2 class="flex items-center gap-2 text-sm font-bold tracking-tight text-(--black)">
                        <svg class="h-4 w-4 text-(--text-2)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" /></svg>
                        July 2026
                    </h2>
                    <span class="rounded-full bg-(--info-bg) px-2.5 py-1 text-[11px] font-bold text-(--info)">Timeline drops</span>
                </div>

                <div class="grid grid-cols-7 border-b border-(--border) py-2 text-center text-[11px] font-bold uppercase tracking-wide text-(--muted)">
                    <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
                </div>

                <div class="grid flex-1 grid-cols-7 divide-x divide-y divide-(--border) overflow-y-auto bg-(--surface)">
                    {{-- Calendar Padding Days --}}
                    <div class="min-h-[64px] bg-(--bg) p-1.5 text-[11px] text-(--muted)">29</div>
                    <div class="min-h-[64px] bg-(--bg) p-1.5 text-[11px] text-(--muted)">30</div>

                    {{-- July Days Grid --}}
                    @for ($day = 1; $day <= 19; $day++)
                        @php
                            $dateString = "2026-07-" . sprintf('%02d', $day);
                            $isToday = ($day === 15);
                        @endphp
                        <div
                            class="calendar-cell flex min-h-[64px] flex-col justify-between bg-(--surface) p-1.5 text-[11px] transition-colors duration-150 {{ $isToday ? 'z-10 ring-2 ring-inset ring-(--btn)' : '' }}"
                            data-date="{{ $dateString }}"
                            ondragover="allowCalendarDrop(event)"
                            ondragleave="leaveCalendarDrop(event)"
                            ondrop="dropTaskOnCalendar(event, '{{ $dateString }}')"
                        >
                            <span class="{{ $isToday ? 'font-extrabold text-(--black)' : 'font-medium text-(--text-2)' }}">{{ $day }}</span>
                            <div class="calendar-markers-container mt-1 space-y-1" data-date-container="{{ $dateString }}">
                                @foreach(collect($lobby->tasks)->where('due_date', $dateString) as $t)
                                    <div id="cal-marker-{{ $t->id }}" class="truncate rounded bg-(--btn) px-1.5 py-0.5 text-[10px] font-medium text-white" title="{{ $t->title }}">
                                        {{ $t->title }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>
            </aside>

            </div>{{-- /board + calendar --}}
        </div>
    </div>

    {{-- MUTABLE TASK MODAL (Handles both Create & Edit dynamically) --}}
    <div id="task-modal" class="fixed inset-0 z-50 flex hidden items-end justify-center sm:items-center sm:p-4">
        <div class="absolute inset-0 bg-(--black)/40 backdrop-blur-sm" onclick="closeTaskModal()"></div>

        <div class="modal-panel relative w-full max-w-md rounded-t-3xl border border-(--border) bg-(--surface) p-6 shadow-2xl sm:rounded-3xl">
            <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-(--selected) sm:hidden"></div>
            <h3 id="modal-title" class="mb-5 text-lg font-bold tracking-tight text-(--black)">Create New Board Task</h3>

            <form id="task-form" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-(--text-2)">Title</label>
                    <input type="text" name="title" id="form-title" required class="t-field" placeholder="Provide distinct task objective...">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-(--text-2)">Assigned Member</label>
                    <select name="user_id" id="form-user-id" required class="t-field">
                        <option value="">Select a verified partner...</option>
                        @foreach($lobby->members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <button type="button" onclick="closeTaskModal()" class="t-btn t-btn-ghost flex-1 sm:flex-none">Cancel</button>
                    <button type="submit" class="t-btn t-btn-primary flex-1 sm:flex-none">Save Task</button>
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
            const isCollapsed = sidebar.dataset.collapsed === 'true';
            sidebar.dataset.collapsed = isCollapsed ? 'false' : 'true';
            toggleText.innerText = isCollapsed ? "Hide Calendar" : "Show Calendar";
        }

        // Start collapsed only on phones/tablets (stacked layout) so the board comes first
        if (window.matchMedia('(max-width: 1023px)').matches) {
            toggleCalendarSidebar();
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
                ev.currentTarget.classList.add('bg-(--hover)');
            }
        }

        function leaveColumnDrop(ev) {
            ev.currentTarget.classList.remove('bg-(--hover)');
        }

        async function dropTaskOnColumn(ev, targetStatus) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('bg-(--hover)');

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
                ev.currentTarget.classList.add('border-(--btn)', 'bg-(--hover)');
            }
        }

        function leaveMemberDropOnCard(ev) {
            ev.currentTarget.classList.remove('border-(--btn)', 'bg-(--hover)');
        }

        async function dropMemberOnCard(ev, taskId) {
            ev.preventDefault();
            ev.stopPropagation();
            ev.currentTarget.classList.remove('border-(--btn)', 'bg-(--hover)');

            if (activeDragType !== 'member') return;

            const memberId = draggedData.memberId;
            const memberName = draggedData.memberName;

            const wrapperElement = document.getElementById(`task-card-${taskId}`);
            if (wrapperElement) {
                const assigneePlaceholder = wrapperElement.querySelector('.task-assignee-name');
                if (assigneePlaceholder) {
                    assigneePlaceholder.textContent = "👤 " + memberName;
                    assigneePlaceholder.className = 'task-assignee-name text-(--text) font-semibold text-xs';
                }
            }

            // FIX: Send via PUT to your general update route and include a spoofed header or method parameter
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                await fetch(`/tasks/${taskId}`, {
                    method: 'POST', // standard HTML wrapper behavior
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', // Force laravel to handle it as JSON response
                        'X-CSRF-TOKEN': csrfToken || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        _method: 'PUT', // Spoof the PUT method for Laravel's Route::put
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
                ev.currentTarget.classList.add('bg-(--hover)');
            }
        }

        function leaveCalendarDrop(ev) {
            ev.currentTarget.classList.remove('bg-(--hover)');
        }

        async function dropTaskOnCalendar(ev, targetDate) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('bg-(--hover)');

            if (activeDragType !== 'task') return;
            const taskId = draggedData.taskId;
            const card = document.getElementById(`task-card-${taskId}`);
            if (!card) return;

            // Remove existing calendar marker if any
            const existingMarker = document.getElementById(`cal-marker-${taskId}`);
            if (existingMarker) existingMarker.remove();

            const labelContainer = ev.currentTarget.querySelector('.calendar-markers-container');
            if (labelContainer) {
                const titleText = card.querySelector('.task-card-title')?.innerText || 'Task';
                const pillHtml = `<div id="cal-marker-${taskId}" class="truncate rounded bg-(--btn) px-1.5 py-0.5 text-[10px] font-medium text-white" title="${titleText}">${titleText}</div>`;
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