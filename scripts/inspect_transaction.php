<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Transaction;

$t = Transaction::latest()->first();
if (! $t) {
    echo "No transactions\n";
    exit;
}

$account = $t->account;
$categoryType = $t->category ? $t->category->type : 'no category';
$accountBalance = $account ? $account->current_balance : 'no account';

print_r([
    'id' => $t->id,
    'amount' => (float) $t->amount,
    'account_id' => $t->account_id,
    'category_type' => $categoryType,
    'account_balance' => $accountBalance,
]);
