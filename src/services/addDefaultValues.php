<?php
require 'ConstanteService.php';

class addDefaultValues {

    public function DefaultValues(&$data) {

        //var_dump($data);
        $tipo = $data[0]['tipoDocumento'];
        var_dump($tipo);
        $constanteServiceInstance = new ConstanteService();

        // Verifica si el tipo de documento es válido
        $tipoDocumento = isset($data['tipoDocumento']) ? intval($data['tipoDocumento']) : null;
        echo $tipoDocumento;

    }
}
