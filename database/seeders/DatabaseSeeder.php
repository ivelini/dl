<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Group::factory()
            ->count(5)
            ->sequence(function ($sequence) {
                return [
                    'name' => 'Группа ' . $sequence->index + 1,
                ];
            })
            ->create();

        User::factory()
            ->count(10)
            ->create();
    }
}
