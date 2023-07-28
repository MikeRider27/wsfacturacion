<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener la ruta solicitada sin los parámetros de consulta
$request_uri = $_SERVER['REQUEST_URI'];
$request_path = parse_url($request_uri, PHP_URL_PATH);

// Rutas disponibles y sus controladores asociados
$routes = [
    //'/' => 'IndexController@index', 
    '/' => 'XmlController@handleRequest',
    // Agrega aquí más rutas si es necesario
];

// Buscar la ruta solicitada en la lista de rutas disponibles
$matched = false;
foreach ($routes as $route => $handler) {
    if ($request_path === $route) {
        list($controller, $method) = explode('@', $handler);
        require_once 'controllers/' . $controller . '.php';
        $controllerInstance = new $controller();
        $controllerInstance->{$method}();
        $matched = true;
        break;
    }
}

// Si no se encuentra una ruta coincidente, muestra un mensaje de error o redirecciona a una página 404
if (!$matched) {
    header("HTTP/1.0 404 Not Found");
    echo "Error 404: Página no encontrada";
    // También puedes redireccionar a una página de error personalizada.
}
