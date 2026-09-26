<x-app-layout>
    <div class="bg-slate-950 min-h-screen text-stone-100 p-6 flex flex-col overflow-hidden relative selection:bg-indigo-500/30">
        
        {{-- Top Workspace Header Controls --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 shrink-0">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('chat.index', ['lobby' => $lobby->id, 'channel' => $currentChannel->id]) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-stone-400 hover:text-white rounded-xl transition-all text-xs font-semibold">
                        ← Back to Chat Lobby
                    </a>
                </div>
                <h2 class="text-2xl font-black text-white tracking-tight mt-3">{{ $lobby->name }} — Workspace</h2>
                <p class="text-xs text-stone-400 mt-1">Focus Target: <span class="text-indigo-400 font-medium">{{ $lobby->project_goal }}</span></p>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="toggleCalendarSidebar()" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-stone-300 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                    📅 <span id="calendar-toggle-text">Hide Calendar</span>
                </button>
                <button onclick="openNewTaskModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                    + Add New Task
                </button>
            </div>
        </div>

        {{-- Main Workspace Splitting Layout --}}
        <div class="flex flex-1 gap-6 overflow-hidden w-full relative">
            
            {{-- LEFT SIDEBAR: Lobby Members --}}
            <aside class="w-64 bg-slate-900 border border-slate-800/80 rounded-2xl p-4 flex flex-col shrink-0 h-full">
                <div class="border-b border-slate-800 pb-3 mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-stone-400 flex items-center gap-2">
                        <span>👥</span> Lobby Partners
                    </h3>
                    <p class="text-[10px] text-stone-500 mt-1">Drag a partner onto a task card to assign them instantly.</p>
                </div>
                
                <div class="flex-1 space-y-2 overflow-y-auto pr-1">
                    @foreach($lobby->members as $member)
                        <div 
                            draggable="true" 
                            ondragstart="dragMember(event, '{{ $member->id }}', '{{ $member->name }}')"
                            ondragend="dragEndMember(event)"
                            class="flex items-center gap-3 p-2.5 bg-slate-950 border border-slate-800/60 hover:border-indigo-500/50 rounded-xl cursor-grab transition-all group"
                        >
                            <div class="w-7 h-7 rounded-lg bg-indigo-600/20 text-indigo-400 font-mono text-xs font-bold flex items-center justify-center border border-indigo-500/20 uppercase">
                                {{ substr($member->name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-stone-200 truncate group-hover:text-white">{{ $member->name }}</p>
                                <span class="text-[9px] text-stone-500 font-mono">ID: {{ $member->id }}</span>
                            </div>
                            <div class="text-stone-600 group-hover:text-indigo-400 transition-colors text-xs">⋮⋮</div>
                        </div>
                    @endforeach
                </div>
            </aside>

            {{-- CENTER WORKSPACE: The Three Column Kanban Grid --}}
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4 overflow-y-auto pb-6 h-full">
                
                {{-- Column: Todo (Backlog) --}}
                <div class="flex flex-col bg-slate-900 border border-slate-800/80 rounded-2xl p-4 space-y-4 h-full">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2 shrink-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Backlog</span>
                        <span id="count-todo" class="px-2 py-0.5 bg-slate-950 text-stone-400 text-xs rounded-md font-mono font-bold">{{ collect($lobby->tasks)->where('status', 'todo')->count() }}</span>
                    </div>
                    <div id="col-todo" class="flex-1 space-y-3 overflow-y-auto min-h-[250px] transition-colors duration-150" ondragover="allowColumnDrop(event)" ondragleave="leaveColumnDrop(event)" ondrop="dropTaskOnColumn(event, 'todo')">
                        @foreach(collect($lobby->tasks)->where('status', 'todo') as $task)
                            @include('task.card', ['task' => $task])
                        @endforeach
                    </div>
                </div>

                {{-- Column: In Progress --}}
                <div class="flex flex-col bg-slate-900 border border-slate-800/80 rounded-2xl p-4 space-y-4 h-full">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2 shrink-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-400">In Progress</span>
                        <span id="count-progress" class="px-2 py-0.5 bg-slate-950 text-indigo-400 text-xs rounded-md font-mono font-bold">{{ collect($lobby->tasks)->where('status', 'progress')->count() }}</span>
                    </div>
                    <div id="col-progress" class="flex-1 space-y-3 overflow-y-auto min-h-[250px] transition-colors duration-150" ondragover="allowColumnDrop(event)" ondragleave="leaveColumnDrop(event)" ondrop="dropTaskOnColumn(event, 'progress')">
                        @foreach(collect($lobby->tasks)->where('status', 'progress') as $task)
                            @include('task.card', ['task' => $task])
                        @endforeach
                    </div>
                </div>

                {{-- Column: Done --}}
                <div class="flex flex-col bg-slate-900 border border-slate-800/80 rounded-2xl p-4 space-y-4 h-full">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2 shrink-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Completed</span>
                        <span id="count-done" class="px-2 py-0.5 bg-slate-950 text-emerald-400 text-xs rounded-md font-mono font-bold">{{ collect($lobby->tasks)->where('status', 'done')->count() }}</span>
                    </div>
                    <div id="col-done" class="flex-1 space-y-3 overflow-y-auto min-h-[250px] transition-colors duration-150" ondragover="allowColumnDrop(event)" ondragleave="leaveColumnDrop(event)" ondrop="dropTaskOnColumn(event, 'done')">
                        @foreach(collect($lobby->tasks)->where('status', 'done') as $task)
                            @include('task.card', ['task' => $task])
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDEBAR: Drop Target Calendar View (Collapsible) --}}
            <aside id="calendar-sidebar" class="w-80 bg-slate-900 border border-slate-800/80 rounded-2xl flex flex-col h-full shrink-0 overflow-hidden transition-all duration-300 ease-in-out">
                <div class="p-4 border-b border-slate-800 flex justify-between items-center bg-slate-950/40">
                    <h2 class="font-bold text-stone-200 text-sm flex items-center gap-2 tracking-tight">
                        <span>📅</span><span>July 2026</span>
                    </h2>
                    <span class="text-[10px] text-indigo-400 font-bold bg-indigo-950/50 border border-indigo-900/50 px-2 py-0.5 rounded-md">Timeline Drops</span>
                </div>
                
                <div class="grid grid-cols-7 text-center text-[10px] font-bold text-stone-400 border-b border-slate-800/60 py-2 bg-slate-950/20">
                    <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
                </div>

                <div class="grid grid-cols-7 flex-1 bg-slate-950 divide-x divide-y divide-slate-900 border-b border-slate-800">
                    {{-- Calendar Padding Days --}}
                    <div class="bg-slate-900/40 p-1 text-stone-600 text-[10px] min-h-[55px]">29</div>
                    <div class="bg-slate-900/40 p-1 text-stone-600 text-[10px] min-h-[55px]">30</div>
                    
                    {{-- July Days Grid --}}
                    @for ($day = 1; $day <= 19; $day++)
                        @php 
                            $dateString = "2026-07-" . sprintf('%02d', $day); 
                            $isToday = ($day === 15);
                        @endphp
                        <div 
                            class="p-1 min-h-[55px] bg-slate-950 text-[10px] flex flex-col justify-between transition-colors duration-150 calendar-cell border border-slate-900 {{ $isToday ? 'border-2 border-indigo-500 z-10' : '' }}" 
                            data-date="{{ $dateString }}"
                            ondragover="allowCalendarDrop(event)"
                            ondragleave="leaveCalendarDrop(event)"
                            ondrop="dropTaskOnCalendar(event, '{{ $dateString }}')"
                        >
                            <span class="{{ $isToday ? 'text-indigo-400 font-bold' : 'text-stone-400' }}">{{ $day }}</span>
                            <div class="calendar-markers-container space-y-1 mt-1" data-date-container="{{ $dateString }}">
                                @foreach(collect($lobby->tasks)->where('due_date', $dateString) as $t)
                                    <div id="cal-marker-{{ $t->id }}" class="bg-indigo-600 border border-indigo-500 text-white font-medium text-[8px] px-1 py-0.5 rounded truncate" title="{{ $t->title }}">
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
    <div id="task-modal" class="fixed inset-0 bg-slate-950/80 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
        <div class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl p-6 shadow-2xl">
            <h3 id="modal-title" class="text-base font-bold text-white mb-4">Create New Board Task</h3>
            <form id="task-form" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                
                <div>
                    <label class="block text-xs font-bold text-stone-400 uppercase mb-1">Title</label>
                    <input type="text" name="title" id="form-title" required class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3 py-2 text-sm text-stone-200 placeholder-stone-600" placeholder="Provide distinct task objective...">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-400 uppercase mb-1">Assigned Member</label>
                    <select name="user_id" id="form-user-id" required class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3 py-2 text-sm text-stone-200">
                        <option value="">Select a verified partner...</option>
                        @foreach($lobby->members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeTaskModal()" class="px-4 py-2 bg-slate-950 border border-slate-800 text-stone-400 rounded-xl text-xs font-semibold hover:text-stone-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-md">Save Task</button>
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
                ev.currentTarget.classList.add('bg-slate-800/50');
            }
        }

        function leaveColumnDrop(ev) {
            ev.currentTarget.classList.remove('bg-slate-800/50');
        }

        async function dropTaskOnColumn(ev, targetStatus) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('bg-slate-800/50');
            
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
                ev.currentTarget.classList.add('border-indigo-500', 'bg-slate-800');
            }
        }

        function leaveMemberDropOnCard(ev) {
            ev.currentTarget.classList.remove('border-indigo-500', 'bg-slate-800');
        }

        async function dropMemberOnCard(ev, taskId) {
            ev.preventDefault();
            ev.stopPropagation();
            ev.currentTarget.classList.remove('border-indigo-500', 'bg-slate-800');

            if (activeDragType !== 'member') return;

            const memberId = draggedData.memberId;
            const memberName = draggedData.memberName;

            const wrapperElement = document.getElementById(`task-card-${taskId}`);
            if (wrapperElement) {
                const assigneePlaceholder = wrapperElement.querySelector('.task-assignee-name');
                if (assigneePlaceholder) {
                    assigneePlaceholder.textContent = "👤 " + memberName;
                    assigneePlaceholder.className = 'task-assignee-name text-indigo-400 font-semibold text-xs';
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
                ev.currentTarget.classList.add('bg-slate-800');
            }
        }

        function leaveCalendarDrop(ev) {
            ev.currentTarget.classList.remove('bg-slate-800');
        }

        async function dropTaskOnCalendar(ev, targetDate) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('bg-slate-800');

            if (activeDragType !== 'task') return;
            const taskId = draggedData.taskId;
            const card = document.getElementById(`task-card-${taskId}`);
            if (!card) return;

            const existingMarker = document.getElementById(`cal-marker-${taskId}`);
            if (existingMarker) existingMarker.remove();

            const labelContainer = ev.currentTarget.querySelector('.calendar-markers-container');
            if (labelContainer) {
                const titleText = card.querySelector('.task-card-title')?.innerText || 'Task';
                const pillHtml = `<div id="cal-marker-${taskId}" class="bg-indigo-600 border border-indigo-500 text-white font-medium text-[8px] px-1 py-0.5 rounded truncate" title="${titleText}">${titleText}</div>`;
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