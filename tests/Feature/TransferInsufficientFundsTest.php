<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Account;
use App\Models\Transfer;

class TransferInsufficientFundsTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_is_rejected_when_insufficient_funds()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $from = Account::create(['user_id' => $user->id, 'name' => 'From', 'type' => 'Cash', 'initial_balance' => 100000, 'current_balance' => 100000]);
        $to = Account::create(['user_id' => $user->id, 'name' => 'To', 'type' => 'Bank', 'initial_balance' => 0, 'current_balance' => 0]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        Transfer::create([
            'user_id' => $user->id,
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
            'amount' => 500000, // > available
            'date' => now(),
        ]);

        // ensure balances unchanged
        $this->assertEquals(100000, (float) $from->fresh()->current_balance);
        $this->assertEquals(0, (float) $to->fresh()->current_balance);
    }
}
