<?php 

class ConstanteService {
    public $tiposDocumentos = [
        [
            'codigo' => 1,
            'descripcion' => 'Factura electrónica',
            'situacion' => 0,
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Factura electrónica de exportación',
            'situacion' => 1, //A futuro
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Factura electrónica de importación',
            'situacion' => 1, //A futuro
        ],
        [
            'codigo' => 4,
            'descripcion' => 'Autofactura electrónica',
            'situacion' => 0,
        ],
        [
            'codigo' => 5,
            'descripcion' => 'Nota de crédito electrónica',
            'situacion' => 0,
        ],
        [
            'codigo' => 6,
            'descripcion' => 'Nota de débito electrónica',
            'situacion' => 0,
        ],
        [
            'codigo' => 7,
            'descripcion' => 'Nota de remisión electrónica',
            'situacion' => 0,
        ],
        [
            'codigo' => 8,
            'descripcion' => 'Comprobante de retención electrónico',
            'situacion' => 1, //A futuro
        ],
    ];

    // Agregar constructor si es necesario
    /*
    public function __construct() {
        // Aquí puedes inicializar propiedades u realizar otras tareas necesarias
    }
    */
}
