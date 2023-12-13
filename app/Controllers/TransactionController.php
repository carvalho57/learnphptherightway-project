<?php

declare(strict_types=1);

namespace App\Controllers;

use App\File;
use App\Models\CalculateTransactionTotals;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\View;

class TransactionController
{
    private TransactionService $transactionService;
    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }
    public function index(): View
    {
        $transactions = (new Transaction())->all();
        $calculateTransactionTotals = new CalculateTransactionTotals($transactions);
        $calculateTransactionTotals->calculate();

        return View::make('transactions.index', ['processedTransaction' => $calculateTransactionTotals]);
    }

    public function prepareUpload(): View
    {
        return View::make('transactions.upload');
    }

    public function upload()
    {
        $files = File::normalize('files');

        foreach ($files as $file) {
            if (!file_exists($file['path']) || !is_readable($file['path'])) {
                continue;
            }

            if (File::isCSV($file['path'])) {
                $this->transactionService->processUploadFile($file['path']);
            }
        }

        header('Location: /transactions');
    }
}
