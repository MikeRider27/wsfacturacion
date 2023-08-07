<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'addDefaultValues.php';
require '../core/params.php';
require 'jsonDeMainservice.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener el JSON enviado a través del POST
    $json_data = file_get_contents('php://input');

    // traer el array $params de src/core/params.php
    global $params;
    
    // Convertir el JSON a un arreglo asociativo
    $data = json_decode($json_data, true);
    
    // instanciar la clase jsonDeMainService
    $JSonDeMainService = new JSonDeMainService();

    // Llama a la función generateXMLDeService y pasa el array $data por referencia
    $JSonDeMainService->generateXMLDeService($params, $data);

   
}
?>
