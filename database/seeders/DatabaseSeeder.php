<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(RoleSeeder::class);
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@eduLite.net.ng',
        ]);

        foreach(Role::all() as $role){
            $role->users()->syncWithoutDetaching($user->id);
        }
        // $user->roles()->attach;
    }
}
