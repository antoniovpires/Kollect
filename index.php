<?php

declare(strict_types=1);


// use composer autoloader
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Authorization");
header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ($parts[2] != "products"){
    http_response_code(404);
    exit;
}

$id = $_POST['id'] ?? $parts[3] ?? null;

$database = new Database("localhost", "product_db", "root", "");

$gateway = new ProductGateway($database);

$controller = new ProductController($gateway);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
    $controller->processRequest($_POST['_method'], $id);
} else {
    $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
}


