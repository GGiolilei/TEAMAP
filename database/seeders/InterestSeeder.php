<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interests = [
            'UI/UX',
            'Frontend',
            'Backend',
            'Product',
            'Mobile',
            'AI',
            'Marketing',
            'Data Science',
        ];

        foreach ($interests as $interest) {
            Interest::firstOrCreate([
                'name' => $interest,
            ]);
        }
    }
}
