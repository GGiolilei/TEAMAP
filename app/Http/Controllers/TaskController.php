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
    public function index()
    {
        // Fallback: Get the first lobby the authenticated user belongs to
        $lobby = auth()->user()->lobbies()->first() ?? Lobby::first();
        
        if (!$lobby) {
            abort(404, 'No active workspace found.');
        }

        return view('task.index', compact('lobby'));
    }

    public function store(Request $request, Lobby $lobby)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        // Constructing through the relation automatically injects the correct parent 'lobby_id'
        $lobby->tasks()->create([
            'title' => $validated['title'],
            'user_id' => $validated['user_id'],
            'status' => 'todo',
        ]);

        return back()->with('success', 'Task added.');
    }

    /**
     * MODIFIED: Handles both frontend AJAX payload actions AND dynamic modal form submissions smoothly.
     */
    public function update(Request $request, Task $task)
    {
        // Fallback or explicit check if target task modification matches structural permissions
        // Note: Removed tight Auth matching constraint here so partners can edit each others' workspace details.

        $validated = $request->validate([
            'title'    => 'sometimes|string|max:255',
            'user_id'  => 'sometimes|exists:users,id',
            'status'   => 'sometimes|string|in:todo,progress,done',
            'date'     => 'sometimes|nullable|date',
            'due_date' => 'sometimes|nullable|date',
        ]);

        $task->update($validated);

        // If requested through standard form UI layout submission, return full page redirect layout context
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json($task);
        }

        return redirect()->back()->with('success', 'Task properties successfully saved.');
    }

    /**
     * MODIFIED: Handles fallback state checking if request expects structural clean HTML redirect.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Task card removed from board ecosystem.');
    }

    /**
     * PRESERVED ORIGINAL: Asynchronous state update pipeline mechanism for Drag/Drop pipeline mutations.
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        // Validate matching your exact database enum options
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
}