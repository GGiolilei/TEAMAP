<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelRead;
use App\Models\LobbyMember;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get array counts for pending lobby requests and unread channel messages.
     */
    public function counts(Request $request): array
    {
        $user = $request->user();

        // 1. Pending Lobbies Count
        $pending = DB::table('lobby_user')
            ->whereIn('lobby_id', $user->hostedLobbies()->select('lobbies.id'))
            ->where('status', 'pending')
            ->count();

        // 2. Fetch Channel IDs where the user is a member/owner
        $channelIds = Channel::whereHas('lobby.members', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->pluck('id');

        if ($channelIds->isEmpty()) {
            return ['pending' => (int)$pending, 'unread' => 0];
        }

        // 3. Unread Messages Count
        $unread = Message::whereIn('channel_id', $channelIds)
            ->where('user_id', '!=', $user->id)
            ->where(function ($query) use ($user) {
                $query->whereNotExists(function ($sub) use ($user) {
                    $sub->select(DB::raw(1))
                        ->from('channel_reads')
                        ->whereColumn('channel_reads.channel_id', 'messages.channel_id')
                        ->where('channel_reads.user_id', $user->id)
                        ->whereColumn('channel_reads.last_read_at', '>=', 'messages.created_at');
                });
            })
            ->count();

        return ['pending' => (int)$pending, 'unread' => (int)$unread];
    }

    /**
     * Endpoint polled by Javascript to detect real-time message toasts and pending counts.
     */
    public function stream(Request $request)
    {
        $user = $request->user() ?? auth()->user();
        if (!$user) {
            return response()->json(['pending' => 0, 'latest_message_id' => 0], 401);
        }

        $userId = $user->id;

        // 1. Pending lobby join requests
        $pending = DB::table('lobby_user')
            ->whereIn('lobby_id', $user->hostedLobbies()->select('lobbies.id'))
            ->where('status', 'pending')
            ->count();

        // 2. Fetch all Channels associated with user's accessible lobbies
        $channelIds = Channel::whereHas('lobby', function ($q) use ($userId) {
            $q->where('owner_id', $userId)
              ->orWhereHas('members', function ($mq) use ($userId) {
                  $mq->where('users.id', $userId)
                     ->where('lobby_user.status', 'accepted');
              });
        })->pluck('id');

        // 3. Get the absolute latest message sent by OTHER users in those channels
        $latestMessage = null;
        if ($channelIds->isNotEmpty()) {
            $latestMessage = Message::whereIn('channel_id', $channelIds)
                ->where('user_id', '!=', $userId)
                ->with('user')
                ->latest('id')
                ->first();
        }

        return response()->json([
            'pending'                => (int) $pending,
            'latest_message_id'      => $latestMessage ? $latestMessage->id : 0,
            'latest_message_user'    => $latestMessage && $latestMessage->user ? $latestMessage->user->name : 'Someone',
            'latest_message_content' => $latestMessage ? $latestMessage->content : '',
        ]);
    }

    /**
     * Mark a channel as read for the current user.
     */
    public function markRead(Request $request, Channel $channel)
    {
        ChannelRead::updateOrCreate(
            ['user_id' => $request->user()->id, 'channel_id' => $channel->id],
            ['last_read_at' => now()]
        );

        return response()->noContent();
    }
}