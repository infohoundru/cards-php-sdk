<?php

require_once __DIR__ . '/vendor/autoload.php';

$action = isset($_GET['act']) ? $_GET['act'] : false;

$access_token = '';

try {
    if (false === $action) {
        throw new \Exception('Required parameter "act" not found.');
    }

    echo \IhCardsImage::stream($action, $access_token, 'file');

} catch (\Exception $e) {
    header("HTTP/1.0 404 Not Found");
    print_r($e->getMessage());
    die;
}