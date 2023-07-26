<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el JSON enviado a través del POST
    $json_data = file_get_contents('php://input');

    // Convertir el JSON a un arreglo asociativo
    $data = json_decode($json_data, true);

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
    $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

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

    // Establecer los encabezados para indicar que la respuesta es XML
    header('Content-type: text/xml');
    header('Content-Disposition: attachment; filename="resultado.xml"');

    // Imprimir el resultado (puedes enviarlo a través de una respuesta HTTP si lo prefieres)
    echo $xml_string;
}
?>
