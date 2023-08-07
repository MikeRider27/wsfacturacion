<?php 

// Incluir las clases de utilidad
require_once 'StringUtilService.php';
require_once 'FechaUtilService.php';

class JSonDteAlgoritmosService {

    private $stringUtilService;
    private $fechaUtilService;

    public function __construct() {
        $this->stringUtilService = new StringUtilService();
        $this->fechaUtilService = new FechaUtilService();
    }
    
    // Función para calcular el dígito verificador
    public function calcularDigitoVerificador($cdc, $baseMax = 11) {
        $v_total = 0;
        $v_resto = 0;
        $k = 0;
        $v_numero_aux = 0;
        $v_numero_al = '';
        $v_caracter = '';
        $v_digit = 0;

        // Convertir letras a su valor ASCII si es necesario
        for ($i = 0; $i < strlen($cdc); $i++) {
            $v_caracter = strtoupper(substr($cdc, $i, 1));
            if (!(ord($v_caracter) >= 48 && ord($v_caracter) <= 57)) {
                $v_numero_al .= ord($v_caracter);
            } else {
                $v_numero_al .= $v_caracter;
            }
        }

        // Calcular el dígito verificador
        $k = 2;
        $v_total = 0;
        for ($i = strlen($v_numero_al); $i > 0; $i--) {
            if ($k > $baseMax) {
                $k = 2;
            }
            $v_numero_aux = intval(substr($v_numero_al, $i - 1, 1));
            $v_total += $v_numero_aux * $k;
            $k++;
        }
        $v_resto = $v_total % 11;
        if ($v_resto > 1) {
            $v_digit = 11 - $v_resto;
        } else {
            $v_digit = 0;
        }
        return $v_digit;
    }

    // Función para generar el código de control
    public function generateCodigoControl($params, $data, $codigoSeguridad) {
        if (strpos($params['ruc'], '-') === false) {
            $result['error'] = 'RUC debe contener dígito verificador en params.ruc';
            echo json_encode(array('error' => $result['error']));
            exit;
        } else {
            $tipoDocumento = $this->stringUtilService->leftZero($data[0]['tipoDocumento'], 2);
            $rucEmisor = $this->stringUtilService->leftZero(explode('-', $params['ruc'])[0], 8);
            $dvEmisor = explode('-', $params['ruc'])[1];
            $establecimiento = $this->stringUtilService->leftZero($data[0]['establecimiento'], 3);
            $punto = $this->stringUtilService->leftZero($data[0]['punto'], 3);
            $numero = $this->stringUtilService->leftZero($data[0]['numero'], 7);
            $tipoContribuyente = $params['tipoContribuyente'];
            $fechaEmision = $this->fechaUtilService->convertToAAAAMMDD(new DateTime($data[0]['fecha']));
            $tipoEmision = $data[0]['tipoEmision'];
            $codigoSeguridadAleatorio = $codigoSeguridad;
            
            $cdc = $tipoDocumento . $rucEmisor . $dvEmisor . $establecimiento . $punto .
            $numero . $tipoContribuyente . $fechaEmision . $tipoEmision . $codigoSeguridadAleatorio;
                      
            $digitoVerificador = $this->calcularDigitoVerificador($cdc, 11);
            $cdc .= $digitoVerificador;
            
            $data[0]['cdc'] = $cdc;
        }        
        return $data[0]['cdc'];
    }

}
