<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Főadmin (Te)',
            'email' => 'admin@getingo.hu',
            'password' => Hash::make('jelszo123'),
            'role' => 'admin',
            
        ]);

        User::create([
        'name' => 'Profi Ingatlaniroda',
            'email' => 'agent@getingo.hu',
            'password' => Hash::make('jelszo123'),
            'role' => 'agent',
        
        ]);
    }
}
