@php
    $tones = [
        ['bar' => 'bg-(--info)',    'tile' => 'bg-(--info-bg)',    'text' => 'text-(--info)'],
        ['bar' => 'bg-(--success)', 'tile' => 'bg-(--success-bg)', 'text' => 'text-(--success)'],
        ['bar' => 'bg-(--warning)', 'tile' => 'bg-(--warning-bg)', 'text' => 'text-(--warning)'],
        ['bar' => 'bg-(--mention)', 'tile' => 'bg-[#F8E8DC]',      'text' => 'text-(--mention)'],
    ];
    $emojis = ['✨', '🚀', '🎯', '🛠️', '📝', '💡', '🔥', '🧩'];

    $tone  = $tones[$task->id % count($tones)];
    $emoji = $emojis[$task->id % count($emojis)];
@endphp

<div
    id="task-card-{{ $task->id }}"
    draggable="true"
    ondragstart="dragTask(event, '{{ $task->id }}')"
    ondragend="dragEndTask('{{ $task->id }}')"
    ondragover="allowMemberDropOnCard(event)"
    ondragleave="leaveMemberDropOnCard(event)"
    ondrop="dropMemberOnCard(event, '{{ $task->id }}')"
    class="group relative cursor-grab overflow-hidden rounded-2xl border border-(--border) bg-(--surface) transition-all duration-200 hover:-translate-y-0.5 hover:rotate-[-.4deg] hover:border-(--muted) hover:shadow-[0_16px_28px_-18px_rgba(32,33,32,.35)] active:cursor-grabbing"
>
    {{-- Color stripe --}}
    <span class="absolute inset-y-0 left-0 w-1.5 {{ $tone['bar'] }}"></span>

    {{-- Delete button (always visible on touch, hover-only on desktop) --}}
    <button
        type="button"
        onclick="deleteTaskInline('{{ $task->id }}')"
        aria-label="Delete task"
        class="absolute right-2 top-2 z-10 grid h-8 w-8 place-items-center rounded-lg text-xs font-bold text-(--muted) opacity-100 transition hover:bg-(--error-bg) hover:text-(--error) lg:opacity-0 lg:group-hover:opacity-100"
    >
        ✕
    </button>

    {{-- Task body clicking opens edit modal --}}
    <div onclick="openEditTaskModal('{{ $task->id }}', {{ \Illuminate\Support\Js::from($task->title) }}, '{{ $task->user_id }}')" class="flex cursor-pointer items-start gap-3 py-3.5 pl-5 pr-10">
        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-lg transition-transform duration-300 group-hover:-rotate-6 group-hover:scale-110 {{ $tone['tile'] }}" aria-hidden="true">
            {{ $emoji }}
        </div>

        <div class="min-w-0 flex-1">
            <h4 class="task-card-title line-clamp-2 break-words text-sm font-bold leading-snug text-(--black)">
                {{ $task->title }}
            </h4>

            <div class="mt-2.5 flex items-center justify-between">
                <span class="inline-flex max-w-full items-center rounded-full px-2.5 py-1 {{ $tone['tile'] }}">
                    <span class="task-assignee-name truncate text-xs font-semibold {{ $tone['text'] }}">
                        👤 {{ $task->user ? $task->user->name : 'Unassigned' }}
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>