<?php

namespace App\Http\Controllers;

use App\Models\Lobby;
use App\Models\LobbyMember;
use Illuminate\Http\Request;

class LobbyMembershipController extends Controller
{
    public function join(Request $request, Lobby $lobby)
    {
        $user = auth()->user();

        // Check if they have an active or pending application to avoid duplicates
        $alreadyApplied = LobbyMember::where('lobby_id', $lobby->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($alreadyApplied) {
            return redirect()->back()->with('error', 'You are already an active member or in queue for this active party.');
        }

        LobbyMember::create([
            'lobby_id' => $lobby->id,
            'user_id' => $user->id,
            'role_in_team' => $request->input('role_in_team', 'Mercenary'),
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Application transmitted! Your request is pending review by the Guild Leader.');
    }

    /**
     * FIXED: Changed second parameter to explicit ID lookup. 
     */
    public function updateStatus(Request $request, $memberId, $status)
    {
        // Explicitly locate the pivot record row safely
        $member = LobbyMember::findOrFail($memberId);
        
        // Force-load the missing relationship to bypass race conditions
        $member->loadMissing('lobby');

        // Safety fallback check if route mismatch or record deletion occurred
        if (!$member->lobby) {
            abort(404, 'The target project workspace lobby link could not be resolved.');
        }

        // Gatekeeping check: Verify current user is actually the owner
        if (auth()->id() !== $member->lobby->owner_id) {
            abort(403, 'Unauthorized Guild Leader modification action.');
        }

        // Normalize incoming status modifications cleanly
        $cleanStatus = ($status === 'accepted' || $status === 'approved') ? 'accepted' : 'rejected';
        
        $member->update(['status' => $cleanStatus]);

        $message = $cleanStatus === 'accepted' 
            ? "Contract approved! Target operative is now a squad member." 
            : "Application declined and removed from queue.";

        return redirect()->back()->with('success', $message);
    }

}