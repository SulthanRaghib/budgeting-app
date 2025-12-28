<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Transfer;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_moves_money_between_accounts()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $from = Account::create(['user_id' => $user->id, 'name' => 'From', 'type' => 'Cash', 'initial_balance' => 100000, 'current_balance' => 100000]);
        $to = Account::create(['user_id' => $user->id, 'name' => 'To', 'type' => 'Bank', 'initial_balance' => 0, 'current_balance' => 0]);

        $transfer = Transfer::create([
            'user_id' => $user->id,
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
            'amount' => 25000,
            'date' => now(),
        ]);

        $this->assertEquals(75000, (float) $from->fresh()->current_balance);
        $this->assertEquals(25000, (float) $to->fresh()->current_balance);

        // Deleting transfer should revert balances
        $transfer->delete();

        $this->assertEquals(100000, (float) $from->fresh()->current_balance);
        $this->assertEquals(0, (float) $to->fresh()->current_balance);
    }
}
