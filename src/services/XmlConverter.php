<?php

require_once 'ArrayToXmlConverter.php';



class XmlConverter {
    public static function convertArrayToXml($data, $version) {       
        // Crear el objeto XML
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><rDE></rDE>');
        
        // Agregar los atributos al elemento raíz
        $xml_data->addAttribute('xmlns', 'http://ekuatia.set.gov.py/sifen/xsd');
        $xml_data->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml_data->addAttribute('xsi:schemaLocation', 'https://ekuatia.set.gov.py/sifen/xsd/siRecepDE_v150.xsd');
        // Agregar el elemento dVerFor
        $xml_data->addChild('dVerFor', $version);

        // Convertir el arreglo asociativo a XML usando el servicio ArrayToXmlConverter
        foreach ($data as $item) {
            ArrayToXmlConverter::convert($item, $xml_data);
        }

        // Crear el objeto DOMDocument para formatear el XML
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml_data->asXML());

        // Obtener el XML como una cadena
        $xml_string = $dom->saveXML();

        return $xml_string;
    }
}
