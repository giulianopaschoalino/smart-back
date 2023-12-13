<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // User::query()->create([
        //     'client_id'      => null,
        //     'name'           => 'Admin',
        //     'email'          => 'admin@admin.com',
        //     'password'       => 'password',
        //     'remember_token' => null,
        // ]);

        User::query()->create([
            'client_id'      => null,
            'name'           => 'Admin',
            'email'          => 'admin2@admin.com',
            'password'       => 'password',
            'remember_token' => null,
        ]);

        User::query()->create([
            'client_id'      => 180103211,
            'name'           => 'Client',
            'email'          => 'client@client.com',
            'password'       => 'password',
            'remember_token' => null,
        ]);
    }
}
