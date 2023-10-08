<?php

declare(strict_types = 1);


function readCsv(string $path) : array {

    $lines = [];

    if (!file_exists($path) && !is_readable($path)) {
        throw new Exception("Não é possível ler o arquivo");
    }    

    $file = fopen($path, 'r');

    if ($file === false) {
        throw new Exception("Não foi possível abrir o arquivo");
    }

    fgetcsv($file);

    while (($line = fgetcsv($file)) !== false) {

        if (empty($line)) continue;
        $lines[] = $line;
    }

    return $lines;
}

function readCsvDiretory(string $directory) : array {

    if (!is_dir($directory)) {
        throw new Exception("O diretório não existe");
    }

    $lines = [];

    foreach(scandir($directory) as $file) {
        if (is_dir($file)) continue;

        $path = $directory . $file;

        if (pathinfo($path)['extension'] !== 'csv') continue;

        $csvData = readCsv($path);
        
        if (empty($csvData)) continue;        

        $lines = array_merge($lines, $csvData);
    }
    
    return $lines;
}


/**
 * Processa transações e retorna um array associativo com as informações da transação.
 *
 * @param string $path O caminho para o arquivo de transações.
 * @param callable $callback Função que processa cada transação.
 *
 * @return array[] Um array associativo contendo informações de transação.
 *                 Com as seguintes chaveschaves:
 *                 - date: A data da transação.
 *                 - check: O número do cheque.
 *                 - description: A descrição da transação.
 *                 - amount: O valor da transação.
 */
function getTransactions(string $path, callable $handlerTransactions = null) : array {        

    $transactions = readCsvDiretory($path);
        
    if($handlerTransactions === null) {
        return $transactions;
    }

    $processedTransaction = array_map($handlerTransactions,$transactions);

    return $processedTransaction;
}

function getTotals(array $transactions) : array {
   
    $totals = [
        'totalIncome' => 0,
        'totalExpense' => 0,
        'netTotal' => 0      
    ];

    foreach($transactions as $transaction) {

        if ($transaction['amount'] > 0) {
            $totals['totalIncome'] += $transaction['amount'];
        } else  {
            $totals['totalExpense'] += $transaction['amount'];
        }
    }

    $totals['netTotal'] = $totals['totalIncome'] + $totals['totalExpense'];

    return $totals;  
}

