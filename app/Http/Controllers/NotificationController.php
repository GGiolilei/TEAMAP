<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelRead;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationController extends Controller
{
    public function counts(Request $request): array
    {
        $user = $request->user();

        $pending = $user->hostedLobbies()
            ->with('members')
            ->get()
            ->sum(fn ($lobby) => $lobby->members()->wherePivot('status', 'pending')->count());

        $channelIds = Channel::whereHas('lobby.members', fn ($q) => $q->where('users.id', $user->id))
            ->pluck('id');

        $reads = ChannelRead::where('user_id', $user->id)
            ->whereIn('channel_id', $channelIds)
            ->pluck('last_read_at', 'channel_id');

        $unread = 0;
        foreach ($channelIds as $channelId) {
            $lastRead = $reads[$channelId] ?? null;
            $unread += Message::where('channel_id', $channelId)
                ->when($lastRead, fn ($q) => $q->where('created_at', '>', $lastRead))
                ->where('user_id', '!=', $user->id)
                ->count();
        }

        return ['pending' => $pending, 'unread' => $unread];
    }

    public function stream(Request $request): StreamedResponse
    {
        $user = $request->user();

        return response()->stream(function () use ($user, $request) {
            $lastPayload = null;
            $start = time();

            while (time() - $start < 55) {
                if (connection_aborted()) break;

                $payload = $this->counts($request);

                if ($payload !== $lastPayload) {
                    echo "event: update\n";
                    echo 'data: ' . json_encode($payload) . "\n\n";
                    ob_flush();
                    flush();
                    $lastPayload = $payload;
                }

                sleep(3);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function markRead(Request $request, Channel $channel)
    {
        ChannelRead::updateOrCreate(
            ['user_id' => $request->user()->id, 'channel_id' => $channel->id],
            ['last_read_at' => now()]
        );

        return response()->noContent();
    }
}