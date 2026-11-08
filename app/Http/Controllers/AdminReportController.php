<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lobby;
use App\Models\Interest;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index()
    {
        // 1. Overall System Stats
        $activeUserCount = User::count();
        $lobbyCreatedCount = Lobby::count();

        // 2. Most Applied Interests (Category breakdown for Pie Chart)
        $categoryStats = Interest::withCount('users')
            ->orderByDesc('users_count')
            ->take(4)
            ->get();

        // 3. Report Bay (Latest incoming user reports stream)
        $reportBay = UserReport::with(['reporter', 'reportedUser'])
            ->latest()
            ->take(8)
            ->get();

        // 4. Most Reported Users (Ranked by number of reports received)
        $mostReportedUsers = User::withCount('reportsAgainst')
            ->having('reports_against_count', '>', 0)
            ->orderByDesc('reports_against_count')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'activeUserCount',
            'lobbyCreatedCount',
            'categoryStats',
            'reportBay',
            'mostReportedUsers'
        ));
    }

    public function destroyReport(UserReport $report)
    {
        $report->delete();
        return back()->with('success', 'Report removed.');
    }

    public function destroyUser(User $user)
    {
        // Prevent the currently logged-in admin from deleting themselves
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $userName = $user->name;

        // Remove user record from database
        $user->delete();

        return back()->with('success', "User '{$userName}' and all associated reports have been deleted.");
    }
}