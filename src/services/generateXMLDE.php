<?php

class generateXMLDE
{

    public function generateRte(&$params)
    {
        // Crear el objeto XML con la estructura deseada
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><rDE></rDE>');

        // Agregar los atributos al elemento raíz
        $xml_data->addAttribute('xmlns', 'http://ekuatia.set.gov.py/sifen/xsd');
        $xml_data->addAttribute('xsi:schemaLocation', 'https://ekuatia.set.gov.py/sifen/xsd/siRecepDE_v150.xsd');
        $xml_data->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        // Agregar el elemento dVerFor
        $xml_data->addChild('dVerFor', $params['version']);
    }

    /*

    private function generateDe($params, $data, $xml_data) {
        if (strpos($params['ruc'], '-') === false) {
            //throw new Error('RUC debe contener dígito verificador en params.ruc');
        }
        
        $rucEmisor = explode('-', $params['ruc']);
        $dvEmisor = explode('-', $params['ruc'])[1];
    
       // $id = $this->codigoControl;
    
        $fechaFirmaDigital = new DateTime($params['fechaFirmaDigital']);
    
        $digitoVerificadorString = (string)$this->codigoControl;
    
        $jsonResult = array(
            '$' => array(
                'Id' => $id,
            ),
            'dDVId' => substr($digitoVerificadorString, -1),
            'dFecFirma' => date_format(new DateTime(), 'Y-m-d\TH:i:s'),
            'dSisFact' => 1,
        );
    
        return $jsonResult;
    }
    
*/
}
