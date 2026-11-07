<?php

namespace Tests\Feature;

use App\Models\Lobby;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response
            ->assertOk()
            ->assertDontSee('Verification status')
            ->assertDontSee('cv_input_file');

        $this->assertFalse(Route::has('profile.cv.update'));
        $this->assertFalse(Route::has('lobby.member.cv'));
    }

    public function test_user_can_apply_to_join_a_lobby_without_a_cv(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $lobby = Lobby::create([
            'owner_id' => $owner->id,
            'name' => 'Test Lobby',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('lobby.join', $lobby));

        $response->assertRedirect();
        $this->assertDatabaseHas('lobby_members', [
            'lobby_id' => $lobby->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_update_their_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile/avatar', [
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]);

        $response->assertSessionHasNoErrors();
        Storage::disk('public')->assertExists($user->fresh()->profile->avatar_path);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
