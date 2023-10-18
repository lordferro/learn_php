<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TransactionsModel;
use App\View;

class HomeController
{
    protected $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

    public function index(): View
    {
        return View::make('index');
    }

    public function upload(): View
    {

        if ($_FILES['transactions']['name'][0] == '') {

            return View::make('index');
        } else {

            $total = count(($_FILES['transactions']['name']));

            $transactions = [];

            for($i = 0; $i < $total; $i++) {
                $file = fopen($_FILES['transactions']['tmp_name'][$i], 'r');
                fgetcsv($file);
                while (($transaction = fgetcsv($file)) !== false) {
                    $transaction = $this->transactionHandler($transaction);

                    $transactions[] = $transaction;
                };
            }
            (new TransactionsModel())->create($transactions);

            return View::make('uploadSuccess');

        }
    }

    public function transactions(): View
    {
        $transactions = (new TransactionsModel())->getAll();
        $this->calcTotal($transactions);

        return View::make('transactions', ['transactions' => $transactions, 'totals'=>$this->totals]);
    }

    public function transactionHandler(array $transaction): array
    {
        [$date, $check, $description, $amount] = $transaction;
        $amount = (float)str_replace(['$', ','], '', $amount);

        return [ 'date' => $date,
        'check' => $check,
        'description' => $description,
        'amount' => $amount];
    }

    public function getDateFormat(string $date)
    {
        return date('M j, Y', strtotime($date));

    }


    public function formatDollarAmount(float $amount): string
    {
        $isNegative = $amount < 0;

        return ($isNegative ? '-' : '') . '$' . number_format(abs($amount), 2);
    }

    public function calcTotal(array $transactions)
    {

        foreach ($transactions as $transaction) {

            $this->totals['netTotal'] += $transaction['amount'];
            if ($transaction['amount'] >= 0) {
                $this->totals['totalIncome'] += $transaction['amount'];
            } else {
                $this->totals['totalExpense'] += $transaction['amount'];
            }
        }

    }


}