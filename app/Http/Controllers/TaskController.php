<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lobby;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Display the Kanban task board view with workspace layout payloads.
     */
    public function index(Lobby $lobby)
    {
        $lobby->load(['channels', 'members', 'tasks']);

        $currentChannel = $lobby->channels->first();

        if (!$currentChannel) {
            $currentChannel = $lobby->channels()->create([
                'name' => 'general'
            ]);
        }

        return view('task.index', compact('lobby', 'currentChannel'));
    }

    public function store(Request $request, Lobby $lobby)
    {
        if (!auth()->user()->lobbies->contains($lobby->id)) {
            abort(403, 'Unauthorized workspace access.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $lobby->tasks()->create([
            'title' => $validated['title'],
            'user_id' => $validated['user_id'],
            'status' => 'todo',
        ]);

        return back()->with('success', 'Task added.');
    }

    public function update(Request $request, Task $task)
    {
        if (!auth()->user()->lobbies->contains($task->lobby_id)) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized workspace'], 403);
            }
            abort(403, 'Unauthorized workspace access.');
        }

        $validated = $request->validate([
            'title'    => 'sometimes|string|max:255',
            'user_id'  => 'sometimes|exists:users,id',
            'status'   => 'sometimes|string|in:todo,progress,done',
            'due_date' => 'sometimes|nullable|date',
        ]);

        $task->update($validated);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json($task);
        }

        return redirect()->back()->with('success', 'Task properties successfully saved.');
    }

    public function destroy(Task $task)
    {
        if (!auth()->user()->lobbies->contains($task->lobby_id)) {
            abort(403, 'Unauthorized workspace access.');
        }

        $task->delete();

        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Task card removed from board.');
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        if (!auth()->user()->lobbies->contains($task->lobby_id)) {
            return response()->json(['error' => 'Unauthorized workspace'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:todo,progress,done',
        ]);

        $task->update([
            'status' => $validated['status']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task pipeline layout successfully saved to database.'
        ]);
    }

    public function updateDueDate(Request $request, Task $task): JsonResponse
    {
        if (!auth()->user()->lobbies->contains($task->lobby_id)) {
            return response()->json(['error' => 'Unauthorized workspace'], 403);
        }

        $validated = $request->validate([
            'due_date' => 'required|date',
        ]);

        $task->update(['due_date' => $validated['due_date']]);

        return response()->json(['success' => true]);
    }
}