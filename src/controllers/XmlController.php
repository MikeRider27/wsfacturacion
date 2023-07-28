<?php
require_once __DIR__ . '/../services/XmlConverter.php';


class XmlController {
    public function handleRequest() {
        // Verificar si la solicitud es POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Versión del esquema XML
            $version = '150';
            // Obtener el JSON enviado a través del POST
            $json_data = file_get_contents('php://input');
            
            // Convertir el JSON a un arreglo asociativo
            $data = json_decode($json_data, true);

            // Convertir el arreglo asociativo a XML usando el servicio XmlConverter
            $xml_string = XmlConverter::convertArrayToXml($data, $version);

            // Imprimir el resultado (puedes enviarlo a través de una respuesta HTTP si lo prefieres)
            echo $xml_string;
        }
    }
}


