<?php

namespace Tests\Feature;

use App\Models\Lobby;
use App\Models\LobbyMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LobbyJoinStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_join_request_shows_pending_status_and_disables_join_button(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $lobby = Lobby::create([
            'owner_id' => $owner->id,
            'name' => 'Test Lobby',
        ]);

        $this->actingAs($user)
            ->post(route('lobby.join', $lobby))
            ->assertRedirect();

        $response = $this->get(route('lobbies.index'));

        $response
            ->assertOk()
            ->assertSeeText('Pending')
            ->assertSee('disabled', false)
            ->assertDontSee('Request to Join');
    }

    public function test_accepted_member_sees_joined_status_and_can_open_chat(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $lobby = Lobby::create([
            'owner_id' => $owner->id,
            'name' => 'Test Lobby',
        ]);
        LobbyMember::create([
            'lobby_id' => $lobby->id,
            'user_id' => $user->id,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($user)->get(route('lobbies.index'));

        $response
            ->assertOk()
            ->assertSeeText('Joined')
            ->assertSeeText('Open Chat')
            ->assertDontSee('disabled', false);
    }
}
