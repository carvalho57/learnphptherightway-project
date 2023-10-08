<?php


function formatMoney(float $amount) : string {
    $value = abs($amount);

    $value = number_format($value, 2);

    return $amount > 0 ? "\${$value}" : "-\${$value}";
}


function formatDate(string $date) : string {
    $date = strtotime($date);
    return date('M j,Y', $date);
}