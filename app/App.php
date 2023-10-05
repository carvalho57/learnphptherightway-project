<?php

declare(strict_types = 1);


function readCsv(string $path) : array {

    $data = [];

    if(!file_exists($path) && !is_readable($path)) {
        throw new Exception("Não é possível ler o arquivo");
    }    

    $file = fopen($path, 'r');

    if ($file === false) {
        throw new Exception("Não foi possível abrir o arquivo");
    }

    $header = fgetcsv($file);

    while(($line = fgetcsv($file)) !== false) {
        
        if (!empty($line)) {         
            $data[] = array_combine($header, $line);            
        }                
    }

    return $data;
}

function readTransactions($directory) {

    if (!is_dir($directory)) {
        throw new Exception("O diretório não existe");
    }

    $transactions = [];

    foreach(scandir($directory) as $file) {
        if($file === '.' || $file === '..') continue;

        $csvData = readCsv("{$directory}/{$file}");
        
        if (empty($csvData)) continue;        
        $transactions = array_merge($transactions, $csvData);
    }
    
    return $transactions;
}

function processTransactions($path) : array {
    $transactions = readTransactions($path);

    $totalIncome = 0;
    $totalExpense = 0;

    $processedTransaction = array_map(function($transaction) use (&$totalIncome, &$totalExpense) {

        $date = date_create_from_format('m/d/Y',$transaction['Date']);
        $transaction['Date'] = date_format($date, 'M j,Y');

        $amount = (float)(str_replace(['$',','],[''],$transaction['Amount']));

        if ($amount > 0) {
            $totalIncome += $amount;
        } else {
            $totalExpense -= $amount;
        }

        return $transaction;

    }, $transactions);

    return [
        'totalIncome' => round($totalIncome,2),
        'totalExpense' => round($totalExpense,2),
        'netTotal' => round($totalIncome - $totalExpense,2),
        'transactions' => $processedTransaction
    ];
}

$processed = processTransactions(FILES_PATH);

extract($processed);

include VIEWS_PATH . 'transactions.php';