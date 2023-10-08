<?php

declare(strict_types = 1);

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

define('APP_PATH', $root . 'app' . DIRECTORY_SEPARATOR);
define('FILES_PATH', $root . 'transaction_files' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', $root . 'views' . DIRECTORY_SEPARATOR);

require_once APP_PATH . 'App.php';
require_once APP_PATH . 'Helpers.php';


$transactions = getTransactions(FILES_PATH, function($transaction) {

    [$date, $check,$description, $amount] = $transaction;
    
    $amount = str_replace(['$',','], [''], $amount);

    return [
        'date' => $date,
        'check' => $check,
        'description' =>$description,
        'amount' => $amount
    ];
});


$totals = getTotals($transactions);

extract($totals);


include VIEWS_PATH . 'transactions.php';