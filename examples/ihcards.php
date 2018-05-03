<?php

# Подключение класса IhCardsImage
# require_once '/path/to/autoload.php'; # Если в вашем проекте есть автозагрузчик.
require_once '/path/to/IhCardsImage.php'; # Если автозагрзчика нет.

$action = isset($_GET['act']) ? $_GET['act'] : false;

$access_token = 'your_access_token';

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