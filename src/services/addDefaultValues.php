<?php

require 'ConstantsService.php';

class addDefaultValues {

    public function DefaultValues(&$data) {
        $tipoDocumento = $data[0]['tipoDocumento'];

        // Suponiendo que la clase ConstantService tiene un método getTiposDocumentos() que devuelve el array tiposDocumentos
        $constanteServiceInstance = new ConstantService();
        $tiposDocumentos = $constanteServiceInstance->getTiposDocumentos();

        $success = false;
        foreach ($tiposDocumentos as $um) {
            if ($um['codigo'] === $tipoDocumento) {
                $data[0]['tipoDocumentoDescripcion'] = $um['descripcion'];
                $success = true;
                break;
            }
        }

        if (!$success) {
            // Generando el mensaje de error en formato JSON con los valores válidos del array $tiposDocumentos
            $valoresValidos = array_map(function ($um) {
                return $um['codigo'];
            }, $tiposDocumentos);

            $errorMensaje = [
                'success' => false,
                'error' => "Tipo de Documento $tipoDocumento no válido. Los valores válidos son: " . implode(', ', $valoresValidos) . "."
            ];

           echo json_encode($errorMensaje);
           exit;
        }

        // El resto del código sigue siendo el mismo...
        if (!isset($data[0]['tipoEmision'])) {
            $data[0]['tipoEmision'] = 1;
        }

        if (!isset($data[0]['tipoTransaccion'])) {
            $data[0]['tipoTransaccion'] = 1;
        }

        if (!isset($data[0]['moneda'])) {
            $data[0]['moneda'] = 'PYG';
        }

        if ($data[0]['moneda'] !== 'PYG') {
            if (!isset($data[0]['condicionTipoCambio'])) {
                $data[0]['condicionTipoCambio'] = 1; // Por el Global
            }
        }

        // Valores por defecto para los items
        if (isset($data[0]['items']) && is_array($data[0]['items']) && count($data[0]['items']) > 0) {
            foreach ($data[0]['items'] as &$item) {
                if (!isset($item['unidadMedida'])) {
                    $item['unidadMedida'] = 77;
                }
            }
        }
    }
}
