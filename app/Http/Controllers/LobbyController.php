<?php

namespace App\Http\Controllers;

use App\Models\Lobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LobbyController extends Controller
{
    /**
     * Display a listing of available lobbies.
     */
    public function index(Request $request): View
    {
        $query = Lobby::with(['members']);

        // Handle incoming search query requests
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('project_goal', 'like', '%' . $request->search . '%');
        }

        $lobbies = $query->latest()->get();

        return view('lobbies.index', compact('lobbies'));
    }

    /**
     * Show the form for creating a new lobby.
     */
    public function create(): View
    {
        return view('lobbies.create');
    }

    /**
     * Store a newly created lobby in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate incoming form inputs
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'project_goal'   => 'required|string|max:255',
            'description'    => 'required|string',
            'max_members'    => 'required|integer|min:2|max:10',
            'required_roles' => 'nullable|string|max:255',
            'interests'      => 'required|array|min:1', // Ensures at least one tag is selected
            'interests.*'    => 'exists:interests,id',
        ]);

        // 2. Create the lobby and link it to the authenticated user as owner
        $lobby = Lobby::create([
            'owner_id'       => Auth::id(),
            'name'           => $validated['name'],
            'project_goal'   => $validated['project_goal'],
            'description'    => $validated['description'],
            'max_members'    => $validated['max_members'],
            'required_roles' => $validated['required_roles'],
            'status'         => 'active',
        ]);

        // 3. Attach the chosen interest tags to the database pivot table
        $lobby->interests()->attach($request->interests);
        $lobby->members()->attach(auth()->id());

        // 4. Redirect cleanly back to the workspace dashboard
        return redirect()->route('dashboard')->with('success', 'Your new project lobby has been launched successfully!');
    }
    public function destroy(Lobby $lobby)
{
    // Optional: Ensure only the host/owner can delete this lobby
    if (auth()->id() !== $lobby->owner_id) {
        return redirect()->back()->with('error', 'Unauthorized access.');
    }

    // Delete the lobby (cascading memberships automatically if foreign keys are configured)
    $lobby->delete();

    return redirect()->route('dashboard')->with('success', 'Workspace successfully decommissioned.');
}

    /**
     * Attach the authenticated user to the specified lobby workspace roster.
     */
    public function join(Lobby $lobby)
    {
        // 1. Security Check: Prevent the owner from joining their own lobby redundantly
        if (Auth::id() === $lobby->owner_id) {
            return redirect()->back()->with('error', 'You are already the owner/organizer of this workspace.');
        }

        // 2. Prevent duplicate entries in your pivot table
        if ($lobby->members->contains(Auth::id())) {
            return redirect()->route('chat.index', $lobby->id)
                ->with('success', 'You are already a member of this workspace.');
        }

        // 3. Attach the user using your Many-to-Many relationship mapping
        $lobby->members()->attach(Auth::id());

        // 4. Redirect to the newly opened chat screen with a success flag
        return redirect()->route('chat.index', $lobby->id)
            ->with('success', "You have successfully joined {$lobby->name}!");
    }
    public function leave(Lobby $lobby)
{
    $user = auth()->user();

    // Detach the user from the lobby's members relationship
    // This removes the record from the 'lobby_members' pivot table
    $lobby->members()->detach($user->id);

    return redirect()->back()->with('success', 'You have successfully left the workspace.');
}

    /**
     * Display a listing of owned lobbies.
     */
    public function owned(Request $request): View
    {
        $hostedLobbies = $request->user()->hostedLobbies()
            ->withCount('members')
            ->latest()
            ->get();

        return view('lobbies.owned', compact('hostedLobbies'));
    }

    /**
     * Display a listing of joined lobbies.
     */
    public function joined(Request $request): View
{
    $user = $request->user();

    $joinedLobbies = $user->lobbies()
        ->where('owner_id', '!=', $user->id)
        ->withCount('members')
        ->latest()
        ->get();

    $hostedLobbies = $user->hostedLobbies()
        ->withCount('members')
        ->latest()
        ->get();

    return view('lobbies.joined', compact('joinedLobbies', 'hostedLobbies'));
}   
}