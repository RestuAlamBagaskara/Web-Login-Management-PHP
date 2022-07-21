<?php

    require_once __DIR__ . "/../vendor/autoload.php";

    use Alambagaskara\LoginManagement\App\Router;
    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Controller\HomeController;
    use Alambagaskara\LoginManagement\Controller\UserController;

    Database::getConnection('prod');

    Router::add('GET', '/', HomeController::class, 'index', []);
    Router::add('GET', '/users/register', UserController::class, 'register', []);
    Router::add('POST', '/users/register', UserController::class, 'postRegister', []);
    Router::run();