<?php

define('APP_NAME', 'Pharmacy Sales Management System');

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = preg_replace('#/(admin|pharmacist)(/.*)?$#', '', $scriptDir);
define('BASE_URL', rtrim($baseUrl, '/') ?: '');

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function formatCurrency(float $amount): string
{
    return number_format($amount, 2) . ' TZS';
}

function formatDate(?string $date): string
{
    if (!$date) {
        return '-';
    }

    return date('d M Y', strtotime($date));
}
