<?php

declare(strict_types = 1);

// Your Code

function getTransactionFiles(string $path): array
{
    $files = [];
    foreach (scandir($path) as $file){
        if(is_dir($file))
            continue;
        $files[] = $path . $file;
    }

    return $files;
}

function getTransactions(string $fileName, ?callable $transactionHandler = null): array
{
    if (! file_exists($fileName)){
        trigger_error('file not found', E_USER_ERROR);
    }

    $file = fopen($fileName, 'r');

    $transactions = [];

    fgetcsv($file);
    while (($transaction = fgetcsv($file)) !== false){
        if($transactionHandler !== null) {
            $transaction = $transactionHandler($transaction);
        }

        $transactions[] = $transaction;
    }
    return $transactions;
}

function extractTransaction($transactionRow): array
{
    [$date, $checkNumber, $description, $amount] = $transactionRow;

    $amount = (float)str_replace(['$', ','], '', $amount);
    return [
        'date' => $date,
        'checkNumber' => $checkNumber,
        'description' => $description,
        'amount' => $amount
    ];
}

function calculateTotals(array $transactions): array
{
    $totals = [
        'netTotal' => 0,
        'totalIncome' => 0,
        'totalExpanse' => 0
    ];

    foreach ($transactions as $transaction){
        $totals['netTotal'] += $transaction['amount'];

        if(($amount = $transaction['amount']) >= 0){
            $totals['totalIncome'] += $amount;
        }
        else{
            $totals['totalExpanse'] += $amount;
        }
    }
    return $totals;
}