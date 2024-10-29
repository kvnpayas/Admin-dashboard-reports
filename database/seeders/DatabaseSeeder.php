<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ReportSeeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\UserGroupSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(UserGroupSeeder::class);
        $this->call(ReportSeeder::class);
        User::factory()->create([
            'group_id' => 1,
            'name' => 'superadmin',
            'username' => 'superadmin',
            // 'username' => 'superadmin91224',
            'email' => 'kjspayas@teiph.com',
            'password' => Hash::make('password12'),
            'status' => 1,
        ]);
    }
}
