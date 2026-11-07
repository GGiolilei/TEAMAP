<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserReport;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function store(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot report yourself.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        UserReport::create([
            'reporter_id'      => auth()->id(),
            'reported_user_id' => $user->id,
            'reason'           => $validated['reason'],
            'status'           => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User report submitted safely.']);
        }

        return back()->with('success', 'Report successfully filed for admin review.');
    }
}