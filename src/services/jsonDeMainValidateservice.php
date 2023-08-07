<?php

require_once 'StringUtilService.php';
require_once 'FechaUtilService.php';
require_once 'ConstantsService.php';

class JSonDeMainValidateService {
    
    public $errors;
    
    public function __construct() {
        $this->errors = array();
    }

    public function validateValues($params, $data) {
        
        $this->errors = array();

        $constanteServiceInstance = new ConstantService();
        $tiposDocumentos = $constanteServiceInstance->getTiposDocumentos();
 
        $success = false;
        foreach ($tiposDocumentos as $um) {
            if ($um['codigo'] ===$data['tipoDocumento']) {
                $data['tipoDocumentoDescripcion'] = $um['descripcion'];
                $success = true;
                break;
            }
        }

        if (!$success) {
            // Generando el mensaje de error en formato JSON con los valores válidos del array $tiposDocumentos
            $valoresValidos = array_map(function ($um) {
                return $um['codigo'];
            }, $tiposDocumentos);

            $this->errors[] = "Tipo de Documento$data['tipoDocumento'] no válido. Los valores válidos son: " . implode(', ', $valoresValidos) . ".";        
        }
        
        if (isset($data['cliente'])) {
            if (!isset($data['cliente']['contribuyente'])) {
                $this->errors[] = 'Debe indicar si el Cliente es o no un Contribuyente true|false en data.cliente.contribuyente';
            } else {
                if ($data['cliente']['contribuyente'] !== true && $data['cliente']['contribuyente'] !== false) {
                    $this->errors[] = 'data.cliente.contribuyente debe ser true|false';
                }
            }
        }
        
        $this->generateCodigoControlValidate($params, $data);
        
        $this->datosEmisorValidate($params, $data);
        
        $this->generateDatosOperacionValidate($params, $data);
        
        $this->generateDatosGeneralesValidate($params, $data);
        
        $this->generateDatosEspecificosPorTipoDEValidate($params, $data);
        
        if ($tipoDocumento == 4) {
            $this->generateDatosAutofacturaValidate($params, $data);
        }
        
        if ($tipoDocumento == 1 ||$data['tipoDocumento'] == 4) {
            $this->generateDatosCondicionOperacionDEValidate($params, $data);
        }

        $this->errors = jsonDteItemValidate::generateDatosItemsOperacionValidate($params, $data, $this->errors);
        
        $this->generateDatosComplementariosComercialesDeUsoEspecificosValidate($params, $data);
        
        if ($tipoDocumento == 1 ||$data['tipoDocumento'] == 7) {
            if ($tipoDocumento == 7) {
                if (!isset($data['detalleTransporte'])) {
                                $this->errors[] = 'Debe especificar el detalle de transporte en data.tranporte para el Tipo de Documento = 7';
          } else {
            $this->generateDatosTransporteValidate($params, $data);
          }
        } else {
          if (isset($data['detalleTransporte'])) {
            $this->generateDatosTransporteValidate($params, $data);
          }
        }
      }
  
      if ($tipoDocumento != 7) {
        $this->generateDatosTotalesValidate($params, $data);
      }
  
      if (isset($data['complementarios'])) {
        $this->generateDatosComercialesUsoGeneralValidate($params, $data);
      }
  
      if ($data['moneda'] != 'PYG' && $data['condicionTipoCambio'] == 1) {
        if (!isset($data['cambio'])) {
          $this->errors[] = 'Debe especificar el valor del Cambio en data.cambio cuando moneda != PYG y la Cotización es Global';
        }
      }
  
      if ($tipoDocumento == 4 ||$data['tipoDocumento'] == 5 ||$data['tipoDocumento'] == 6) {
        if (!isset($data['documentoAsociado'])) {
          $this->errors[] = 'Documento asociado es obligatorio para el tipo de documento electrónico (' .$data['tipoDocumento'] . ') seleccionado';
        }
      }
  
      if ($tipoDocumento == 1 ||$data['tipoDocumento'] == 4 ||$data['tipoDocumento'] == 5 ||$data['tipoDocumento'] == 6 ||$data['tipoDocumento'] == 7) {
        if (isset($data['documentoAsociado'])) {
          if (!is_array($data['documentoAsociado'])) {
            $this->generateDatosDocumentoAsociadoValidate($params, $data['documentoAsociado'], $data);
          } else {
            foreach ($data['documentoAsociado'] as $dataDocumentoAsociado) {
              $this->generateDatosDocumentoAsociadoValidate($params, $dataDocumentoAsociado, $data);
            }
          }
        }
      }
  
      if (count($this->errors) > 0) {
        $errorExit = new Exception();
  
        $msgErrorExit = '';
  
        $recorrerHasta = count($this->errors);
        if (($config['errorLimit'] ?? 3) < $recorrerHasta) {
          $recorrerHasta = $config['errorLimit'] ?? 3;
        }
  
        for ($i = 0; $i < $recorrerHasta; $i++) {
          $error = $this->errors[$i];
          $msgErrorExit .= $error;
  
          if ($i < $recorrerHasta - 1) {
            $msgErrorExit .= $config['errorSeparator'] . '';
          }
        }
  
        $errorExit->message = $msgErrorExit;
        throw $errorExit;
      }
    }

    private function generateCodigoControlValidate($params, $data) {
        if (isset($data['cdc']) && strlen($data['cdc']) == 44) {
            // Caso ya se le pase el CDC
            $codigoControl = $data['cdc'];
    
            // Como se va a utilizar el CDC enviado como parámetro, se verificará que todos los datos del XML coincidan con el CDC.
           $tipoDocumentoCDC = substr($codigoControl, 0, 2);
            $establecimientoCDC = substr($codigoControl, 11, 3);
            $puntoCDC = substr($codigoControl, 14, 3);
            $numeroCDC = substr($codigoControl, 17, 7);
            $fechaCDC = substr($codigoControl, 25, 8);
            $tipoEmisionCDC = substr($codigoControl, 33, 1);
    
            if (intval($data['tipoDocumento']) != intval($tipoDocumentoCDC)) {
                $this->errors[] = "El Tipo de Documento '" . $data['tipoDocumento'] . "' en data.tipoDocumento debe coincidir con el CDC re-utilizado (" . intval($tipoDocumentoCDC) . ")";
            }
    
            $establecimiento = StringUtilService::leftZero($data['establecimiento'], 3);
            if ($establecimiento != $establecimientoCDC) {
                $this->errors[] = "El Establecimiento '" . $establecimiento . "' en data.establecimiento debe coincidir con el CDC reutilizado (" . $establecimientoCDC . ")";
            }
    
            $punto = StringUtilService::leftZero($data['punto'], 3);
            if ($punto != $puntoCDC) {
                $this->errors[] = "El Punto '" . $punto . "' en data.punto debe coincidir con el CDC reutilizado (" . $puntoCDC . ")";
            }
    
            $numero = StringUtilService::leftZero($data['numero'], 7);
            if ($numero != $numeroCDC) {
                $this->errors[] = "El Numero de Documento '" . $numero . "' en data.numero debe coincidir con el CDC reutilizado (" . $numeroCDC . ")";
            }
    
            $fecha = substr($data['fecha'], 0, 4) . substr($data['fecha'], 5, 2) . substr($data['fecha'], 8, 2);
            if ($fecha != $fechaCDC) {
                $this->errors[] = "La fecha '" . $fecha . "' en data.fecha debe coincidir con el CDC reutilizado (" . $fechaCDC . ")";
            }
    
            if (intval($data['tipoEmision']) != intval($tipoEmisionCDC)) {
                $this->errors[] = "El Tipo de Emisión '" . $data['tipoEmision'] . "' en data.tipoEmision debe coincidir con el CDC reutilizado (" . intval($tipoEmisionCDC) . ")";
            }
        }
    }

    private function datosEmisorValidate($params, $data) {
        if (strpos($params['ruc'], '-') === false) {
            $this->errors[] = 'RUC debe contener dígito verificador en params.ruc';
        }
        $rucEmisor = explode('-', $params['ruc']);
        $dvEmisor = explode('-', $params['ruc'])[1];
    
        $reg = '/^\d+$/';
        if (!preg_match($reg, $rucEmisor)) {
            $this->errors[] = "La parte que corresponde al RUC '" . $params['ruc'] . "' en params.ruc debe ser numérico";
        }
        if (strlen($rucEmisor) > 8) {
            $this->errors[] = "La parte que corresponde al RUC '" . $params['ruc'] . "' en params.ruc debe contener de 1 a 8 caracteres";
        }
    
        if (!preg_match($reg, $dvEmisor)) {
            $this->errors[] = "La parte que corresponde al DV del RUC '" . $params['ruc'] . "' en params.ruc debe ser numérico";
        }
        if ($dvEmisor > 9) {
            $this->errors[] = "La parte que corresponde al DV del RUC '" . $params['ruc'] . "' en params.ruc debe ser del 1 al 9";
        }
    
        if (strlen($params['timbradoNumero']) !== 8) {
            $this->errors[] = 'Debe especificar un Timbrado de 8 caracteres en params.timbradoNumero';
        }
    
        if (!FechaUtilService::isIsoDate($params['timbradoFecha'])) {
            $this->errors[] = "Valor de la Fecha '" . $params['timbradoFecha'] . "' en params.fecha no válido. Formato: yyyy-MM-dd";
        }
    
        if (isset($params['tipoRegimen'])) {
            $constanteServiceInstance = new ConstantService();
            $tiposRegimenes = $constanteServiceInstance->getTiposRegimenes();
            
            if (count(array_filter($tiposRegimenes, function($um) use ($params) {
                return $um['codigo'] === $params['tipoRegimen'];
            })) === 0) {
                $this->errors[] = "Tipo de Regimen '" . $data['tipoRegimen'] . "' en params.tipoRegimen no válido. Valores: " . implode(', ', array_map(function($a) {
                    return $a['codigo'] . '-' . $a['descripcion'];
                }, $constanteServiceInstance->tiposRegimenes));
            }
        }
    
        if (!isset($params['razonSocial'])) {
            $this->errors[] = 'La razon social del emisor en params.razonSocial no puede ser vacio';
        } else {
            $razonSocialLength = strlen($params['razonSocial']);
            if (!($razonSocialLength >= 4 && $razonSocialLength <= 250)) {
                $this->errors[] = "La razon Social del Emisor '" . $params['razonSocial'] . "' en params.razonSocial debe tener de 4 a 250 caracteres";
            }
        }
    
        if (isset($params['nombreFantasia']) && strlen($params['nombreFantasia']) > 0) {
            $nombreFantasiaLength = strlen($params['nombreFantasia']);
            if (!($nombreFantasiaLength >= 4 && $nombreFantasiaLength <= 250)) {
                $this->errors[] = "El nombre de Fantasia del Emisor '" . $params['nombreFantasia'] . "' en params.nombreFantasia debe tener de 4 a 250 caracteres";
            }
        }
    
        // Aquí hay que verificar los datos de las sucursales
        if (!isset($params['establecimientos']) || !is_array($params['establecimientos'])) {
            $this->errors[] = 'Debe especificar un array de establecimientos en params.establecimientos';
        } else {
            foreach ($params['establecimientos'] as $i => $establecimiento) {
                if (!isset($establecimiento['codigo'])) {
                    $this->errors[] = 'Debe especificar el código del establecimiento en params.establecimientos[' . $i . '].codigo';
                }
    
                if (isset($establecimiento['telefono'])) {
                    $telefonoLength = strlen($establecimiento['telefono']);
                    if (!($telefonoLength >= 6 && $telefonoLength <= 15)) {
                        $this->errors[] = "El valor '" . $establecimiento['telefono'] . "' en params.establecimientos[" . $i . '].telefono debe tener una longitud de 6 a 15 caracteres';
                    } else if (strpos($establecimiento['telefono'], '(') !== false || strpos($establecimiento['telefono'], ')') !== false || strpos($establecimiento['telefono'], '[') !== false || strpos($establecimiento['telefono'], ']') !== false) {
                        /*$this->errors[] = "El valor '" . $establecimiento['telefono'] . "' en params.establecimientos[" . $i . '].telefono no puede contener () o []';
                          Finalmente no da error en la SET por esto */
                    }
                }
            }
        }
    }

    private function generateDatosOperacionValidate($params, $data) {
        $constanteServiceInstance = new ConstantService();
    
        $tiposEmisiones = $constanteServiceInstance->getTiposEmisiones();
        $tipoEmision = $data['tipoEmision'];
    
        $tiposEmisionExists = array_filter($tiposEmisiones, function($um) use ($tipoEmision) {
            return $um['codigo'] === $tipoEmision;
        });
    
        if (count($tiposEmisionExists) === 0) {
            $this->errors[] = "Tipo de Emisión '" . $tipoEmision . "' en data.tipoEmision no válido. Valores: " . implode(', ', array_map(function($a) {
                return $a['codigo'] . '-' . $a['descripcion'];
            }, $tiposEmisiones));
        }
    
        if ($tipoDocumento == 7) {
            // Nota de Remision
            if (!(isset($data['descripcion']) && trim($data['descripcion']) !== '')) {
                // Según dicen en TDE no es obligatorio, entonces se retira la validación.
                // $this->errors[] = 'Debe informar la Descripción en data.descripcion para el Documento Electrónico';
            }
        }
    }

    private function generateDatosGeneralesValidate($params, $data, $config) {
        $this->generateDatosGeneralesInherentesOperacionValidate($params, $data);
    
        $this->generateDatosGeneralesEmisorDEValidate($params, $data);
    
        if ($config->userObjectRemove == false) {
            // Si está TRUE no crea el objeto usuario
            if (isset($data['usuario'])) {
                // No es obligatorio
                $this->generateDatosGeneralesResponsableGeneracionDEValidate($params, $data);
            }
        }
        $this->generateDatosGeneralesReceptorDEValidate($params, $data);
    }

    private function generateDatosEspecificosPorTipoDEValidate($params, $data) {
        if ($tipoDocumento === 1) {
            $this->generateDatosEspecificosPorTipoDE_FacturaElectronicaValidate($params, $data);
        }
        if ($tipoDocumento === 4) {
            $this->generateDatosEspecificosPorTipoDE_AutofacturaValidate($params, $data);
        }
    
        if ($tipoDocumento === 5 ||$data['tipoDocumento'] === 6) {
            $this->generateDatosEspecificosPorTipoDE_NotaCreditoDebitoValidate($params, $data);
        }
    
        if ($tipoDocumento === 7) {
            $this->generateDatosEspecificosPorTipoDE_RemisionElectronicaValidate($params, $data);
        }
    }

    private function generateDatosAutofacturaValidate($params, $data) {
        if (!isset($data['autoFactura'])) {
            $this->errors[] = 'Debe especificar los datos de Autofactura en data.autoFactura para el Tipo de Documento = 4';
            return;
        }
    
        if (!isset($data['autoFactura']['documentoNumero'])) {
            $this->errors[] = 'Debe especificar el Documento del Vendedor para la AutoFactura en data.autoFactura.documentoNumero';
        } else {
            $documentoNumero = $data['autoFactura']['documentoNumero'];
            if (!(strlen($documentoNumero) >= 1 && strlen($documentoNumero) <= 20)) {
                $this->errors[] = 'El Numero de Documento del Vendedor en data.autoFactura.numeroDocuemnto debe contener entre 1 y 20 caracteres ';
            }
    
            if (preg_match('/[a-zA-Z]/', $documentoNumero) || preg_match('/\./', $documentoNumero)) {
                $this->errors[] = 'El Numero de Documento del Vendedor "' . $documentoNumero . '" en data.autoFactura.numeroDocuemnto no puede contener Letras ni puntos';
            }
        }
    
        if (!isset($data['documentoAsociado'])) {
            $this->errors[] = 'Debe indicar el Documento Asociado en data.documentoAsociado para el Tipo de Documento = 4';
        } else {
            if (is_array($data['documentoAsociado'])) {
                $this->validateAsociadoConstancia($params, $data['documentoAsociado'], true);
            } else {
                $this->validateAsociadoConstancia($params, $data['documentoAsociado'], false);
            }
    
            if ($data['cliente']['contribuyente'] == false) {
                $this->errors[] = 'El Cliente de una Autofactura debe ser Contribuyente en data.cliente.contribuyente';
            }
        }
    }

    private function generateDatosCondicionOperacionDEValidate($params, $data) {
        $items = $data['items'];
        $sumaSubtotales = 0;
    
        if (true) {  // No estoy seguro del propósito de esta condición, reemplázala con tu lógica
            if (!isset($data['condicion'])) {
                $this->errors[] = 'Debe indicar los datos de la Condición de la Operación en data.condicion';
                return; // salir del método
            } else {
                $tipoCondicion = $data['condicion']['tipo'];
                $condicionesOperaciones = $constanteService->getCondicionesOperaciones();
                
                $condicionEncontrada = false;
                foreach ($condicionesOperaciones as $condicion) {
                    if ($condicion['codigo'] === $tipoCondicion) {
                        $condicionEncontrada = true;
                        break;
                    }
                }
                
                if (!$condicionEncontrada) {
                    $this->errors[] = "Condición de la Operación '" . $tipoCondicion . "' en data.condicion.tipo no encontrado. Valores: " . implode(', ', array_map(function($a) {
                        return $a['codigo'] . '-' . $a['descripcion'];
                    }, $condicionesOperaciones));
                }
    
                $this->generateDatosCondicionOperacionDE_ContadoValidate($params, $data);
    
                if ($data['condicion']['tipo'] === 2) {
                    $this->generateDatosCondicionOperacionDE_CreditoValidate($params, $data);
                }
            }
        }
    }

    public function generateDatosComplementariosComercialesDeUsoEspecificosValidate($params, $data) {
        if (isset($data['sectorEnergiaElectrica'])) {
            $this->generateDatosSectorEnergiaElectricaValidate($params, $data);
        }
    
        if (isset($data['sectorSeguros'])) {
            $this->generateDatosSectorSegurosValidate($params, $data);
        }
    
        if (isset($data['sectorSupermercados'])) {
            $this->generateDatosSectorSupermercadosValidate($params, $data);
        }
    
        if (isset($data['sectorAdicional'])) {
            $this->generateDatosDatosAdicionalesUsoComercialValidate($params, $data);
        }
    }

    private function generateDatosTransporteValidate($params, $data) {
        if ($tipoDocumento == 7) {
            if (!isset($data['detalleTransporte']) || !isset($data['detalleTransporte']['tipo']) || $data['detalleTransporte']['tipo'] <= 0) {
                $this->errors[] = 'Obligatorio informar transporte.tipo';
            }
        }
        if (isset($data['detalleTransporte']) && isset($data['detalleTransporte']['condicionNegociacion'])) {
            if (!in_array($data['detalleTransporte']['condicionNegociacion'], constanteService::condicionesNegociaciones)) {
                $this->errors[] = 'detalleTransporte.condicionNegociación (' . $data['detalleTransporte']['condicionNegociacion'] . ') no válido';
            }
        }
        if ($tipoDocumento == 7) {
            if (!isset($data['detalleTransporte']['inicioEstimadoTranslado'])) {
                $this->errors[] = 'Obligatorio informar data.transporte.inicioEstimadoTranslado. Formato yyyy-MM-dd';
            } else {
                if (!FechaUtilService::isIsoDate($data['detalleTransporte']['inicioEstimadoTranslado'])) {
                    $this->errors[] = "Valor de la Fecha '" . $data['detalleTransporte']['inicioEstimadoTranslado'] . "' en data.transporte.inicioEstimadoTranslado no válido. Formato: yyyy-MM-dd";
                }
            }
        }
        if ($tipoDocumento == 7) {
            if (!isset($data['detalleTransporte']['finEstimadoTranslado'])) {
                $this->errors[] = 'Obligatorio informar data.transporte.finEstimadoTranslado. Formato yyyy-MM-dd';
            } else {
                if (!FechaUtilService::isIsoDate($data['detalleTransporte']['finEstimadoTranslado'])) {
                    $this->errors[] = "Valor de la Fecha '" . $data['detalleTransporte']['finEstimadoTranslado'] . "' en data.transporte.finEstimadoTranslado no válido. Formato: yyyy-MM-dd";
                }
            }
        }
    
        if ($tipoDocumento == 7 && isset($data['detalleTransporte']['inicioEstimadoTranslado']) && isset($data['detalleTransporte']['finEstimadoTranslado'])) {
            $fechaInicio = new DateTime($data['detalleTransporte']['inicioEstimadoTranslado']);
            $fechaFin = new DateTime($data['detalleTransporte']['finEstimadoTranslado']);
            $fechaHoy = new DateTime((new DateTime())->format('Y-m-d'));
    
            $fechaHoy->setTime(0, 0, 0, 0);
        }
    
        if (!in_array($data['detalleTransporte']['tipo'], array_column(constanteService::tiposTransportes, 'codigo'))) {
            $this->errors[] = "Tipo de Transporte '" . $data['detalleTransporte']['tipo'] . "' en data.transporte.tipo no encontrado. Valores: " . implode(', ', array_map(function ($a) {
                return $a['codigo'] . '-' . $a['descripcion'];
            }, constanteService::tiposTransportes));
        }
        if (!in_array($data['detalleTransporte']['modalidad'], array_column(constanteService::modalidadesTransportes, 'codigo'))) {
            $this->errors[] = "Modalidad de Transporte '" . $data['detalleTransporte']['modalidad'] . "' en data.transporte.modalidad no encontrado. Valores: " . implode(', ', array_map(function ($a) {
                return $a['codigo'] . '-' . $a['descripcion'];
            }, constanteService::modalidadesTransportes));
        }
    
        /*if (!in_array($data['detalleTransporte']['condicionNegociacion'], array_column(constanteService::condicionesNegociaciones, 'codigo'))) {
            $this->errors[] = "Condición de Negociación '" . $data['detalleTransporte']['condicionNegociacion'] . "' en data.transporte.condicionNegociacion no encontrado. Valores: " . implode(', ', array_map(function ($a) {
                return $a['codigo'] . '-' . $a['descripcion'];
            }, constanteService::condicionesNegociaciones));
        }*/
    
        if (isset($data['detalleTransporte']['salida'])) {
            $this->generateDatosSalidaValidate($params, $data);
        }
        if (isset($data['detalleTransporte']['entrega'])) {
            $this->generateDatosEntregaValidate($params, $data);
        }
        if (isset($data['detalleTransporte']['vehiculo'])) {
            $this->generateDatosVehiculoValidate($params, $data);
        }
        if (isset($data['detalleTransporte']['transportista'])) {
            $this->generateDatosTransportistaValidate($params, $data);
        }
    }

    public function generateDatosTotalesValidate($params, $data, $config) {
        /*$temporalTotal = jsonDteTotales.generateDatosTotales($params, $data, $data['items'], $config);
        echo "temporalTotal: " . json_encode($temporalTotal);
    
        if ($data['descuentoGlobal'] > 0) {
            echo "temporalTotal: " . $data['descuentoGlobal'];
        }*/
    
        if ($data['moneda'] != 'PYG' && $data['condicionTipoCambio'] == 1) {
            if (!isset($data['cambio'])) {
                $this->errors[] = 'Debe especificar el valor del Cambio en data.cambio cuando moneda != PYG y la Cotización es Global';
            }
        }
    
        if ($data['moneda'] == 'PYG') {
            if (isset($data['descuentoGlobal']) && intval($data['descuentoGlobal']) != $data['descuentoGlobal']) {
                $this->errors[] = 'El Descuento Global "' . $data['descuentoGlobal'] . '" en "PYG" en data.descuentoGlobal, no puede contener decimales';
            }
        } else {
            if (isset($data['descuentoGlobal']) && strlen(explode('.', $data['descuentoGlobal'])[1]) > 8) {
                $this->errors[] = 'El Descuento Global "' . $data['descuentoGlobal'] . '" en data.descuentoGlobal, no puede contener más de 8 decimales';
            }
        }
    
        if ($data['moneda'] == 'PYG') {
            if (isset($data['anticipoGlobal']) && intval($data['anticipoGlobal']) != $data['anticipoGlobal']) {
                $this->errors[] = 'El Anticipo Global "' . $data['anticipoGlobal'] . '" en "PYG" en data.anticipoGlobal, no puede contener decimales';
            }
        } else {
            if (isset($data['anticipoGlobal']) && strlen(explode('.', $data['anticipoGlobal'])[1]) > 8) {
                $this->errors[] = 'El Anticipo Global "' . $data['anticipoGlobal'] . '" en data.anticipoGlobal, no puede contener más de 8 decimales';
            }
        }
    }

    public function generateDatosComercialesUsoGeneralValidate($params, $data) {
        $jsonResult = array(
            //'dOrdCompra' => $data['complementarios']['ordenCompra'],
            //'dOrdVta' => $data['complementarios']['ordenVenta'],
            //'dAsiento' => $data['complementarios']['numeroAsiento']
        );
    
        if ($tipoDocumento == 1 ||$data['tipoDocumento'] == 7) {
            // Opcional si 1 o 7
            if (
                (isset($data['complementarios']['carga']['volumenTotal']) ||
                isset($data['complementarios']['carga']['pesoTotal']))
            ) {
                $this->generateDatosCargaValidate($params, $data);
            }
        }
    }

    public function generateDatosDocumentoAsociadoValidate($params, $dataDocumentoAsociado, $data) {
        if ($data['tipoTransaccion'] == 11 && !isset($dataDocumentoAsociado['resolucionCreditoFiscal'])) {
            $this->errors[] = 'Obligatorio informar data.documentoAsociado.resolucionCreditoFiscal';
        }
    
        // Validaciones
        if (!in_array($dataDocumentoAsociado['formato'], array_column(constanteService::tiposDocumentosAsociados, 'codigo'))) {
            $this->errors[] =
                "Formato de Documento Asociado '" .
                $dataDocumentoAsociado['formato'] .
                "' en data.documentoAsociado.formato no encontrado. Valores: " .
                implode(', ', array_map(function ($a) {
                    return $a['codigo'] . '-' . $a['descripcion'];
                }, constanteService::tiposDocumentosAsociados));
        }
    
        if ($dataDocumentoAsociado['tipo'] == 2) {
            if (!in_array($dataDocumentoAsociado['tipoDocumentoImpreso'], array_column(constanteService::tiposDocumentosImpresos, 'codigo'))) {
                $this->errors[] =
                    "Tipo de Documento impreso '" .
                    $dataDocumentoAsociado['tipoDocumentoImpreso'] .
                    "' en data.documentoAsociado.tipoDocumentoImpreso no encontrado. Valores: " .
                    implode(', ', array_map(function ($a) {
                        return $a['codigo'] . '-' . $a['descripcion'];
                    }, constanteService::tiposDocumentosImpresos));
            }
        }
    
        if ($dataDocumentoAsociado['formato'] == 1) {
            // H002 = Electronico
            if (!isset($dataDocumentoAsociado['cdc']) || strlen($dataDocumentoAsociado['cdc']) < 44) {
                $this->errors[] = 'Debe indicar el CDC asociado en data.documentoAsociado.cdc';
            }
        }
    
        if ($dataDocumentoAsociado['formato'] == 2) {
            // H002 = Impreso
            if (!isset($dataDocumentoAsociado['timbrado'])) {
                $this->errors[] =
                    'Debe especificar el Timbrado del Documento impreso Asociado en data.documentoAsociado.timbrado';
            }
            if (!isset($dataDocumentoAsociado['establecimiento'])) {
                $this->errors[] =
                    'Debe especificar el Establecimiento del Documento impreso Asociado en data.documentoAsociado.establecimiento';
            }
            if (!isset($dataDocumentoAsociado['punto'])) {
                $this->errors[] = 'Debe especificar el Punto del Documento impreso Asociado en data.documentoAsociado.punto';
            }
            if (!isset($dataDocumentoAsociado['numero'])) {
                $this->errors[] = 'Debe especificar el Número del Documento impreso Asociado en data.documentoAsociado.numero';
            }
            if (!isset($dataDocumentoAsociado['tipoDocumentoImpreso'])) {
                $this->errors[] =
                    'Debe especificar el Tipo del Documento Impreso Asociado en data.documentoAsociado.tipoDocumentoImpreso';
            }
            if (isset($dataDocumentoAsociado['fecha'])) {
                if (strlen($dataDocumentoAsociado['fecha']) !== 10) {
                    $this->errors[] =
                        'La Fecha del Documento impreso Asociado en data.documentoAsociado.fecha debe tener una longitud de 10 caracteres';
                }
            } else {
                $this->errors[] = 'Debe especificar la Fecha del Documento impreso Asociado en data.documentoAsociado.fecha';
            }
        }
    
        if ($dataDocumentoAsociado['formato'] == 3) {
            // H002 = Constancia electronica
            if (!isset($dataDocumentoAsociado['constanciaTipo'])) {
                $this->errors[] = 'Debe especificar el Tipo de Constancia data.documentoAsociado.constanciaTipo';
            } else {
                if (!in_array($dataDocumentoAsociado['constanciaTipo'], array_column(constanteService::tiposConstancias, 'codigo'))) {
                    $this->errors[] =
                        "Tipo de Constancia '" .
                        $dataDocumentoAsociado['constanciaTipo'] .
                        "' en data.documentoAsociado.constanciaTipo no encontrado. Valores: " .
                        implode(', ', array_map(function ($a) {
                            return $a['codigo'] . '-' . $a['descripcion'];
                        }, constanteService::tiposConstancias));
                }
            }
        }
    }
    
    private function generateDatosGeneralesInherentesOperacionValidate($params, $data) {
        if ($tipoDocumento == 7) {
            // C002
            return; // No informa si el tipo de documento es 7
        }
    
        if (!fechaUtilService::isIsoDateTime($data['fecha'])) {
            $this->errors[] = "Valor de la Fecha '" . $data['fecha'] . "' en data.fecha no válido. Formato: yyyy-MM-ddTHH:mm:ss";
        }
    
        if (!isset($data['tipoImpuesto'])) {
            $this->errors[] = 'Debe especificar el Tipo de Impuesto en data.tipoImpuesto';
        } else {
            $tipoImpuestoExists = array_filter(constanteService::tiposImpuestos, function ($um) use ($data) {
                return $um['codigo'] === (int)$data['tipoImpuesto'];
            });
            if (empty($tipoImpuestoExists)) {
                $this->errors[] =
                    "Tipo de Impuesto '" .
                    $data['tipoImpuesto'] .
                    "' en data.tipoImpuesto no válido. Valores: " .
                    implode(', ', array_map(function ($a) {
                        return $a['codigo'] . '-' . $a['descripcion'];
                    }, constanteService::tiposImpuestos));
            }
        }
    
        $moneda = $data['moneda'] ?? 'PYG';
    
        $monedaExists = array_filter(constanteService::monedas, function ($um) use ($moneda) {
            return $um['codigo'] === $moneda;
        });
        if (empty($monedaExists)) {
            $this->errors[] =
                "Moneda '" .
                $moneda .
                "' en data.moneda no válido. Valores: " .
                implode(', ', array_map(function ($a) {
                    return $a['codigo'] . '-' . $a['descripcion'];
                }, constanteService::monedas));
        }
    
        if (isset($data['condicionAnticipo'])) {
            $condicionAnticipoExists = array_filter(constanteService::globalPorItem, function ($um) use ($data) {
                return $um['codigo'] === $data['condicionAnticipo'];
            });
            if (empty($condicionAnticipoExists)) {
                $this->errors[] =
                    "Condición de Anticipo '" .
                    $data['condicionAnticipo'] .
                    "' en data.condicionAnticipo no válido. Valores: " .
                    implode(', ', array_map(function ($a) {
                        return $a['codigo'] . '-Anticipo ' . $a['descripcion'];
                    }, constanteService::globalPorItem));
            }
        } else {
            // condicionAnticipo - si no tiene condicion anticipo, pero tipo transaccion es 9, que de un error.
        }
    
        $tipoTransaccionExists = array_filter(constanteService::tiposTransacciones, function ($um) use ($data) {
            return $um['codigo'] === $data['tipoTransaccion'];
        });
        if (empty($tipoTransaccionExists)) {
            $this->errors[] =
                "Tipo de Transacción '" .
                $data['tipoTransaccion'] .
                "' en data.tipoTransaccion no válido. Valores: " .
                implode(', ', array_map(function ($a) {
                    return $a['codigo'] . '-' . $a['descripcion'];
                }, constanteService::tiposTransacciones));
        }
    
        if ($tipoDocumento == 1 ||$data['tipoDocumento'] == 4) {
            // Obligatorio informar iTipTra D011
            if (!isset($data['tipoTransaccion'])) {
                $this->errors[] = 'Debe proveer el Tipo de Transacción en data.tipoTransaccion';
            }
        }
    
        if ($moneda != 'PYG') {
            if (!isset($data['condicionTipoCambio'])) {
                $this->errors[] = 'Debe informar el tipo de Cambio en data.condicionTipoCambio';
            }
        }
    
        if ($data['condicionTipoCambio'] == 1 && $moneda != 'PYG') {
            if (!(isset($data['cambio']) && $data['cambio'] > 0)) {
                $this->errors[] = 'Debe informar el valor del Cambio en data.cambio';
            }
        }
    }

    private function generateDatosGeneralesEmisorDEValidate($params, $data) {
        $regExpOnlyNumber = '/^\d+$/';
    
        if (!isset($params['establecimientos'])) {
            $this->errors[] = 'Debe proveer un Array con la información de los establecimientos en params';
        }
    
        // Validar si el establecimiento viene en params
        $establecimiento = stringUtilService::leftZero($data['establecimiento'], 3);
    
        $establecimientoExists = array_filter($params['establecimientos'], function ($um) use ($establecimiento) {
            return $um['codigo'] === $establecimiento;
        });
    
        if (empty($establecimientoExists)) {
            $this->errors[] =
                "Establecimiento '" .
                $establecimiento .
                "' no encontrado en params.establecimientos*.codigo. Valores: " .
                implode(', ', array_map(function ($a) {
                    return $a['codigo'] . '-' . $a['denominacion'];
                }, $params['establecimientos']));
        }
    
        /*if (strpos($params['ruc'], '-') === false) { // Removido temporalmente, al parecer no hace falta
            $this->errors[] = 'RUC debe contener dígito verificador en params.ruc';
        }*/
    
        if (!isset($params['actividadesEconomicas']) || count($params['actividadesEconomicas']) === 0) {
            $this->errors[] = 'Debe proveer el array de actividades económicas en params.actividadesEconomicas';
        }
    
        // Validacion de algunos datos de la sucursal
        $establecimientoUsado = array_filter($params['establecimientos'], function ($e) use ($establecimiento) {
            return $e['codigo'] === $establecimiento;
        });
    
        if (!$establecimientoUsado) {
            $this->errors[] =
                'Debe especificar los datos del Establecimiento "' . $establecimiento . '" en params.establecimientos*';
        } else {
            if (!isset($establecimientoUsado['ciudad'])) {
                $this->errors[] = 'Debe proveer la Ciudad del establecimiento en params.establecimientos*.ciudad';
            }
            if (!isset($establecimientoUsado['distrito'])) {
                $this->errors[] = 'Debe proveer la Distrito del establecimiento en params.establecimientos*.distrito';
            }
            if (!isset($establecimientoUsado['departamento'])) {
                $this->errors[] = 'Debe proveer la Departamento del establecimiento en params.establecimientos*.departamento';
            }
    
            constanteService::validateDepartamentoDistritoCiudad(
                'params.establecimientos*',
                +$establecimientoUsado['departamento'],
                +$establecimientoUsado['distrito'],
                +$establecimientoUsado['ciudad'],
                $this->errors
            );
    
            if (isset($establecimientoUsado['numeroCasa'])) {
                if (!preg_match($regExpOnlyNumber, $establecimientoUsado['numeroCasa'])) {
                    $this->errors[] = 'El Número de Casa en params.establecimientos*.numeroCasa debe ser numérico';
                }
            }
        }
    }

    private function generateDatosGeneralesResponsableGeneracionDEValidate($params, $data) {
        $tiposDocumentosIdentidades = $constanteService->tiposDocumentosIdentidades;
    
        $documentoTipoExists = array_filter($tiposDocumentosIdentidades, function ($um) use ($data) {
            return $um['codigo'] === (int)$data['usuario']['documentoTipo'];
        });
    
        if (empty($documentoTipoExists)) {
            $this->errors[] =
                "Tipo de Documento '" .
                $data['usuario']['documentoTipo'] .
                "' no encontrado en data.usuario.documentoTipo. Valores: " .
                implode(', ', array_map(function ($a) {
                    return $a['codigo'] . '-' . $a['descripcion'];
                }, $tiposDocumentosIdentidades));
        }
    
        if (empty($data['usuario']['documentoNumero'])) {
            $this->errors[] = 'El Documento del Responsable en data.usuario.documentoNumero no puede ser vacio';
        }
    
        if (empty($data['usuario']['nombre'])) {
            $this->errors[] = 'El Nombre del Responsable en data.usuario.nombre no puede ser vacio';
        }
    
        if (empty($data['usuario']['cargo'])) {
            $this->errors[] = 'El Cargo del Responsable en data.usuario.cargo no puede ser vacio';
        }
    }
    
    
    
    
    
    

    
    
    
    
    
    
    
    
    
  }
  ?>