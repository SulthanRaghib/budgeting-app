<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add nullable account_id first so existing rows are not broken.
        if (! Schema::hasColumn('transactions', 'account_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreignId('account_id')->nullable()->constrained()->cascadeOnDelete();
            });
        } else {
            // If column exists, ensure foreign key exists; attempt to add FK if missing
            try {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
                });
            } catch (\Exception $e) {
                // ignore if FK already exists or cannot be created
            }
        }

        // Create default accounts for existing users if none exist, and assign
        foreach (User::cursor() as $user) {
            if ($user->accounts()->count() === 0) {
                $user->accounts()->create([
                    'name' => 'Cash',
                    'type' => 'Cash',
                    'initial_balance' => 0,
                    'current_balance' => 0,
                ]);
            }

            // Assign transactions without an account to the user's first account
            $first = $user->accounts()->first();
            if ($first) {
                DB::table('transactions')
                    ->where('user_id', $user->id)
                    ->whereNull('account_id')
                    ->update(['account_id' => $first->id]);
            }
        }

        // Recalculate account current_balance from assigned transactions
        foreach (\App\Models\Account::cursor() as $account) {
            $balance = (float) $account->initial_balance;
            $txs = DB::table('transactions')
                ->where('account_id', $account->id)
                ->join('categories', 'transactions.category_id', '=', 'categories.id')
                ->select('transactions.amount', 'categories.type')
                ->get();

            foreach ($txs as $tx) {
                if ($tx->type === 'income') {
                    $balance += (float) $tx->amount;
                } else {
                    $balance -= (float) $tx->amount;
                }
            }

            DB::table('accounts')->where('id', $account->id)->update(['current_balance' => $balance]);
        }

        // Make column required. NOTE: this uses the doctrine/dbal change() functionality.
        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreignId('account_id')->nullable(false)->change();
            });
        } catch (\Exception $e) {
            // If change() is not available or fails, skip. The column will remain nullable.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_id');
        });
    }
};
