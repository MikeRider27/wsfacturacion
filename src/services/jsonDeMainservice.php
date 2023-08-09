<?php

require_once 'FechaUtilService.php';
require_once 'StringUtilService.php';
require_once 'jsonDteAlgoritmosservice.php';
require_once 'ConstantsService.php';

class JSonDeMainService {
    public $codigoSeguridad = null;
    public $codigoControl = null;
    public $json = array();
    public $validateError = true;

    private $stringUtilService;

    public function __construct() {
        $this->stringUtilService = new StringUtilService();        
        
    }

    public function generateXMLDeService(&$params, &$data) {
      $this->addDefaultValues($data); 
      
      //echo $data['tipoDocumento'];

      $this->generateCodigoControl($params, $data);   
      $DE = $this->generateDe($params, $data);
      $DatOp = $this->generateDatosOperacion($params, $data);
      $Timb = $this->generateDatosTimbrado($params, $data);
      $DatGen = $this->generateDatosGenerales($params, $data);
      //var_dump($DatGen);
        
        
        // Crear el objeto XML con la estructura deseada
        $xml_data = new SimpleXMLElement(
          '<?xml version="1.0" encoding="utf-8" ?>
          <rDE 
              xmlns="http://ekuatia.set.gov.py/sifen/xsd"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="https://ekuatia.set.gov.py/sifen/xsd siRecepDE_v150.xsd">
          </rDE>');   


        // Agregar el elemento dVerFor
        $xml_data->addChild('dVerFor', $params['version']);
        
        // Agregar la estructura 'DE' con atributo 'Id'
        $de_element = $xml_data->addChild('DE');
        $de_element->addAttribute('Id', $this->codigoControl);
        $this->array_to_xml($DE, $de_element);

      

        // Agregar la estructura gOpeDE
        $gOpeDE_element = $de_element->addChild('gOpeDE');   
        $this->array_to_xml($DatOp, $gOpeDE_element);

        // Agregar la estructura gTimb
        $gTimb_element = $de_element->addChild('gTimb'); 
        $this->array_to_xml($Timb, $gTimb_element);
        
        // // Agregar la estructura gDatGen
        $gDatGen_element = $de_element->addChild('gDatGralOpe');
        $this->array_to_xml($DatGen, $gDatGen_element);
       // $gDatGen_element->addChild('dFeEmiDE', $DatGen['gDatGralOpe']['dFeEmiDE']); // 




        // Agregar la estructura Signature debajo de DE
        // $xml_data->addChild('Signature', null, 'http://www.w3.org/2000/09/xmldsig#');


        
        // Convertir el arreglo asociativo a XML dentro del elemento 'DE'
        //$this->array_to_xml($data, $de_element);
        
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

    // Función para convertir un arreglo asociativo a XML
    private function array_to_xml($data, &$xml_data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Verificar si el valor es un elemento singular o una lista de elementos
                if (is_numeric($key)) {
                    // Si es una lista de elementos, no crear un nuevo elemento para cada uno
                    $this->array_to_xml($value, $xml_data);
                } else {
                    // Si es un elemento singular, crear un nuevo elemento con el nombre $key
                    $subnode = $xml_data->addChild($key);
                    $this->array_to_xml($value, $subnode);
                }
            } else {
                $xml_data->addChild($key, htmlspecialchars($value));
            }
        }
    }

    /**
     * Genera el CDC para la Factura
     * Corresponde al Id del DE
     *
     * @param array $params
     * @param array $data
     */
    private function generateCodigoControl($params, $data) {      
        if (isset($data[0]['cdc']) && strlen($data[0]['cdc']) == 44) {
            // Caso ya se le pase el CDC
            $this->codigoSeguridad = substr($data[0]['cdc'], 34, 9);
            $this->codigoControl = $data[0]['cdc'];

            // Como se va a utilizar el CDC enviado como parámetro, se verificará que todos los datos del XML coincidan con el CDC.
            $tipoDocumentoCDC = substr($this->codigoControl, 0, 2);
            $establecimientoCDC = substr($this->codigoControl, 11, 3);
            $puntoCDC = substr($this->codigoControl, 14, 3);
            $numeroCDC = substr($this->codigoControl, 17, 7);
            $fechaCDC = substr($this->codigoControl, 25, 8);
            $tipoEmisionCDC = substr($this->codigoControl, 33, 1);
            $establecimiento = $this->stringUtilService->leftZero($data[0]['establecimiento'], 3);
            $punto = $this->stringUtilService->leftZero($data[0]['punto'], 3);
            $numero = $this->stringUtilService->leftZero($data[0]['numero'], 7);
            $fecha = substr($data[0]['fecha'], 0, 4) . substr($data[0]['fecha'], 5, 2) . substr($data[0]['fecha'], 8, 2);
        } else {
            $codigoSeguridad = $this->stringUtilService->leftZero($data[0]['codigoSeguridadAleatorio'], 9);
            $this->codigoSeguridad = $codigoSeguridad;
            $JSonDteAlgoritmosService = new JSonDteAlgoritmosService();
            $this->codigoControl = $JSonDteAlgoritmosService->generateCodigoControl($params, $data, $codigoSeguridad);           
        }
        $data[0]['cdc'] = $this->codigoControl;        
    }
      
    private function generateDe($params, $data) {
      if (strpos($params['ruc'], '-') === false) {
        $result['error'] = 'RUC debe contener dígito verificador en params.ruc';
        echo json_encode(array('error' => $result['error']));
        exit;
      }
      
      $rucEmisor = explode('-', $params['ruc'])[0];
      $dvEmisor = explode('-', $params['ruc'])[1];
      
      $id = $this->codigoControl;
      
      $fechaFirmaDigital = new DateTime($params['fechaFirmaDigital']);
      
      $digitoVerificadorString = (string)$this->codigoControl;
      
      $jsonResult = array(          
          'dDVId' => substr($digitoVerificadorString, -1),
          'dFecFirma' => date('Y-m-d', strtotime('now')),
          'dSisFact' => 1,
      );
      
      return $jsonResult;
    }

    /**
        * Datos inerentes a la operacion 
        * <gOpeDE>
        *      <iTipEmi>1</iTipEmi>
        *      <dDesTipEmi>Normal</dDesTipEmi>
        *      <dCodSeg>000000023</dCodSeg>
        *      <dInfoEmi>1</dInfoEmi>
        *      <dInfoFisc>Información de interés del Fisco respecto al DE</dInfoFisc>
        * </gOpeDE>
        * @param params 
        * @param data
    */

    private function generateDatosOperacion($params, $data) {
      
      if (strpos($params['ruc'], '-') === false) {
        //throw new Error('RUC debe contener dígito verificador en params.ruc');      
      }

      $rucEmisor = explode('-', $params['ruc'])[0];
      $dvEmisor = explode('-', $params['ruc'])[1];
      
      $id = $this->codigoControl;
      $JSonDteAlgoritmosService = new JSonDteAlgoritmosService();
      $digitoVerificador = $JSonDteAlgoritmosService->calcularDigitoVerificador($rucEmisor, 11);
      
      if (strlen($id) != 44) {
        // Do something here
      }
      
      $codigoSeguridadAleatorio = $this->codigoSeguridad;
      $ConstantService = new ConstantService();      
      $tiposEmisiones = $ConstantService->getTiposEmisiones();

      $tipoEmisionValido = false;
      
      foreach ($tiposEmisiones as $um) {
        if ($um['codigo'] === $data[0]['tipoEmision']) {
          $data[0]['tipoEmisionDescripcion'] = $um['descripcion'];
          $tipoEmisionValido = true;
          break;
        }
      }

      if (!$tipoEmisionValido) {
        /*throw new Error(
          "Tipo de Emisión '" . $data['tipoEmision'] . "' en data.tipoEmision no válido. Valores: " .
          implode('-', array_map(function($a) {
              return $a['codigo'] . '-' . $a['descripcion'];
          }, $constanteService->tiposEmisiones))
        );*/
      }

      $json = array(          
              'iTipEmi' => $data[0]['tipoEmision'],
              'dDesTipEmi' => $data[0]['tipoEmisionDescripcion'],
              'dCodSeg' => $codigoSeguridadAleatorio,          
      ); 
      
      if (isset($data[0]['observacion']) && strlen($data[0]['observacion']) > 0) {
        $json['dInfoEmi'] = $data[0]['observacion'];
      }

      if (isset($data[0]['descripcion']) && strlen($data[0]['descripcion']) > 0) {
        $json['dInfoFisc'] = $data[0]['descripcion'];
      }
    
      return $json;
    }

    /**
     * Genera los datos del timbrado
     * 
     * <gTimb>
     *  <iTiDE>1</iTiDE>
     *  <dDesTiDE>Factura electrónica</dDesTiDE>
     *  <dNumTim>12345678</dNumTim>
     *  <dEst>001</dEst>
     * 	<dPunExp>001</dPunExp>
     * 	<dNumDoc>1000050</dNumDoc>
     * 	<dSerieNum>AB</dSerieNum>
     * 	<dFeIniT>2019-08-13</dFeIniT>
     * </gTimb>
     * @param params 
     * @param data 
     * @param options 
    */
    private function generateDatosTimbrado($params, $data) {
      $json = [         
              'iTiDE' => $data[0]['tipoDocumento'],
              'dDesTiDE' => $data[0]['tipoDocumentoDescripcion'],
              'dNumTim' => $params['timbradoNumero'],
              'dEst' => $this->stringUtilService->leftZero($data[0]['establecimiento'], 3),
              'dPunExp' => $this->stringUtilService->leftZero($data[0]['punto'], 3),
              'dNumDoc' => $this->stringUtilService->leftZero($data[0]['numero'], 7),         
      ];
  
      if (isset($data[0]['items'][0]['numeroSerie'])) {
          $json['dSerieNum'] = $data[0]['items'][0]['numeroSerie'];
      } elseif (isset($data[0]['serie'])) {
          $json['dSerieNum'] = $data[0]['items'][0]['numeroSerie'];
      }
      $json['dFeIniT'] = substr($params['timbradoFecha'], 0, 10);

        return $json;
    }
    
    /**
      * Genera los campos generales, divide las actividades en diferentes métodos
      *
      * <gDatGralOpe>
      *     <dFeEmiDE>2020-05-07T15:03:57</dFeEmiDE>
      * </gDatGralOpe>
      *
      * @param array $params
      * @param array $data
      * @param XmlgenConfig $config
    */
    private function generateDatosGenerales($params, $data) {
      $json = [       
          'dFeEmiDE' => $data[0]['fecha'],        
      ];
      
      $json['gOpeCom'] = $this->generateDatosGeneralesInherentesOperacion($params, $data);
      $json['gEmis'] = $this->generateDatosGeneralesEmisorDE($params, $data);
      
      //if($data[0]['usuario']){
      //  $json['gRespDE'] = $this->generateDatosGeneralesResponsableGeneracionDE($params, $data);
      //}

      $json['gDatRec'] = $this->generateDatosGeneralesReceptorDE($params, $data);


  
     // 
     // if ($config->userObjectRemove == false) {
     //   // Si está TRUE no crea el objeto usuario
     //   if ($data['usuario']) {
     //     // No es obligatorio
     //     $this->generateDatosGeneralesResponsableGeneracionDE($params, $data);
     //   }
     // }
     // $this->generateDatosGeneralesReceptorDE($params, $data);

     return $json;
    
    }

     /**
     * D1. Campos inherentes a la operación comercial (D010-D099)
     * Pertenece al grupo de datos generales
     * 
     * <gOpeCom>
     *       <iTipTra>1</iTipTra>
     *       <dDesTipTra>Venta de mercadería</dDesTipTra>
     *       <iTImp>1</iTImp>
     *       <dDesTImp>IVA</dDesTImp>
     *       <cMoneOpe>PYG</cMoneOpe>
     *       <dDesMoneOpe>Guarani</dDesMoneOpe>
     *   </gOpeCom>
     * @param params 
     * @param data
     */
    private function generateDatosGeneralesInherentesOperacion($params, $data) {
      if ($data[0]['tipoDocumento'] == 7) {
          // C002
          return; // No informa si el tipo de documento es 7
      }
      $ConstantService = new ConstantService(); 
      $tiposTransacciones = $ConstantService->getTiposTransacciones();
      $tiposImpuestos = $ConstantService->getTiposImpuestos();
      $monedas = $ConstantService->getMonedas();
      $globalPorItem = $ConstantService->getGlobalPorItem();
  
      $moneda = $data[0]['moneda'];
      if (!$moneda) {
          $moneda = 'PYG';
      }
  
      $json = array();
    

      if($data[0]['tipoDocumento'] == 1 || $data[0]['tipoDocumento'] == 4) {
        // Obligatorio informar iTipTra D011
        if (!$data[0]['tipoTransaccion']) {
          $error = 'Debe proveer el Tipo de Transacción en data.tipoTransaccion';
          echo json_encode($error);
          exit;
        }
        
        $json['iTipTra'] = $data[0]['tipoTransaccion'];
        foreach ($tiposTransacciones as $um) {
          if ($um['codigo'] === $data[0]['tipoTransaccion']) {
              $json['dDesTipTra'] = $um['descripcion'];            
              break;
          }
        }
      }

      $json['iTImp'] = $data[0]['tipoImpuesto']; // D013
      foreach ($tiposImpuestos as $um) {
        if ($um['codigo'] === $data[0]['tipoImpuesto']) {
            $json['dDesTImp'] = $um['descripcion'];            
            break;
        }
      }

      $json['cMoneOpe'] = $moneda; // D015
      foreach ($monedas as $um) {
        if ($um['codigo'] === $moneda) {
            $json['dDesMoneOpe'] = $um['descripcion'];            
            break;
        }
      }

      if($moneda != 'PYG') {
        if (!$data[0]['tipoCambio']) {
          $error = 'Debe proveer el Tipo de Cambio en data.tipoCambio';
          echo json_encode($error);
          exit;
        }
        //Obligatorio informar dCondTiCam D017
        $json['dCondTiCam'] = $data[0]['condicionTipoCambio'];
      }

      if(isset($data[0]['cambio']) && $data[0]['cambio'] > 0) {
        if ($data[0]['condicionTipoCambio'] == 1 && $moneda != 'PYG') {
          if (!($data[0]['cambio'] && $data[0]['cambio'] > 0)) {
            $error = 'Debe proveer el valor del Cambio en data.cambio';
            echo json_encode($error);
            exit;
          }
          //Obligatorio informar dCondTiCam D018
          $json['dTiCam'] = $data[0]['cambio'];
        }
      }
  
      if (isset($data[0]['condicionAnticipo'])) {
        $json['iCondAnt'] = $data[0]['condicionAnticipo'];
        foreach ($globalPorItem as $um) {
          if ($um['codigo'] === $data[0]['condicionAnticipo']) {
              $json['dDesCondAnt'] = 'Anticipo ' . $um['descripcion'];            
              break;
          }
        }
      }

      return $json;
    }

    /**
      * D2. Campos que identifican al emisor del Documento Electrónico DE (D100-D129)
      * Pertenece al grupo de datos generales
      *
      * @param params
      * @param data
      * @param options
      */
    private function generateDatosGeneralesEmisorDE($params, $data) {
      if(!$params && !$params['establecimientos']) {
        $error = 'Debe proveer un Array con la información de los establecimientos en params';
        echo json_encode($error);
        exit;
      }

      $ConstantService = new ConstantService();      
      $departamentos = $ConstantService->getDepartamentos();
      $distritos = $ConstantService->getDistritos();
      $ciudades = $ConstantService->getCiudades();

      //Validar si el establecimiento viene en params
      $establecimiento = $this->stringUtilService->leftZero($data[0]['establecimiento'], 3);
      $establecimientoEncontrado = false;
      foreach ($params['establecimientos'] as $um) {
        if ($um['codigo'] === $establecimiento) {
          $establecimientoEncontrado = true;
          break;
        }
      }

      if (!$establecimientoEncontrado) {
        $valor = array_map(function ($um) {
          return $um['codigo'] . '-' . $um['denominacion'];
        }, $params['establecimientos']);

        $error = [
          'error' => 'Establecimiento ' . $establecimiento . ' no encontrado en params.establecimientos*.codigo. Valores:' . implode(', ', $valor) . '.',
        ];
        echo json_encode($error);
        exit;
      }

      if (strpos($params['ruc'], '-') === false) {       
        $error = 'RUC debe contener dígito verificador en params.ruc';
        echo json_encode($error);
        exit;
      }

      $json = array();
      $ruc = explode('-', $params['ruc']);
      $json['dRucEm'] = $ruc[0];
      $json['dDVEmi'] = $ruc[1];
      $json['iTipCont'] = $params['tipoContribuyente'];
      if (isset($params['tipoRegimen'])) {
        $json['cTipReg'] = $params['tipoRegimen'];
      }
      $json['dNomEmi'] = $params['razonSocial'];
      if (isset($params['nombreFantasia']) && strlen((string)$params['nombreFantasia']) > 0) {
        $json['dNomFanEmi'] = $params['nombreFantasia'];
      }
      $json['dDirEmi'] = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['direccion'];
      $json['dNumCas'] = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['numeroCasa']; 

      $dCompDir1 = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['complementoDireccion1'];
      
      if ($dCompDir1 && strlen((string)$dCompDir1) > 1) {
        $json['dCompDir1'] = $dCompDir1;
      }
      
      $dCompDir2 = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['complementoDireccion2'];
      
      if ($dCompDir2 && strlen((string)$dCompDir2) > 1) {
        $json['dCompDir2'] = $dCompDir2;
      }

      $json['cDepEmi'] = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['departamento'];          
      
      foreach ($departamentos as $um) {
        if ($um['codigo'] === $json['cDepEmi']) {
          $json['dDesDepEmi'] = $um['descripcion'];
          break;
        }
      }

      $json['cDisEmi'] = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['distrito'];
      
      foreach ($distritos as $um) {
        if ($um['codigo'] === $json['cDisEmi']) {
          $json['dDesDisEmi'] = $um['descripcion'];
          break;
        }
      }

      $json['cCiuEmi'] = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['ciudad'];

      foreach ($ciudades as $um) {
        if ($um['codigo'] === $json['cCiuEmi']) {
          $json['dDesCiuEmi'] = $um['descripcion'];
          break;
        }
      }

      $json['dTelEmi'] = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['telefono'];

      if(array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['email']) {

        $email = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
          return $e['codigo'] === $establecimiento;
        })[0]['email'];

         //Verificar si tiene varios correos.
        if (strpos($email, ',') !== false) {
          //Si el Email tiene , (coma) entonces va enviar solo el primer valor, ya que la SET no acepta Comas
          $email = explode(',', $email)[0];
        }

        $json['dEmailE'] = $email;
      }

      $json['dDenSuc'] = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
        return $e['codigo'] === $establecimiento;
      })[0]['denominacion'];

      if (isset($params['actividadesEconomicas']) && count($params['actividadesEconomicas']) > 0) {
        $json['gActEco'] = [];
        foreach ($params['actividadesEconomicas'] as $actividadEconomica) {
          $gActEco = [
            'cActEco' => $actividadEconomica['codigo'],
            'dDesActEco' => $actividadEconomica['descripcion'],
          ];
          $json['gActEco'][] = $gActEco;
        }
      } else {
        $error = 'Debe proveer el array de actividades económicas en params.actividadesEconomicas';
        echo json_encode($error);
        exit;
      }

      return $json;
    }

    /**
      * Datos generales del responsable de generacion del DE
      *
      * @param params
      * @param data
      * @param options
      */
    private function generateDatosGeneralesResponsableGeneracionDE($params, $data) {
      $ConstantService = new ConstantService();
      $tiposDocumentosIdentidades = $ConstantService->getTiposDocumentosIdentidades();

      $json = [
        'iTipIDRespDE' => $data[0]['usuario']['documentoTipo'],
        'dDTipIDRespDE' => array_filter($tiposDocumentosIdentidades, function ($td) use ($data) {
          return $td['codigo'] === $data[0]['usuario']['documentoTipo'];
        })[0]['descripcion'],
      ];

      if ($data[0]['usuario']['documentoTipo'] == 9) {
        $json['dDTipIDRespDE'] = $data[0]['usuario']['documentoTipoDescripcion'];
      }

      $json['dNumIDRespDE'] = $data[0]['usuario']['documentoNumero'];
      $json['dNomRespDE'] = $data[0]['usuario']['nombre'];
      $json['dCarRespDE'] = $data[0]['usuario']['cargo'];

      return $json;
    }

    /**
     * Datos generales del receptor del documento electrónico
     * Pertenece al grupo de datos generales
     * 
     * <gDatRec>
     *           <iNatRec>1</iNatRec>
     *           <iTiOpe>1</iTiOpe>
     *           <cPaisRec>PRY</cPaisRec>
     *           <dDesPaisRe>Paraguay</dDesPaisRe>
     *           <iTiContRec>2</iTiContRec>
     *           <dRucRec>00000002</dRucRec>
     *           <dDVRec>7</dDVRec>
     *           <dNomRec>RECEPTOR DEL DOCUMENTO</dNomRec>
     *           <dDirRec>CALLE 1 ENTRE CALLE 2 Y CALLE 3</dDirRec>
     *           <dNumCasRec>123</dNumCasRec>
     *           <cDepRec>1</cDepRec>
     *           <dDesDepRec>CAPITAL</dDesDepRec>
     *           <cDisRec>1</cDisRec>
     *           <dDesDisRec>ASUNCION (DISTRITO)</dDesDisRec>
     *           <cCiuRec>1</cCiuRec>
     *           <dDesCiuRec>ASUNCION (DISTRITO)</dDesCiuRec>
     *           <dTelRec>012123456</dTelRec>
     *           <dCodCliente>AAA</dCodCliente>
     *       </gDatRec>
     * 
     * @param params 
     * @param data  
     */
    private function generateDatosGeneralesReceptorDE($params, $data) {
      $ConstantService = new ConstantService();
      $paises = $ConstantService->getPaises();
      $tiposDocumentosReceptor = $ConstantService->getTiposDocumentosReceptor();
      $departamentos = $ConstantService->getDepartamentos();
      $distritos = $ConstantService->getDistritos();
      $ciudades = $ConstantService->getCiudades();

      foreach ($paises as $um) {
        if ($um['codigo'] === $data[0]['cliente']['pais']) {
            $um['descripcion'];
          break;
        }
      }     

      $json = [
        'iNatRec' => $data[0]['cliente']['contribuyente'] ? 1 : 2,
        'iTiOpe' => $data[0]['cliente']['tipoOperacion'],
        'cPaisRec' => $data[0]['cliente']['pais'],
        'dDesPaisRe' => $um['descripcion'],      
      ];

      if ($data[0]['cliente']['contribuyente']) {
        $json['iTiContRec'] = $data[0]['cliente']['tipoContribuyente'];
        $ruccli = explode('-', $data[0]['cliente']['ruc']);
        $json['dRucRec'] = TRIM($ruccli[0]);
        $json['dDVRec'] = TRIM($ruccli[1]);
      }

      if (!$data[0]['cliente']['contribuyente'] && isset($data[0]['cliente']['tipoOperacion'])) {
        //Obligatorio completar D210

        if (isset($data[0]['cliente']['documentoTipo'])) {
          $json['iTipIDRec'] = $data[0]['cliente']['documentoTipo'];
          $json['dDTipIDRec'] = array_filter($tiposDocumentosReceptor, function ($td) use ($data) {
            return $td['codigo'] === $data[0]['cliente']['documentoTipo'];
          })[0]['descripcion'];
        }

        if ($data[0]['cliente']['documentoTipo'] === 9) {
          $json['dDTipIDRec'] = $data[0]['cliente']['documentoTipoDescripcion'];
        }

        $json['dNumIDRec'] = TRIM($data[0]['cliente']['documentoNumero']);

        if ($data[0]['cliente']['documentoTipo'] === 5) {
          // Si es innominado completar con cero
          $json['dNumIDRec'] = '0';
        }
        
      }

      $json['dNomRec'] = TRIM($data[0]['cliente']['razonSocial']);

      if ($data[0]['cliente']['documentoTipo'] === 5) {
        $json['dNomRec'] = 'Sin Nombre';
      }

      if ($data[0]['cliente']['nombreFantasia']) {
        $json['dNomFanRec'] = TRIM($data[0]['cliente']['nombreFantasia']);
      }

      if ($data[0]['cliente']['direccion']) {
        //eliminar los espacios
        $json['dDirRec'] = TRIM($data[0]['cliente']['direccion']);
      }

      if ($data[0]['cliente']['numeroCasa']) {
        $json['dNumCasRec'] = TRIM($data[0]['cliente']['numeroCasa']);
      }

      if ($data[0]['cliente']['direccion'] && $data[0]['cliente']['tipoOperacion'] != 4) {
        $json['cDepRec'] = $data[0]['cliente']['departamento'];        
        foreach ($departamentos as $um) {
          if ($um['codigo'] === $data[0]['cliente']['departamento']) {
            $json['dDesDepRec'] = $um['descripcion'];
            break;
          }
        }
        $json['cDisRec'] = $data[0]['cliente']['distrito'];
        foreach ($distritos as $um) {
          if ($um['codigo'] === $data[0]['cliente']['distrito']) {
            $json['dDesDisRec'] = $um['descripcion'];
            break;
          }
        }
        $json['cCiuRec'] = $data[0]['cliente']['ciudad'];
        foreach ($ciudades as $um) {
          if ($um['codigo'] === $data[0]['cliente']['ciudad']) {
            $json['dDesCiuRec'] = $um['descripcion'];
            break;
          }
        }        
      }

      if ($data[0]['cliente']['telefono']) {
        $json['dTelRec'] = TRIM($data[0]['cliente']['telefono']);
      }

      if ($data[0]['cliente']['celular']) {
        $json['dCelRec'] = TRIM($data[0]['cliente']['celular']);
      }

      if ($data[0]['cliente']['email']) {
        $email = $data[0]['cliente']['email']; //Hace una copia, para no alterar.
  
        //Verificar si tiene varios correos.
        if (strpos($email, ',') > -1) {
          //Si el Email tiene , (coma) entonces va enviar solo el primer valor, ya que la SET no acepta Comas
          $email = explode(',', $email)[0];
        }

        $json['dEmailRec'] = TRIM($email);
      }
  
      if ($data[0]['cliente']['codigo']) {
        $json['dCodCliente'] = TRIM($data[0]['cliente']['codigo']);
      }
  

      



      return $json;

  }

  
  



  

    




    /*

    private function removeUnderscoreAndPutCamelCase(&$data) {
        if (isset($data[0]['tipo_documento'])) {
            $data[0]['tipoDocumento'] = $data[0]['tipo_documento'];
            unset($data[0]['tipo_documento']);
        }

        if (isset($data[0]['tipo_contribuyente'])) {
            $data[0]['tipoContribuyente'] = $data[0]['tipo_contribuyente'];
            unset($data[0]['tipo_contribuyente']);
        }

        if (isset($data[0]['tipo_emision'])) {
            $data[0]['tipoEmision'] = $data[0]['tipo_emision'];
            unset($data[0]['tipo_emision']);
        }

        if (isset($data[0]['tipo_transaccion'])) {
            $data[0]['tipoTransaccion'] = $data[0]['tipo_transaccion'];
            unset($data[0]['tipo_transaccion']);
        }

        if (isset($data[0]['tipo_impuesto'])) {
            $data[0]['tipoImpuesto'] = $data[0]['tipo_impuesto'];
            unset($data[0]['tipo_impuesto']);
        }
        
        if (isset($data[0]['condicion_anticipo'])) {
            $data[0]['condicionAnticipo'] = $data[0]['condicion_anticipo'];
            unset($data[0]['condicion_anticipo']);
        }

        if (isset($data[0]['condicion_tipo_cambio'])) {
            $data[0]['condicionTipoCambio'] = $data[0]['condicion_tipo_cambio'];
            unset($data[0]['condicion_tipo_cambio']);
        }

        //Objeto Cliente
        if (isset($data[0]['cliente']['razon_social'])) {
            $data[0]['cliente']['razonSocial'] = $data[0]['cliente']['razon_social'];
            unset($data[0]['cliente']['razon_social']);
        }

        if (isset($data[0]['cliente']['nombre_fantasia'])) {
            $data[0]['cliente']['nombreFantasia'] = $data[0]['cliente']['nombre_fantasia'];
            unset($data[0]['cliente']['nombre_fantasia']);
        }

        if (isset($data[0]['cliente']['tipo_operacion'])) {
            $data[0]['cliente']['tipoOperacion'] = $data[0]['cliente']['tipo_operacion'];
            unset($data[0]['cliente']['tipo_operacion']);
        }
        
        //Campo que puede ser un numero = 0, hay que validar de esta forma
        if (isset($data[0]['cliente']) && isset($data[0]['cliente']['numero_casa'])) {
            if (isset($data[0]['cliente']['numero_casa'])) {
                $data[0]['cliente']['numeroCasa'] = $data[0]['cliente']['numero_casa'] . '';
                unset($data[0]['cliente']['numero_casa']);
            }
        }

        if (isset($data[0]['cliente']['tipo_contribuyente'])) {
          $data[0]['cliente']['tipoContribuyente'] = $data[0]['cliente']['tipo_contribuyente'];
          unset($data[0]['cliente']['tipo_contribuyente']);
        }

        if (isset($data[0]['cliente']['documento_tipo'])) {
          $data[0]['cliente']['documentoTipo'] = $data[0]['cliente']['documento_tipo'];
          unset($data[0]['cliente']['documento_tipo']);
        }
        
        if (isset($data[0]['cliente']['documento_tipo_descripcion'])) {
          $data[0]['cliente']['documentoTipoDescripcion'] = $data[0]['cliente']['documento_tipo_descripcion'];
          unset($data[0]['cliente']['documento_tipo_descripcion']);
        }
        if (isset($data[0]['cliente']['documento_numero'])) {
          $data[0]['cliente']['documentoNumero'] = $data[0]['cliente']['documento_numero'];
          unset($data[0]['cliente']['documento_numero']);
        }

        //Objeto Usuario
        if (isset($data[0]['usuario']['documento_tipo'])) {
          $data[0]['usuario']['documentoTipo'] = $data[0]['usuario']['documento_tipo'];
          unset($data[0]['usuario']['documento_tipo']);
        }

        if (isset($data[0]['usuario']['documento_tipo_descripcion'])) {
          $data[0]['usuario']['documentoTipoDescripcion'] = $data[0]['usuario']['documento_tipo_descripcion'];
          unset($data[0]['usuario']['documento_tipo_descripcion']);
        }

        if (isset($data[0]['usuario']['documento_numero'])) {
          $data[0]['usuario']['documentoNumero'] = $data[0]['usuario']['documento_numero'];
          unset($data[0]['usuario']['documento_numero']);
        }

        //Objeto Factura
        if (isset($data[0]['factura']['fecha_envio'])) {
          $data[0]['factura']['fechaEnvio'] = $data[0]['factura']['fecha_envio'];
          unset($data[0]['factura']['fecha_envio']);
        }

        //Objeto AutoFactura
        if (isset($data[0]['auto_factura'])) {
          $data[0]['autoFactura'] = $data[0]['auto_factura'];
          unset($data[0]['auto_factura']);
        }

        if (isset($data[0]['autoFactura']['tipo_vendedor'])) {
          $data[0]['autoFactura']['tipoVendedor'] = $data[0]['autoFactura']['tipo_vendedor'];
          unset($data[0]['autoFactura']['tipo_vendedor']);
        }

        if (isset($data[0]['autoFactura']['documento_tipo'])) {
          $data[0]['autoFactura']['documentoTipo'] = $data[0]['autoFactura']['documento_tipo'];
          unset($data[0]['autoFactura']['documento_tipo']);
        }

        if (isset($data[0]['autoFactura']['documento_numero'])) {
          $data[0]['autoFactura']['documentoNumero'] = $data[0]['autoFactura']['documento_numero'];
          unset($data[0]['autoFactura']['documento_numero']);
        }

        if (isset($data[0]['autoFactura']['numero_casa'])) {
          $data[0]['autoFactura']['numeroCasa'] = $data[0]['autoFactura']['numero_casa'];
          unset($data[0]['autoFactura']['numero_casa']);
        }

        //Objeto Remision
        if (isset($data[0]['nota_credito_debito'])) {
          $data[0]['notaCreditoDebito'] = $data[0]['nota_credito_debito'];
          unset($data[0]['nota_credito_debito']);
        }

        //Objeto Remision
        if (isset($data[0]['remision']['tipo_responsable'])) {
          $data[0]['remision']['tipoResponsable'] = $data[0]['remision']['tipo_responsable'];
          unset($data[0]['remision']['tipo_responsable']);
        }

        //Objeto Documento Asociado
        if (isset($data[0]['documento_asociado'])) {
          $data[0]['documentoAsociado'] = $data[0]['documento_asociado'];
          unset($data[0]['documento_asociado']);
        }

        if (isset($data[0]['documentoAsociado']['numero_retencion'])) {
          $data[0]['documentoAsociado']['numeroRetencion'] = $data[0]['documentoAsociado']['numero_retencion'];
          unset($data[0]['documentoAsociado']['numero_retencion']);
        }

        if (isset($data[0]['documentoAsociado']['resolucion_credito_fiscal'])) {
          $data[0]['documentoAsociado']['resolucionCreditoFiscal'] = $data[0]['documentoAsociado']['resolucion_credito_fiscal'];
          unset($data[0]['documentoAsociado']['resolucion_credito_fiscal']);
        }

        if (isset($data[0]['documentoAsociado']['tipo_documento_impreso'])) {
          $data[0]['documentoAsociado']['tipoDocumentoImpreso'] = $data[0]['documentoAsociado']['tipo_documento_impreso'];
          unset($data[0]['documentoAsociado']['tipo_documento_impreso']);
        }

        if (isset($data[0]['documentoAsociado']['constancia_tipo'])) {
          $data[0]['documentoAsociado']['constanciaTipo'] = $data[0]['documentoAsociado']['constancia_tipo'];
          unset($data[0]['documentoAsociado']['constancia_tipo']);
        }

        if (isset($data[0]['documentoAsociado']['constancia_numero'])) {
          $data[0]['documentoAsociado']['constanciaNumero'] = $data[0]['documentoAsociado']['constancia_numero'];
          unset($data[0]['documentoAsociado']['constancia_numero']);
        }

        if (isset($data[0]['documentoAsociado']['constancia_control'])) {
          $data[0]['documentoAsociado']['constanciaControl'] = $data[0]['documentoAsociado']['constancia_control'];
          unset($data[0]['documentoAsociado']['constancia_control']);
        }

        //Objeto Condicion entregas
        if (isset($data[0]['condicion']['entregas']) && count($data[0]['condicion']['entregas']) > 0) {
          foreach ($data[0]['condicion']['entregas'] as $i => $entrega) {
              if (isset($entrega['info_tarjeta'])) {
                  $data[0]['condicion']['entregas'][$i]['infoTarjeta'] = $entrega['info_tarjeta'];
                  unset($data[0]['condicion']['entregas'][$i]['info_tarjeta']);
              }
      
              if (isset($entrega['infoTarjeta']['razon_social'])) {
                  $data[0]['condicion']['entregas'][$i]['infoTarjeta']['razonSocial'] = $entrega['infoTarjeta']['razon_social'];
                  unset($data[0]['condicion']['entregas'][$i]['infoTarjeta']['razon_social']);
              }
      
              if (isset($entrega['infoTarjeta']['medio_pago'])) {
                  $data[0]['condicion']['entregas'][$i]['infoTarjeta']['medioPago'] = $entrega['infoTarjeta']['medio_pago'];
                  unset($data[0]['condicion']['entregas'][$i]['infoTarjeta']['medio_pago']);
              }
      
              if (isset($entrega['infoTarjeta']['codigo_autorizacion'])) {
                  $data[0]['condicion']['entregas'][$i]['infoTarjeta']['codigoAutorizacion'] = $entrega['infoTarjeta']['codigo_autorizacion'];
                  unset($data[0]['condicion']['entregas'][$i]['infoTarjeta']['codigo_autorizacion']);
              }
      
              if (isset($entrega['info_cheque'])) {
                  $data[0]['condicion']['entregas'][$i]['infoCheque'] = $entrega['info_cheque'];
                  unset($data[0]['condicion']['entregas'][$i]['info_cheque']);
              }
      
              if (isset($entrega['infoCheque']['numero_cheque'])) {
                  $data[0]['condicion']['entregas'][$i]['infoCheque']['numeroCheque'] = $entrega['infoCheque']['numero_cheque'];
                  unset($data[0]['condicion']['entregas'][$i]['infoCheque']['numero_cheque']);
              }
            }
        }
        if (isset($data[0]['condicion']['monto_entrega'])) {
          $data[0]['condicion']['montoEntrega'] = $data[0]['condicion']['monto_entrega'];
          unset($data[0]['condicion']['monto_entrega']);
        }

        if (isset($data[0]['condicion']['credito'])) {
          if (isset($data[0]['condicion']['credito']['info_cuotas'])) {
            $data[0]['condicion']['credito']['infoCuotas'] = $data[0]['condicion']['credito']['info_cuotas'];
            unset($data[0]['condicion']['credito']['info_cuotas']);
          }
        }
        
        //Items
        if (isset($data[0]['items']) && count($data[0]['items']) > 0) {
          foreach ($data[0]['items'] as $i => $item) {
            if (isset($item['partida_arancelaria'])) {
              $data[0]['items'][$i]['partidaArancelaria'] = $item['partida_arancelaria'];
            }
            if (isset($item['unidad_medida'])) {
              $data[0]['items'][$i]['unidadMedida'] = $item['unidad_medida'];
            }
            if (isset($item['precio_unitario'])) {
              $data[0]['items'][$i]['precioUnitario'] = $item['precio_unitario'];
            }
            if (isset($item['tolerancia_cantidad'])) {
              $data[0]['items'][$i]['toleranciaCantidad'] = $item['tolerancia_cantidad'];
            }
            if (isset($item['tolerancia_porcentaje'])) {
              $data[0]['items'][$i]['toleranciaPorcentaje'] = $item['tolerancia_porcentaje'];
            }
            if (isset($item['cdc_anticipo'])) {
              $data[0]['items'][$i]['cdcAnticipo'] = $item['cdc_anticipo'];
            }
            if (isset($item['iva_tipo'])) {
              $data[0]['items'][$i]['ivaTipo'] = $item['iva_tipo'];
            }

            if (isset($item['iva_base'])) {
              $data[0]['items'][$i]['ivaBase'] = $item['iva_base'];
            }
            if (isset($item['numero_serie'])) {
              $data[0]['items'][$i]['numeroSerie'] = $item['numero_serie'];
            }
            if (isset($item['numero_pedido'])) {
              $data[0]['items'][$i]['numeroPedido'] = $item['numero_pedido'];
            }
            if (isset($item['numero_seguimiento'])) {
              $data[0]['items'][$i]['numeroSeguimiento'] = $item['numero_seguimiento'];
            }
            
            // DNCP
            if (isset($item['dncp'])) {
              if (isset($item['dncp']['codigo_nivel_general'])) {
                $data[0]['items'][$i]['dncp']['codigoNivelGeneral'] = $item['dncp']['codigo_nivel_general'];
              }
        
              if (isset($item['dncp']['codigo_nivel_especifico'])) {
                $data[0]['items'][$i]['dncp']['codigoNivelEspecifico'] = $item['dncp']['codigo_nivel_especifico'];
              }
        
              if (isset($item['dncp']['codigo_gtin_producto'])) {
                $data[0]['items'][$i]['dncp']['codigoGtinProducto'] = $item['dncp']['codigo_gtin_producto'];
              }
        
              if (isset($item['dncp']['codigo_nivel_paquete'])) {
                $data[0]['items'][$i]['dncp']['codigoNivelPaquete'] = $item['dncp']['codigo_nivel_paquete'];
              }
            }
            
            // Importador
            if (isset($item['importador'])) {
              if (isset($item['importador']['registro_importador'])) {
                $data[0]['items'][$i]['importador']['registroImportador'] = $item['importador']['registro_importador'];
              }
        
              if (isset($item['registro_senave'])) {
                $data[0]['items'][$i]['registroSenave'] = $item['registro_senave'];
              }
        
              if (isset($item['registro_entidad_comercial'])) {
                $data[0]['items'][$i]['registroEntidadComercial'] = $item['registro_entidad_comercial'];
              }
            }

            // Sector Automotor
            if (isset($item['sector_automotor'])) {
              if (isset($item['sector_automotor']['capacidad_motor'])) {
                $data[0]['items'][$i]['sector_automotor']['capacidadMotor'] = $item['sector_automotor']['capacidad_motor'];
              }
        
              if (isset($item['sector_automotor']['capacidad_pasajeros'])) {
                $data[0]['items'][$i]['sector_automotor']['capacidadPasajeros'] = $item['sector_automotor']['capacidad_pasajeros'];
              }
        
              if (isset($item['sector_automotor']['peso_bruto'])) {
                $data[0]['items'][$i]['sector_automotor']['pesoBruto'] = $item['sector_automotor']['peso_bruto'];
              }
        
              if (isset($item['sector_automotor']['peso_neto'])) {
                $data[0]['items'][$i]['sector_automotor']['pesoNeto'] = $item['sector_automotor']['peso_neto'];
              }
        
              if (isset($item['sector_automotor']['tipo_combustible'])) {
                $data[0]['items'][$i]['sector_automotor']['tipoCombustible'] = $item['sector_automotor']['tipo_combustible'];
              }
        
              if (isset($item['sector_automotor']['numero_motor'])) {
                $data[0]['items'][$i]['sector_automotor']['numeroMotor'] = $item['sector_automotor']['numero_motor'];
              }
        
              if (isset($item['sector_automotor']['capacidad_traccion'])) {
                $data[0]['items'][$i]['sector_automotor']['capacidadTraccion'] = $item['sector_automotor']['capacidad_traccion'];
              }
        
              if (isset($item['sector_automotor']['tipo_vehiculo'])) {
                $data[0]['items'][$i]['sector_automotor']['tipoVehiculo'] = $item['sector_automotor']['tipo_vehiculo'];
              }
            }
          }
        }

        // Detalle de Tranposte
        if (isset($data[0]['detalle_transporte'])) {
          $data[0]['detalleTransporte'] = $data[0]['detalle_transporte'];
        }

        if (isset($data[0]['transporte'])) {
          //Nueva version quedara solamente data.trasnsporte
          $data[0]['detalleTransporte'] = $data[0]['transporte'];
        }

        if (isset($data[0]['detalleTransporte']['tipo_responsable'])) {
          $data[0]['detalleTransporte']['tipoResponsable'] = $data[0]['detalleTransporte']['tipo_responsable'];
        }

        if (isset($data[0]['detalleTransporte']['condicion_negociacion'])) {
          $data[0]['detalleTransporte']['condicionNegociacion'] = $data[0]['detalleTransporte']['condicion_negociacion'];
        }

        if (isset($data[0]['detalleTransporte']['numero_manifiesto'])) {
          $data[0]['detalleTransporte']['numeroManifiesto'] = $data[0]['detalleTransporte']['numero_manifiesto'];
        }

        if (isset($data[0]['detalleTransporte']['numero_despacho_importacion'])) {
          $data[0]['detalleTransporte']['numeroDespachoImportacion'] = $data[0]['detalleTransporte']['numero_despacho_importacion'];
        }

        if (isset($data[0]['detalleTransporte']['inicio_estimado_translado'])) {
          $data[0]['detalleTransporte']['inicioEstimadoTranslado'] = $data[0]['detalleTransporte']['inicio_estimado_translado'];
        }

        if (isset($data[0]['detalleTransporte']['fin_estimado_translado'])) {
          $data[0]['detalleTransporte']['finEstimadoTranslado'] = $data[0]['detalleTransporte']['fin_estimado_translado'];
        }

        if (isset($data[0]['detalleTransporte']['pais_destino'])) {
          $data[0]['detalleTransporte']['paisDestino'] = $data[0]['detalleTransporte']['pais_destino'];
        }

        if (isset($data[0]['detalleTransporte']['pais_destino_nombre'])) {
          $data[0]['detalleTransporte']['paisDestinoNombre'] = $data[0]['detalleTransporte']['pais_destino_nombre'];
        }

        //Falta los de salida, entrega, etc.

        //Detalle de Transporte Salida
        if (isset($data[0]['detalleTransporte']['salida']['numero_casa'])) {
          //Nueva version quedara solamente data.trasnsporte
          $data[0]['detalleTransporte']['salida']['numeroCasa'] = $data[0]['detalleTransporte']['salida']['numero_casa'];
        }

        if (isset($data[0]['detalleTransporte']['salida']['complemento_direccion1'])) {
          $data[0]['detalleTransporte']['salida']['complementoDireccion1'] = $data[0]['detalleTransporte']['salida']['complemento_direccion1'];
        }

        if (isset($data[0]['detalleTransporte']['salida']['complemento_direccion2'])) {
          $data[0]['detalleTransporte']['salida']['complementoDireccion2'] = $data[0]['detalleTransporte']['salida']['complemento_direccion2'];
        }

        if (isset($data[0]['detalleTransporte']['salida']['departamento_descripcion'])) {
          $data[0]['detalleTransporte']['salida']['departamentoDescripcion'] = $data[0]['detalleTransporte']['salida']['departamento_descripcion'];
        }

        if (isset($data[0]['detalleTransporte']['salida']['distrito_descripcion'])) {
          $data[0]['detalleTransporte']['salida']['distritoDescripcion'] = $data[0]['detalleTransporte']['salida']['distrito_descripcion'];
        }

        if (isset($data[0]['detalleTransporte']['salida']['ciudad_descripcion'])) {
          $data[0]['detalleTransporte']['salida']['ciudadDescripcion'] = $data[0]['detalleTransporte']['salida']['ciudad_descripcion'];
        }

        if (isset($data[0]['detalleTransporte']['salida']['pais_descripcion'])) {
          $data[0]['detalleTransporte']['salida']['paisDescripcion'] = $data[0]['detalleTransporte']['salida']['pais_descripcion'];
        }

        if (isset($data[0]['detalleTransporte']['salida']['telefono_contacto'])) {
          $data[0]['detalleTransporte']['salida']['telefonoContacto'] = $data[0]['detalleTransporte']['salida']['telefono_contacto'];
        }

        //Detalle de Transporte Entrega
        if (isset($data[0]['detalleTransporte']['entrega']['numero_casa'])) {
          //Nueva version quedara solamente data.trasnsporte
          $data[0]['detalleTransporte']['entrega']['numeroCasa'] = $data[0]['detalleTransporte']['entrega']['numero_casa'];
        }

        if (isset($data[0]['detalleTransporte']['entrega']['complemento_direccion1'])) {
          $data[0]['detalleTransporte']['entrega']['complementoDireccion1'] = $data[0]['detalleTransporte']['entrega']['complemento_direccion1'];
        }

        if (isset($data[0]['detalleTransporte']['entrega']['complemento_direccion2'])) {
          $data[0]['detalleTransporte']['entrega']['complementoDireccion2'] = $data[0]['detalleTransporte']['entrega']['complemento_direccion2'];
        }

        if (isset($data[0]['detalleTransporte']['entrega']['departamento_descripcion'])) {
          $data[0]['detalleTransporte']['entrega']['departamentoDescripcion'] = $data[0]['detalleTransporte']['entrega']['departamento_descripcion'];
        }

        if (isset($data[0]['detalleTransporte']['entrega']['distrito_descripcion'])) {
          $data[0]['detalleTransporte']['entrega']['distritoDescripcion'] = $data[0]['detalleTransporte']['entrega']['distrito_descripcion'];
        }

        if (isset($data[0]['detalleTransporte']['entrega']['ciudad_descripcion'])) {
          $data[0]['detalleTransporte']['entrega']['ciudadDescripcion'] = $data[0]['detalleTransporte']['entrega']['ciudad_descripcion'];
        }

        if (isset($data[0]['detalleTransporte']['entrega']['pais_descripcion'])) {
          $data[0]['detalleTransporte']['entrega']['paisDescripcion'] = $data[0]['detalleTransporte']['entrega']['pais_descripcion'];
        }

        if (isset($data[0]['detalleTransporte']['entrega']['telefono_contacto'])) {
          $data[0]['detalleTransporte']['entrega']['telefonoContacto'] = $data[0]['detalleTransporte']['entrega']['telefono_contacto'];
        }

        //Detalle de Transporte Vehiculo
        if (isset($data[0]['detalleTransporte']['vehiculo']['documento_tipo'])) {
          $data[0]['detalleTransporte']['vehiculo']['documentoTipo'] = $data[0]['detalleTransporte']['vehiculo']['documento_tipo'];
        }

        if (isset($data[0]['detalleTransporte']['vehiculo']['documento_numero'])) {
          $data[0]['detalleTransporte']['vehiculo']['documentoNumero'] = $data[0]['detalleTransporte']['vehiculo']['documento_numero'];
        }

        if (isset($data[0]['detalleTransporte']['vehiculo']['numero_matricula'])) {
          $data[0]['detalleTransporte']['vehiculo']['numeroMatricula'] = $data[0]['detalleTransporte']['vehiculo']['numero_matricula'];
        }

        if (isset($data[0]['detalleTransporte']['vehiculo']['numero_vuelo'])) {
          $data[0]['detalleTransporte']['vehiculo']['numeroVuelo'] = $data[0]['detalleTransporte']['vehiculo']['numero_vuelo'];
        }

        //Detalle de Transporte Transportista
        if (isset($data[0]['detalleTransporte']['transportista']['documento_tipo'])) {
          $data[0]['detalleTransporte']['transportista']['documentoTipo'] = $data[0]['detalleTransporte']['transportista']['documento_tipo'];
        }

        if (isset($data[0]['detalleTransporte']['transportista']['documento_numero'])) {
          $data[0]['detalleTransporte']['transportista']['documentoNumero'] = $data[0]['detalleTransporte']['transportista']['documento_numero'];
        }

        if (isset($data[0]['detalleTransporte']['transportista']['pais_descripcion'])) {
          $data[0]['detalleTransporte']['transportista']['paisDescripcion'] = $data[0]['detalleTransporte']['transportista']['pais_descripcion'];
        }

        //Detalle de Transporte Transportista Chofer
        if (isset($data[0]['detalleTransporte']['transportista']['chofer']['documento_numero'])) {
          $data[0]['detalleTransporte']['transportista']['chofer']['documentoNumero'] = $data[0]['detalleTransporte']['transportista']['chofer']['documento_numero'];
        }

        //Data Complementarios
        if (isset($data[0]['complementarios']['orden_compra'])) {
          $data[0]['complementarios']['ordenCompra'] = $data[0]['complementarios']['orden_compra'];
        }

        if (isset($data[0]['complementarios']['orden_venta'])) {
          $data[0]['complementarios']['ordenVenta'] = $data[0]['complementarios']['orden_venta'];
        }

        if (isset($data[0]['complementarios']['numero_asiento'])) {
          $data[0]['complementarios']['numeroAsiento'] = $data[0]['complementarios']['numero_asiento'];
        }

        //Data complementarios carga
        if (isset($data[0]['complementarios']['carga']['orden_compra'])) {
          $data[0]['complementarios']['carga']['ordenCompra'] = $data[0]['complementarios']['carga']['orden_compra'];
        }

        if (isset($data[0]['complementarios']['carga']['orden_venta'])) {
          $data[0]['complementarios']['carga']['ordenVenta'] = $data[0]['complementarios']['carga']['orden_venta'];
        }

        if (isset($data[0]['complementarios']['carga']['numero_asiento'])) {
          $data[0]['complementarios']['carga']['numeroAsiento'] = $data[0]['complementarios']['carga']['numero_asiento'];
        }

        //Sector Energia
        if (isset($data[0]['sector_energia_electrica'])) {
          $data[0]['sectorEnergiaElectrica'] = $data[0]['sector_energia_electrica'];
        }

        if (isset($data[0]['sectorEnergiaElectrica']['numero_medidor'])) {
          $data[0]['sectorEnergiaElectrica']['numeroMedidor'] = $data[0]['sectorEnergiaElectrica']['numero_medidor'];
        }

        if (isset($data[0]['sectorEnergiaElectrica']['codigo_actividad'])) {
          $data[0]['sectorEnergiaElectrica']['codigoActividad'] = $data[0]['sectorEnergiaElectrica']['codigo_actividad'];
        }

        if (isset($data[0]['sectorEnergiaElectrica']['codigo_categoria'])) {
          $data[0]['sectorEnergiaElectrica']['codigoCategoria'] = $data[0]['sectorEnergiaElectrica']['codigo_categoria'];
        }

        if (isset($data[0]['sectorEnergiaElectrica']['lectura_anterior'])) {
          $data[0]['sectorEnergiaElectrica']['lecturaAnterior'] = $data[0]['sectorEnergiaElectrica']['lectura_anterior'];
        }

        if (isset($data[0]['sectorEnergiaElectrica']['lectura_actual'])) {
          $data[0]['sectorEnergiaElectrica']['lecturaActual'] = $data[0]['sectorEnergiaElectrica']['lectura_actual'];
        }

        //Sector Seguros
        if (isset($data[0]['sector_seguros'])) {
          $data[0]['sectorSeguros'] = $data[0]['sector_seguros'];
        }

        if (isset($data[0]['sectorSeguros']['codigo_aseguradora'])) {
          $data[0]['sectorSeguros']['codigoAseguradora'] = $data[0]['sectorSeguros']['codigo_aseguradora'];
        }

        if (isset($data[0]['sectorSeguros']['codigo_poliza'])) {
          $data[0]['sectorSeguros']['codigoPoliza'] = $data[0]['sectorSeguros']['codigo_poliza'];
        }

        if (isset($data[0]['sectorSeguros']['numero_poliza'])) {
          $data[0]['sectorSeguros']['numeroPoliza'] = $data[0]['sectorSeguros']['numero_poliza'];
        }

        if (isset($data[0]['sectorSeguros']['vigencia_unidad'])) {
          $data[0]['sectorSeguros']['vigenciaUnidad'] = $data[0]['sectorSeguros']['vigencia_unidad'];
        }

        if (isset($data[0]['sectorSeguros']['inicio_vigencia'])) {
          $data[0]['sectorSeguros']['inicioVigencia'] = $data[0]['sectorSeguros']['inicio_vigencia'];
        }

        if (isset($data[0]['sectorSeguros']['fin_vigencia'])) {
          $data[0]['sectorSeguros']['finVigencia'] = $data[0]['sectorSeguros']['fin_vigencia'];
        }

        if (isset($data[0]['sectorSeguros']['codigo_interno_item'])) {
          $data[0]['sectorSeguros']['codigoInternoItem'] = $data[0]['sectorSeguros']['codigo_interno_item'];
        }
    }*/

    private function addDefaultValues(&$data) {   

      // Suponiendo que la clase ConstantService tiene un método getTiposDocumentos() que devuelve el array tiposDocumentos
      $ConstantService = new ConstantService();
      $tiposDocumentos = $ConstantService->getTiposDocumentos();

      $success = false;
      foreach ($tiposDocumentos as $um) {
          if ($um['codigo'] === $data[0]['tipoDocumento']) {
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
              'error' => "Tipo de Documento ". $$data[0]['tipoDocumento']." no válido. Los valores válidos son: " . implode(', ', $valoresValidos) . "."
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
