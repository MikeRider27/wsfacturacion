<?php
//require 'ConstanteService.php';
require 'addDefaultValues.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Versión del esquema XML
    $version = '150';
    // Obtener el JSON enviado a través del POST
    $json_data = file_get_contents('php://input');

    // Convertir el JSON a un arreglo asociativo
    $data = json_decode($json_data, true);

    // Crea una instancia de la clase addDefaultValues
    $addDefaultValues = new addDefaultValues();
 

    // Llama a la función DefaultValues y pasa el array $data por referencia
    $addDefaultValues->DefaultValues($data);
   
    

    // Función para convertir un arreglo asociativo a XML
    function array_to_xml($data, &$xml_data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item';
                }
                $subnode = $xml_data->addChild($key);
                array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild($key, htmlspecialchars($value));
            }
        }
    }

    // Crear el objeto XML
    $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><rDE></rDE>');
        
    // Agregar los atributos al elemento raíz
    $xml_data->addAttribute('xmlns', 'http://ekuatia.set.gov.py/sifen/xsd');
    $xml_data->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $xml_data->addAttribute('xsi:schemaLocation', 'https://ekuatia.set.gov.py/sifen/xsd/siRecepDE_v150.xsd');
    // Agregar el elemento dVerFor
    $xml_data->addChild('dVerFor', $version);
    
    // Convertir el arreglo asociativo a XML
    foreach ($data as $item) {
        array_to_xml($item, $xml_data);
    }

    // Formatear el XML para que sea más legible
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml_data->asXML());

    // Obtener el XML como una cadena
    $xml_string = $dom->saveXML();
    
    // Imprimir el resultado (puedes enviarlo a través de una respuesta HTTP si lo prefieres)
    echo $xml_string;
}
?>
