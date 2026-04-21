<?php
use App\Core\Router;
$router->get('/', function() {
    echo json_encode(["message" => "Welcome to ShopLight API"]);
});

?>