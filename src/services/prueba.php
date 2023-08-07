<?php
require 'addDefaultValues.php';
require '../core/params.php';
require 'generateXMLDE.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener el JSON enviado a través del POST
    $json_data = file_get_contents('php://input');

    // traer el array $params de src/core/params.php
    global $params;
    
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
                // Verificar si el valor es un elemento singular o una lista de elementos
                if (is_numeric($key)) {
                    // Si es una lista de elementos, no crear un nuevo elemento para cada uno
                    array_to_xml($value, $xml_data);
                } else {
                    // Si es un elemento singular, crear un nuevo elemento con el nombre $key
                    $subnode = $xml_data->addChild($key);
                    array_to_xml($value, $subnode);
                }
            } else {
                $xml_data->addChild($key, htmlspecialchars($value));
            }
        }
    }

   // $xml = new generateXMLDE();
   // $xml_data = $xml->generateRte($params);

    // Crear el objeto XML con la estructura deseada
    $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="no"?><rDE></rDE>');

    // Agregar los atributos al elemento raíz
    $xml_data->addAttribute('xmlns', 'http://ekuatia.set.gov.py/sifen/xsd');
    $xml_data->addAttribute('xsi:schemaLocation', 'https://ekuatia.set.gov.py/sifen/xsd/siRecepDE_v150.xsd');
    $xml_data->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

    // Agregar el elemento dVerFor
    $xml_data->addChild('dVerFor', $params['version']);

    // Agregar la estructura 'DE' con atributo 'Id'
    $de_element = $xml_data->addChild('DE');
    $de_element->addAttribute('Id', '01');
    

    // Convertir el arreglo asociativo a XML dentro del elemento 'DE'
    array_to_xml($data, $de_element);

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
