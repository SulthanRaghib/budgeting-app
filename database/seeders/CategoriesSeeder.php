<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'user_id' => 1,
                'name' => 'Gaji',
                'type' => 'income',
                'color' => '#34D399',
                'icon' => 'heroicon-o-cash',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Freelance',
                'type' => 'income',
                'color' => '#60A5FA',
                'icon' => 'heroicon-o-briefcase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Makan',
                'type' => 'expense',
                'color' => '#FBBF24',
                'icon' => 'heroicon-o-utensils',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Transport',
                'type' => 'expense',
                'color' => '#F87171',
                'icon' => 'heroicon-o-car',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Kos',
                'type' => 'expense',
                'color' => '#A78BFA',
                'icon' => 'heroicon-o-home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Internet',
                'type' => 'expense',
                'color' => '#34D399',
                'icon' => 'heroicon-o-globe-alt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Belanja Harian',
                'type' => 'expense',
                'color' => '#FBBF24',
                'icon' => 'heroicon-o-shopping-cart',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Darurat',
                'type' => 'expense',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-shield-check',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
