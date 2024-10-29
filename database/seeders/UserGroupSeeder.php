<?php

namespace Database\Seeders;

use App\Models\UserGroup;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserGroup::create([
          'name' => 'Admin',
          'description' => 'Dashboard admin support'
        ]);

        UserGroup::create([
          'name' => 'ICSS',
          'description' => 'ICSS Reports'
        ]);
    }
}
