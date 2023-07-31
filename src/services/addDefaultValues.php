<?php
require 'ConstanteService.php';

class addDefaultValues {

    public function DefaultValues(&$data) {

        $tipoDocumento = $data[0]['tipoDocumento'];
        $constanteServiceInstance = new ConstanteService();
        $tiposDocumentos = $constanteServiceInstance->getTiposDocumentos();


       
    }
}