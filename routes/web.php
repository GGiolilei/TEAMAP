<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\LobbyMembershipController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\UserReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Login, Registration, etc.)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Protected App Workspace Layer
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Profile Management ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

    // --- Lobbies Hub Management ---
    Route::get('/lobbies', [LobbyController::class, 'index'])->name('lobbies.index');
    Route::get('/lobbies/owned', [LobbyController::class, 'owned'])->name('lobbies.owned');
    Route::get('/lobbies/joined', [LobbyController::class, 'joined'])->name('lobbies.joined');
    Route::get('/lobbies/create', [LobbyController::class, 'create'])->name('lobbies.create');
    Route::post('/lobbies', [LobbyController::class, 'store'])->name('lobbies.store');
    Route::get('/lobbies/{lobby}', [ChatController::class, 'index'])->name('lobbies.show');
    Route::delete('/lobbies/{lobby}', [LobbyController::class, 'destroy'])->name('lobbies.destroy');
    Route::delete('/lobbies/{lobby}/leave', [LobbyController::class, 'leave'])->name('lobbies.leave');

    // --- Legacy Singular Lobby Aliases ---
    Route::get('/lobby', [LobbyController::class, 'index'])->name('lobby.index');
    Route::get('/lobby/create', [LobbyController::class, 'create'])->name('lobby.create');

    // --- Lobby Membership Actions & Moderation ---
    Route::post('/lobby/{lobby}/join', [LobbyMembershipController::class, 'join'])->name('lobby.join');
    Route::post('/lobbies/{lobby}/join', [LobbyController::class, 'join'])->name('lobbies.join');
    Route::patch('/membership/{member}/{status}', [LobbyMembershipController::class, 'updateStatus'])->name('lobby.member.status');
    Route::patch('/membership/{member}/status/{status}', [LobbyMembershipController::class, 'updateStatus'])->name('membership.update');

    // --- Channel Provisioning & Notifications ---
    Route::get('/lobbies/{lobby}/channels/create', [ChannelController::class, 'create'])->name('lobbies.channels.create');
    Route::post('/lobbies/{lobby}/channels', [ChannelController::class, 'store'])->name('lobbies.channels.store');
    Route::get('/notifications/stream', [NotificationController::class, 'stream'])->name('notifications.stream');
    Route::post('/channels/{channel}/read', [NotificationController::class, 'markRead'])->name('channels.read');

    // --- Core Workspace Text Chat & Huddle ---
    Route::get('/chat/{lobby}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/channels/{channel}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/channels/{channel}/stream', [ChatController::class, 'stream'])->name('channels.stream');
    Route::get('/chat/channel/{channel}/huddle', function ($channelId) {
        return view('chat.huddle', ['channelId' => $channelId]);
    })->name('chat.huddle');

    // --- Scoped Lobby Task Management (Uses singular 'task.index') ---
    Route::get('/lobbies/{lobby}/tasks', [TaskController::class, 'index'])->name('task.index');
    Route::post('/lobbies/{lobby}/tasks', [TaskController::class, 'store'])->name('lobbies.tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::post('/tasks/{task}/due-date', [TaskController::class, 'updateDueDate'])->name('tasks.updateDueDate');

    // --- User Reporting Endpoint ---
    Route::post('/users/{user}/report', [UserReportController::class, 'store'])->name('users.report');
});

/*
|--------------------------------------------------------------------------
| Admin Moderation Routes (Strictly Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::post('/admin/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('admin.reports.updateStatus');
    Route::delete('/admin/reports/{report}', [AdminReportController::class, 'destroyReport'])->name('admin.reports.destroy');
    Route::delete('/admin/users/{user}', [AdminReportController::class, 'destroyUser'])->name('admin.users.destroy');
});