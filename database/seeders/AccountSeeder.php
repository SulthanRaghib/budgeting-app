<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (User::cursor() as $user) {
            if ($user->accounts()->count() === 0) {
                $user->accounts()->createMany([
                    [
                        'name' => 'Cash',
                        'type' => 'Cash',
                        'initial_balance' => 0,
                        'current_balance' => 0,
                    ],
                    [
                        'name' => 'BCA',
                        'type' => 'Bank',
                        'initial_balance' => 0,
                        'current_balance' => 0,
                    ],
                ]);
            }
        }
    }
}
