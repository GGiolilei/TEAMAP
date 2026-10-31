<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Interest;
use App\Models\Lobby;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(6)->create();

        foreach ($users as $user) {
            Profile::create([
                'user_id' => $user->id,
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name),
                'bio' => 'Saya tertarik pada proyek kolaboratif dan suka membangun solusi yang berdampak nyata.',
                'skills' => 'Laravel, Vue, UI/UX, Product Thinking',
            ]);

            $user->interests()->sync(
                Interest::inRandomOrder()->limit(rand(2, 4))->pluck('id')->toArray()
            );
        }

        $lobbyData = [
            [
                'owner_id' => $users[0]->id,
                'name' => 'TEAMAP Platform',
                'description' => 'Platform kolaborasi untuk mengelola project tim dan task secara terstruktur.',
                'project_goal' => 'Membuat dashboard kerja tim yang mudah dipahami.',
                'required_roles' => 'Frontend, Backend, Designer',
                'max_members' => 8,
                'status' => 'active',
            ],
            [
                'owner_id' => $users[1]->id,
                'name' => 'AI Research Hub',
                'description' => 'Eksperimen dan riset produk berbasis AI untuk kebutuhan user.',
                'project_goal' => 'Membangun prototipe AI yang berguna untuk pengguna.',
                'required_roles' => 'AI Engineer, Analyst, Designer',
                'max_members' => 10,
                'status' => 'active',
            ],
            [
                'owner_id' => $users[2]->id,
                'name' => 'Startup Sprint',
                'description' => 'Tim sprint untuk membangun MVP dengan cepat dan fokus pada value.',
                'project_goal' => 'Meluncurkan MVP dalam 2 bulan.',
                'required_roles' => 'Product, Engineering, Marketing',
                'max_members' => 6,
                'status' => 'active',
            ],
        ];

        foreach ($lobbyData as $data) {
            $lobby = Lobby::create($data);
            $lobby->interests()->sync(
                Interest::inRandomOrder()->limit(rand(2, 4))->pluck('id')->toArray()
            );

            $participants = $users->shuffle()->take(rand(3, 5));
            foreach ($participants as $participant) {
                if ($participant->id === $lobby->owner_id) {
                    continue;
                }

                $lobby->members()->attach($participant->id, [
                    'role_in_team' => fake()->randomElement(['Developer', 'Designer', 'Product', 'Marketing', 'QA']),
                    'status' => 'accepted',
                ]);
            }

            $channels = [
                ['name' => 'general', 'description' => 'Percakapan umum tim'],
                ['name' => 'backend', 'description' => 'Diskusi implementasi backend'],
                ['name' => 'frontend', 'description' => 'Diskusi antarmuka dan UX'],
                ['name' => 'roadmap', 'description' => 'Rencana kerja dan milestone'],
            ];

            foreach ($channels as $channelData) {
                $channel = Channel::firstOrCreate([
                    'lobby_id' => $lobby->id,
                    'name' => $channelData['name'],
                ], [
                    'description' => $channelData['description'],
                ]);

                $senderList = $lobby->members()->pluck('users.id')->toArray();
                $senderList[] = $lobby->owner_id;

                foreach (range(1, rand(3, 6)) as $index) {
                    $senderId = fake()->randomElement($senderList);

                    Message::create([
                        'channel_id' => $channel->id,
                        'user_id' => $senderId,
                        'content' => fake()->sentence(rand(8, 16)),
                    ]);
                }
            }

            foreach (range(1, 3) as $taskIndex) {
                $member = $lobby->members()->inRandomOrder()->first() ?? $lobby->owner;

                Task::create([
                    'user_id' => $member->id,
                    'lobby_id' => $lobby->id,
                    'title' => fake()->sentence(4),
                    'status' => fake()->randomElement(['todo', 'progress', 'done']),
                    'date' => now()->addDays(rand(1, 10))->toDateString(),
                ]);
            }
        }
    }
}
