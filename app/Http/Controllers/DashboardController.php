<?php

namespace App\Http\Controllers;

use App\Models\Lobby;
use App\Models\Interest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the unified dashboard workflow space.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Ensure user profile record initialization exists
        if (!$user->profile) {
            $user->profile()->create();
        }

        // Radar Scan: Fetch current user's attribute tags cleanly by array values
        $userInterestIds = $user->interests()->pluck('interests.id')->toArray();

        $search = $request->input('search');
        $interestId = $request->input('interest');

        // 1. Build Base Filterable Query Stack
        $baseQuery = Lobby::has('owner')->with([
            'owner', 
            'interests', 
            'members' => function ($query) {
                $query->with('profile');
            }
        ]);

        // 2. Separate recommendations logic completely BEFORE applying search/filter state mutations
        // This stops search terms from breaking your recommendation feeds!
        $recommendedQuery = Lobby::has('owner')
            ->where('owner_id', '!=', $user->id)
            ->with(['owner', 'interests']);

        if (!empty($userInterestIds)) {
            // FIXED: Using whereHas on the relationship to match pivot associations cleanly
            $recommendedQuery->withCount(['interests as matching_score' => function ($query) use ($userInterestIds) {
                $query->whereIn('interest_id', $userInterestIds); // Match pivot foreign key column name
            }]);
        }

        $recommendedLobbies = $recommendedQuery
            ->orderBy('matching_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($lobby) {
                // If there are no interests matching, ensure it defaults safely to 0
                $score = $lobby->matching_score ?? 0;
                $percentage = $score * 20; 
                $lobby->match_percentage = min($percentage, 100); 
                return $lobby;
            });

        // 3. Apply general dashboard global searching to the regular feed arrays
        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('project_goal', 'LIKE', "%{$search}%");
            });
        }

        if ($interestId) {
            $baseQuery->whereHas('interests', function ($q) use ($interestId) {
                $q->where('interests.id', $interestId);
            });
        }

        // 4. Fetch Freshly Discovered Guilds (Global Feed using mutated base constraints)
        $newestLobbies = (clone $baseQuery)
            ->latest()
            ->take(6)
            ->get();

        // 5. Fetch User's Active Parties & Connected Roster Nodes
        $joinedLobbies = Lobby::has('owner')
            ->whereHas('members', function ($q) use ($user) {
                $q->where('lobby_members.user_id', $user->id)
                  ->where('lobby_members.status', 'accepted'); 
            })
            ->with([
                'owner',
                'interests',
                'members' => function ($query) {
                    $query->with('profile');
                }
            ])
            ->get();

        // 6. Fetch incoming applications using explicit pivot intermediate extraction loaders
        $ownedLobbiesWithRequests = Lobby::has('owner')
            ->where('owner_id', $user->id)
            ->whereHas('members', function ($query) {
                $query->where('lobby_members.status', 'pending');
            })
            ->with([
                'interests',
                'members' => function ($query) {
                    $query->where('lobby_members.status', 'pending')
                          ->withPivot('id', 'status', 'created_at')
                          ->with('profile');
                }
            ])
            ->get();

        // Fetch global filter tags for search bar support dropdown menus
        $allInterests = Interest::all();

        // Return unified collection payload to dashboard blade
        return view('dashboard', compact(
            'recommendedLobbies', 
            'newestLobbies', 
            'joinedLobbies', 
            'ownedLobbiesWithRequests',
            'allInterests'
        ));
    }
}