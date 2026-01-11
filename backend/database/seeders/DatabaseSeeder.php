<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'Nabenns'], // Using username as email or just creating a user
            [
                'name' => 'Nabenns',
                'email' => 'admin@bensserver.cloud', // Dummy email
                'password' => bcrypt('*Nabhan2007'),
            ]
        );
    }
}
