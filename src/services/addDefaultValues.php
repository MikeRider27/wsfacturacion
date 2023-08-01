<?php
require 'ConstantsService.php';

class addDefaultValues {

    public function DefaultValues(&$data) {

        $tipoDocumento = $data[0]['tipoDocumento'];

        var_dump($tipoDocumento);
        $constanteServiceInstance = new ConstantService();
        $tiposDocumentos = $constanteServiceInstance->getTiposDocumentos();


       
    }
}