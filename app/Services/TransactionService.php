<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionCollection;

class TransactionService
{
    public function processUploadFile(string $path): void
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new \Exception('O arquivo não existe ou não está disponível');
        }

        if (($handle = fopen($path, 'r')) !== false) {
            $transactions = [];

            //Remove header
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {
                $transactions[] = Transaction::create(
                    \DateTime::createFromFormat('d/m/Y', $data[0]),
                    (string)$data[1],
                    (string)$data[2],
                    (float) str_replace(['$', ','], '', $data[3])
                );
            }

            $transactionsCollection = new TransactionCollection($transactions);
            (new Transaction())->saveAll($transactionsCollection);
        }
    }
}
