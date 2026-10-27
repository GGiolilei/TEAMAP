<div 
    id="task-card-{{ $task->id }}"
    draggable="true" 
    ondragstart="dragTask(event, '{{ $task->id }}')"
    ondragend="dragEndTask('{{ $task->id }}')"
    ondragover="allowMemberDropOnCard(event)"
    ondragleave="leaveMemberDropOnCard(event)"
    ondrop="dropMemberOnCard(event, '{{ $task->id }}')"
    class="bg-slate-950 border border-slate-800 rounded-xl p-3 shadow-sm hover:border-slate-700 transition-all cursor-grab relative group"
>
    {{-- Delete X button on hover --}}
    <button 
        type="button"
        onclick="deleteTaskInline('{{ $task->id }}')"
        class="absolute top-2 right-2 text-stone-500 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold p-1"
    >
        ✕
    </button>

    {{-- Task body clicking opens edit modal --}}
    <div onclick="openEditTaskModal('{{ $task->id }}', '{{ addslashes($task->title) }}', '{{ $task->user_id }}')" class="cursor-pointer pr-4">
        <h4 class="task-card-title text-sm font-semibold text-stone-200 group-hover:text-white transition-colors">
            {{ $task->title }}
        </h4>
        <div class="mt-2 flex items-center justify-between">
            <span class="task-assignee-name text-[11px] text-stone-500">
                👤 {{ $task->user ? $task->user->name : 'Unassigned' }}
            </span>
        </div>
    </div>
</div>