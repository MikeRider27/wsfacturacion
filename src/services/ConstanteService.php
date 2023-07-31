<?php 

class ConstanteService {

    //Constantes de la aplicación
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

    /*public $tiposEventos = [
        [
            'codigo' => 1,
            'descripcion' => 'Cancelación',
            'situacion' => 0,
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Inutilización',
            'situacion' => 1, //A futuro
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Endoso',
            'situacion' => 1, //A futuro
        ],
        [
            'codigo' => 10,
            'descripcion' => 'Acuse del DE',
            'situacion' => 0,
        ],
        [
            'codigo' => 11,
            'descripcion' => 'Conformidad del DE',
            'situacion' => 0,
        ],
        [
            'codigo' => 12,
            'descripcion' => 'Disconformidad del DE',
            'situacion' => 0,
        ],
        [
            'codigo' => 13,
            'descripcion' => 'Desconocimiento del DE',
            'situacion' => 0,
        ],
    ];*/

    public $tiposEmisiones = [
        [
            'codigo' => 1,
            'descripcion' => 'Normal',
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Contingencia',
        ],
    ];    

    public $tiposTransacciones = [
        [
            'codigo' => 1,
            'descripcion' => 'Venta de mercadería',
            'situacion' => 0,
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Prestación de servicios',
            'situacion' => 1,
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Mixto (Venta de mercadería y servicios)',
            'situacion' => 1,
        ],
        [
            'codigo' => 4,
            'descripcion' => 'Venta de activo fijo',
            'situacion' => 0,
        ],
        [
            'codigo' => 5,
            'descripcion' => 'Venta de divisas',
            'situacion' => 0,
        ],
        [
            'codigo' => 6,
            'descripcion' => 'Compra de divisas',
            'situacion' => 0,
        ],
        [
            'codigo' => 7,
            'descripcion' => 'Promoción o entrega de muestras',
            'situacion' => 0,
        ],
        [
            'codigo' => 8,
            'descripcion' => 'Donación',
            'situacion' => 1,
        ],
        [
            'codigo' => 9,
            'descripcion' => 'Anticipo',
            'situacion' => 1,
        ],
        [
            'codigo' => 10,
            'descripcion' => 'Compra de productos',
            'situacion' => 1,
        ],
        [
            'codigo' => 11,
            'descripcion' => 'Compra de servicios',
            'situacion' => 1,
        ],
        [
            'codigo' => 12,
            'descripcion' => 'Venta de crédito fiscal',
            'situacion' => 1,
        ],
        [
            'codigo' => 13,
            'descripcion' => 'Muestras médicas (Art. 3 RG 24/2014)',
            'situacion' => 1,
        ],
    ];

    public $tiposImpuestos = [
        [
            'codigo' => 1,
            'descripcion' => 'IVA',
            'situacion' => 0,
        ],
        [
            'codigo' => 2,
            'descripcion' => 'ISC',
            'situacion' => 1,
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Renta',
            'situacion' => 1,
        ],
        [
            'codigo' => 4,
            'descripcion' => 'Ninguno',
            'situacion' => 0,
        ],
        [
            'codigo' => 5,
            'descripcion' => 'IVA - Renta',
            'situacion' => 0,
        ],
    ];   

    public $monedas = [
        [ 'codigo' => 'AED', 'descripcion' => 'Dirham' ],
        [ 'codigo' => 'AFN', 'descripcion' => 'Afghani' ],
        [ 'codigo' => 'ALL', 'descripcion' => 'Lek' ],
        [ 'codigo' => 'AMD', 'descripcion' => 'Dram' ],
        [ 'codigo' => 'ANG', 'descripcion' => 'Netherlands Antillian Guilder' ],
        [ 'codigo' => 'AOA', 'descripcion' => 'Kwanza' ],
        [ 'codigo' => 'ARS', 'descripcion' => 'Argentine Peso' ],
        [ 'codigo' => 'AUD', 'descripcion' => 'Australian Dollar' ],
        [ 'codigo' => 'AWG', 'descripcion' => 'Aruban Guilder' ],
        [ 'codigo' => 'AZM', 'descripcion' => 'Azerbaijanian Manat' ],
        [ 'codigo' => 'BAM', 'descripcion' => 'Convertible Mark' ],
        [ 'codigo' => 'BBD', 'descripcion' => 'Barbados Dollar' ],
        [ 'codigo' => 'BYN', 'descripcion' => 'Belarusian Ruble' ],
        [ 'codigo' => 'BDT', 'descripcion' => 'Taka' ],
        [ 'codigo' => 'BGN', 'descripcion' => 'Bulgarian Lev' ],
        [ 'codigo' => 'BHD', 'descripcion' => 'Bahraini Dinar' ],
        [ 'codigo' => 'BIF', 'descripcion' => 'Burundi Franc' ],
        [ 'codigo' => 'BMD', 'descripcion' => 'Bermudian Dollar (customarily: Bermuda Dollar)' ],
        [ 'codigo' => 'BND', 'descripcion' => 'Brunei Dollar' ],
        [ 'codigo' => 'BOB', 'descripcion' => 'Boliviano' ],
        [ 'codigo' => 'BOV', 'descripcion' => 'Mvdol' ],
        [ 'codigo' => 'BRL', 'descripcion' => 'Brazilian Real' ],
        [ 'codigo' => 'BSD', 'descripcion' => 'Bahamian Dollar' ],
        [ 'codigo' => 'BTN', 'descripcion' => 'Ngultrum' ],
        [ 'codigo' => 'BWP', 'descripcion' => 'Pula' ],
        [ 'codigo' => 'BYR', 'descripcion' => 'Belarussian Ruble' ],
        [ 'codigo' => 'BZD', 'descripcion' => 'Belize Dollar' ],
        [ 'codigo' => 'CAD', 'descripcion' => 'Canadian Dollar' ],
        [ 'codigo' => 'CDF', 'descripcion' => 'Franc Congolais' ],
        [ 'codigo' => 'CHF', 'descripcion' => 'Swiss Franc' ],
        [ 'codigo' => 'CHE', 'descripcion' => 'WIR Euro' ],
        [ 'codigo' => 'CHW', 'descripcion' => 'WIR Franc' ],
        [ 'codigo' => 'CLP', 'descripcion' => 'Chilean Peso' ],
        [ 'codigo' => 'CLF', 'descripcion' => 'Unidad de Fomento' ],
        [ 'codigo' => 'CNY', 'descripcion' => 'Yuan Renminbi' ],
        [ 'codigo' => 'COP', 'descripcion' => 'Colombian Peso' ],
        [ 'codigo' => 'COU', 'descripcion' => 'Unidad de Valor Real' ],
        [ 'codigo' => 'CRC', 'descripcion' => 'Costa Rican Colon' ],
        [ 'codigo' => 'CUP', 'descripcion' => 'Cuban Peso' ],
        [ 'codigo' => 'CUC', 'descripcion' => 'Peso Convertible' ],
        [ 'codigo' => 'CVE', 'descripcion' => 'Cape Verde Escudo' ],
        [ 'codigo' => 'CYP', 'descripcion' => 'Cyprus Pound' ],
        [ 'codigo' => 'CZK', 'descripcion' => 'Czech Koruna' ],
        [ 'codigo' => 'DJF', 'descripcion' => 'Djibouti Franc' ],
        [ 'codigo' => 'DKK', 'descripcion' => 'Danish Krone' ],
        [ 'codigo' => 'DOP', 'descripcion' => 'Dominican Peso' ],
        [ 'codigo' => 'DZD', 'descripcion' => 'Algerian Dinar' ],
        [ 'codigo' => 'EEK', 'descripcion' => 'Kroon' ],
        [ 'codigo' => 'EGP', 'descripcion' => 'Egyptian Pound' ],
        [ 'codigo' => 'ERN', 'descripcion' => 'Nakfa' ],
        [ 'codigo' => 'ETB', 'descripcion' => 'Ethopian Birr' ],
        [ 'codigo' => 'EUR', 'descripcion' => 'Euro' ],
        [ 'codigo' => 'FJD', 'descripcion' => 'Fiji Dollar' ],
        [ 'codigo' => 'FKP', 'descripcion' => 'Falkland Islands Pound' ],
        [ 'codigo' => 'GBP', 'descripcion' => 'Pound Sterling' ],
        [ 'codigo' => 'GEL', 'descripcion' => 'Lari' ],
        [ 'codigo' => 'GHS', 'descripcion' => 'Ghana Cedi' ],
        [ 'codigo' => 'GHC', 'descripcion' => 'Cedi' ],
        [ 'codigo' => 'GIP', 'descripcion' => 'Gibraltar Pound' ],
        [ 'codigo' => 'GMD', 'descripcion' => 'Dalasi' ],
        [ 'codigo' => 'GNF', 'descripcion' => 'Guinea Franc' ],
        [ 'codigo' => 'GTQ', 'descripcion' => 'Quetzal' ],
        [ 'codigo' => 'GYD', 'descripcion' => 'Guyana Dollar' ],
        [ 'codigo' => 'HKD', 'descripcion' => 'Honk Kong Dollar' ],
        [ 'codigo' => 'HNL', 'descripcion' => 'Lempira' ],
        [ 'codigo' => 'HRK', 'descripcion' => 'Kuna' ],
        [ 'codigo' => 'HTG', 'descripcion' => 'Gourde' ],
        [ 'codigo' => 'HUF', 'descripcion' => 'Forint' ],
        [ 'codigo' => 'IDR', 'descripcion' => 'Rupiah' ],
        [ 'codigo' => 'ILS', 'descripcion' => 'New Israeli Sheqel' ],
        [ 'codigo' => 'INR', 'descripcion' => 'Indian Rupee' ],
        [ 'codigo' => 'IQD', 'descripcion' => 'Iraqi Dinar' ],
        [ 'codigo' => 'IRR', 'descripcion' => 'Iranian Rial' ],
        [ 'codigo' => 'ISK', 'descripcion' => 'Iceland Krona' ],
        [ 'codigo' => 'JMD', 'descripcion' => 'Jamaican Dollar' ],
        [ 'codigo' => 'JOD', 'descripcion' => 'Jordanian Dinar' ],
        [ 'codigo' => 'JPY', 'descripcion' => 'Yen' ],
        [ 'codigo' => 'KES', 'descripcion' => 'Kenyan Shilling' ],
        [ 'codigo' => 'KGS', 'descripcion' => 'Som' ],
        [ 'codigo' => 'KHR', 'descripcion' => 'Riel' ],
        [ 'codigo' => 'KMF', 'descripcion' => 'Comoro Franc' ],
        [ 'codigo' => 'KPW', 'descripcion' => 'North Korean Won' ],
        [ 'codigo' => 'KRW', 'descripcion' => 'Won' ],
        [ 'codigo' => 'KWD', 'descripcion' => 'Kuwaiti Dinar' ],
        [ 'codigo' => 'KYD', 'descripcion' => 'Cayman Islands Dollar' ],
        [ 'codigo' => 'KZT', 'descripcion' => 'Tenge' ],
        [ 'codigo' => 'LAK', 'descripcion' => 'Kip' ],
        [ 'codigo' => 'LBP', 'descripcion' => 'Lebanese Pound' ],
        [ 'codigo' => 'LKR', 'descripcion' => 'Sri Lanka Rupee' ],
        [ 'codigo' => 'LRD', 'descripcion' => 'Liberian Dollar' ],
        [ 'codigo' => 'LSL', 'descripcion' => 'Loti' ],
        [ 'codigo' => 'LTL', 'descripcion' => 'Lithuanian Litas' ],
        [ 'codigo' => 'LVL', 'descripcion' => 'Latvian Lats' ],
        [ 'codigo' => 'LYD', 'descripcion' => 'Libyan Dinar' ],
        [ 'codigo' => 'MAD', 'descripcion' => 'Morrocan Dirham' ],
        [ 'codigo' => 'MZN', 'descripcion' => 'Mozambique Metical' ],
        [ 'codigo' => 'MDL', 'descripcion' => 'Moldovan Leu' ],
        [ 'codigo' => 'MGF', 'descripcion' => 'Malagasy Franc' ],
        [ 'codigo' => 'MKD', 'descripcion' => 'Denar' ],
        [ 'codigo' => 'MGA', 'descripcion' => 'Malagasy Ariary' ],
        [ 'codigo' => 'MMK', 'descripcion' => 'Kyat' ],
        [ 'codigo' => 'MNT', 'descripcion' => 'Tugrik' ],
        [ 'codigo' => 'MOP', 'descripcion' => 'Pataca' ],
        [ 'codigo' => 'MRO', 'descripcion' => 'Ouguiya' ],
        [ 'codigo' => 'MTL', 'descripcion' => 'Maltese Lira' ],
        [ 'codigo' => 'MUR', 'descripcion' => 'Mauritius Rupee' ],
        [ 'codigo' => 'XUA', 'descripcion' => 'ADB Unit of Account' ],
        [ 'codigo' => 'MVR', 'descripcion' => 'Rufiyaa' ],
        [ 'codigo' => 'MRU', 'descripcion' => 'Ouguiya' ],
        [ 'codigo' => 'MWK', 'descripcion' => 'Kwacha' ],
        [ 'codigo' => 'MXN', 'descripcion' => 'Mexican Peso' ],
        [ 'codigo' => 'MXV', 'descripcion' => 'Mexican Unidad de Inversion' ],
        [ 'codigo' => 'MYR', 'descripcion' => 'Malaysian Ringgit' ],
        [ 'codigo' => 'MZM', 'descripcion' => 'Metical' ],
        [ 'codigo' => 'NAD', 'descripcion' => 'Namibia Dollar' ],
        [ 'codigo' => 'NGN', 'descripcion' => 'Naira' ],
        [ 'codigo' => 'NIO', 'descripcion' => 'Cordoba Oro' ],
        [ 'codigo' => 'NOK', 'descripcion' => 'Norwegian Krone' ],
        [ 'codigo' => 'NPR', 'descripcion' => 'Nepalese Rupee' ],
        [ 'codigo' => 'NZD', 'descripcion' => 'New Zealand Dollar' ],
        [ 'codigo' => 'OMR', 'descripcion' => 'Rial Omani' ],
        [ 'codigo' => 'PAB', 'descripcion' => 'Balboa' ],
        [ 'codigo' => 'PEN', 'descripcion' => 'Nuevo Sol' ],
        [ 'codigo' => 'PGK', 'descripcion' => 'Kina' ],
        [ 'codigo' => 'PHP', 'descripcion' => 'Philippine Peso' ],
        [ 'codigo' => 'PKR', 'descripcion' => 'Pakistan Rupee' ],
        [ 'codigo' => 'PLN', 'descripcion' => 'Zloty' ],
        [ 'codigo' => 'PYG', 'descripcion' => 'Guarani' ],
        [ 'codigo' => 'QAR', 'descripcion' => 'Qatari Rial' ],
        [ 'codigo' => 'RON', 'descripcion' => 'Romanian Leu' ],
        [ 'codigo' => 'ROL', 'descripcion' => 'Leu' ],
        [ 'codigo' => 'RUB', 'descripcion' => 'Russian Ruble' ],
        [ 'codigo' => 'RWF', 'descripcion' => 'Rwanda Franc' ],
        [ 'codigo' => 'SAR', 'descripcion' => 'Saudi Riyal' ],
        [ 'codigo' => 'RSD', 'descripcion' => 'Serbian Dinar' ],
        [ 'codigo' => 'SBD', 'descripcion' => 'Solomon Islands Dollar' ],
        [ 'codigo' => 'SCR', 'descripcion' => 'Seychelles Rupee' ],
        [ 'codigo' => 'SDD', 'descripcion' => 'Sudanese Dinar' ],
        [ 'codigo' => 'SDG', 'descripcion' => 'Sudanese Pound' ],
        [ 'codigo' => 'SRD', 'descripcion' => 'Surinam Dollar' ],
        [ 'codigo' => 'SEK', 'descripcion' => 'Swedish Krona' ],
        [ 'codigo' => 'SGD', 'descripcion' => 'Singapore Dollar' ],
        [ 'codigo' => 'SHP', 'descripcion' => 'St. Helena Pound' ],
        [ 'codigo' => 'SIT', 'descripcion' => 'Tolar' ],
        [ 'codigo' => 'SKK', 'descripcion' => 'Slovak Koruna' ],
        [ 'codigo' => 'SLL', 'descripcion' => 'Leone' ],
        [ 'codigo' => 'SOS', 'descripcion' => 'Somali Shilling' ],
        [ 'codigo' => 'SRG', 'descripcion' => 'Suriname Guilder' ],
        [ 'codigo' => 'SSP', 'descripcion' => 'South Sudanese Pound' ],
        [ 'codigo' => 'STD', 'descripcion' => 'Dobra' ],
        [ 'codigo' => 'SVC', 'descripcion' => 'El Salvador Colon' ],
        [ 'codigo' => 'SYP', 'descripcion' => 'Syrian Pound' ],
        [ 'codigo' => 'SZL', 'descripcion' => 'Lilangeni' ],
        [ 'codigo' => 'THB', 'descripcion' => 'Baht' ],
        [ 'codigo' => 'TJS', 'descripcion' => 'Somoni' ],
        [ 'codigo' => 'TMM', 'descripcion' => 'Manat' ],
        [ 'codigo' => 'TND', 'descripcion' => 'Tunisian Dinar' ],
        [ 'codigo' => 'TRY', 'descripcion' => 'Turkish Lira' ],
        [ 'codigo' => 'TMT', 'descripcion' => 'Turkmenistan New Manat' ],
        [ 'codigo' => 'TOP', 'descripcion' => 'Pa&apos;anga' ],
        [ 'codigo' => 'TRL', 'descripcion' => 'Turkish Lira' ],
        [ 'codigo' => 'TTD', 'descripcion' => 'Trinidad and Tobago Dollar' ],
        [ 'codigo' => 'TWD', 'descripcion' => 'New Taiwan Dollar' ],
        [ 'codigo' => 'TZS', 'descripcion' => 'Tanzanian Shilling' ],
        [ 'codigo' => 'UAH', 'descripcion' => 'Hryvnia' ],
        [ 'codigo' => 'UGX', 'descripcion' => 'Uganda Shilling' ],
        [ 'codigo' => 'USD', 'descripcion' => 'US Dollar' ],
        [ 'codigo' => 'USN', 'descripcion' => 'US Dollar(Next day)' ],
        [ 'codigo' => 'UYU', 'descripcion' => 'Peso Uruguayo' ],
        [ 'codigo' => 'UYI', 'descripcion' => 'Uruguay Peso en Unidades Indexadas(UI)' ],
        [ 'codigo' => 'UYW', 'descripcion' => 'Unidad Previsional' ],
        [ 'codigo' => 'UZS', 'descripcion' => 'Uzbekistan Sum' ],
        [ 'codigo' => 'VEB', 'descripcion' => 'Bolivar' ],
        [ 'codigo' => 'VND', 'descripcion' => 'Dong' ],
        [ 'codigo' => 'VUV', 'descripcion' => 'Vatu' ],
        [ 'codigo' => 'VES', 'descripcion' => 'Bolivar Soberano' ],
        [ 'codigo' => 'WST', 'descripcion' => 'Tala' ],
        [ 'codigo' => 'STN', 'descripcion' => 'Dobra' ],
        [ 'codigo' => 'XAF', 'descripcion' => 'CFA Franc' ],
        [ 'codigo' => 'XAG', 'descripcion' => 'Silver' ],
        [ 'codigo' => 'XAU', 'descripcion' => 'Gold' ],
        [ 'codigo' => 'XCD', 'descripcion' => 'East Carribean Dollar' ],
        [ 'codigo' => 'XDR', 'descripcion' => 'SDR' ],
        [ 'codigo' => 'XOF', 'descripcion' => 'CFA Franc' ],
        [ 'codigo' => 'XPD', 'descripcion' => 'Palladium' ],
        [ 'codigo' => 'XPF', 'descripcion' => 'CFP Franc' ],
        [ 'codigo' => 'XPT', 'descripcion' => 'Platinum' ],
        [ 'codigo' => 'XSU', 'descripcion' => 'Sucre' ],
        [ 'codigo' => 'XBA', 'descripcion' => 'Bond Markets Unit European Composite Unit(EURCO)' ],
        [ 'codigo' => 'XBB', 'descripcion' => 'Bond Markets Unit European Monetary Unit(E.M.U.-6)' ],
        [ 'codigo' => 'XBC', 'descripcion' => 'Bond Markets Unit European Unit of Account 17 (E.U.A.-17)' ],
        [ 'codigo' => 'XTS', 'descripcion' => 'Codes specifically reserved for testing purposes' ],
        [ 'codigo' => 'XXX', 'descripcion' => 'The codes assigned for transactions where no currency is involved' ],
        [ 'codigo' => 'YER', 'descripcion' => 'Yemeni Rial' ],
        [ 'codigo' => 'YUM', 'descripcion' => 'New Dinar' ],
        [ 'codigo' => 'ZMW', 'descripcion' => 'Zambian Kwacha' ],
        [ 'codigo' => 'ZWL', 'descripcion' => 'Zimbabwe Dollar' ],
        [ 'codigo' => 'ZAR', 'descripcion' => 'Rand' ],
        [ 'codigo' => 'ZMK', 'descripcion' => 'Kwacha' ],
        [ 'codigo' => 'ZWD', 'descripcion' => 'Zimbabwe Dollar' ],
    ];

    public $globalPorItem = [
        [ 'codigo' => '1', 'descripcion' => 'Global' ],
        [ 'codigo' => '2', 'descripcion' => 'Por Item' ],
    ];

    public $tiposRegimenes = [
        [ 'codigo' => '1', 'descripcion' => 'Régimen de Turismo' ],
        [ 'codigo' => '2', 'descripcion' => 'Importador' ],
        [ 'codigo' => '3', 'descripcion' => 'Exportador' ],
        [ 'codigo' => '4', 'descripcion' => 'Maquila' ],
        [ 'codigo' => '5', 'descripcion' => 'Ley N° 60/90' ],
        [ 'codigo' => '6', 'descripcion' => 'Régimen del Pequeño Productor' ],
        [ 'codigo' => '7', 'descripcion' => 'Régimen del Mediano Productor' ],
        [ 'codigo' => '8', 'descripcion' => 'Régimen Contable' ],
    ];

    public $tiposDocumentosIdentidades = [
        [
            'codigo' => 1,
            'descripcion' => 'Cédula paraguaya',
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Pasaporte',
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Cédula extranjera',
        ],
        [
            'codigo' => 4,
            'descripcion' => 'Carnet de residencia',
        ],
        [
            'codigo' => 9,
            'descripcion' => 'Otro',
        ],
    ];

    public $tiposDocumentosReceptor = [
        [
            'codigo' => 1,
            'descripcion' => 'Cédula paraguaya',
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Pasaporte',
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Cédula extranjera',
        ],
        [
            'codigo' => 4,
            'descripcion' => 'Carnet de residencia',
        ],
        [
            'codigo' => 5,
            'descripcion' => 'Innominado',
        ],
        [
            'codigo' => 6,
            'descripcion' => 'Tarjeta Diplomática de exoneración fiscal',
        ],
        [
            'codigo' => 9,
            'descripcion' => 'No especificado',
        ],
    ];

    public $tiposOperaciones = [
        ['codigo' => 1, 'descripcion' => 'B2B',],
        ['codigo' => 2, 'descripcion' => 'B2C',],
        ['codigo' => 3, 'descripcion' => 'B2G',],
        ['codigo' => 4, 'descripcion' => 'B2F',],
    ];

    public $indicadoresPresencias = [
        ['codigo' => 1, 'descripcion' => 'Operación presencial',],
        ['codigo' => 2, 'descripcion' => 'Operación electrónica',],
        ['codigo' => 3, 'descripcion' => 'Operación telemarketing',],
        ['codigo' => 4, 'descripcion' => 'Venta a domicilio',],
        ['codigo' => 5, 'descripcion' => 'Operación bancaria',],
        ['codigo' => 6, 'descripcion' => 'Operación cíclica',],
        ['codigo' => 9, 'descripcion' => 'Otro',],
    ];

    public $tipoReceptor = [
        ['codigo' => 1, 'descripcion' => 'Contribuyente',],
        ['codigo' => 2, 'descripcion' => 'No Contribuyente',],
    ];

    public $naturalezaVendedorAutofactura = [
        ['codigo' => 1, 'descripcion' => 'No contribuyente',],
        ['codigo' => 2, 'descripcion' => 'Extranjero',],
    ];

    public $notasCreditosMotivos = [
        [
            'codigo' => 1,
            'descripcion' => 'Devolución y Ajuste de precios',
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Devolución',
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Descuento',
        ],
        [
            'codigo' => 4,
            'descripcion' => 'Bonificación',
        ],
        [
            'codigo' => 5,
            'descripcion' => 'Crédito incobrable',
        ],
        [
            'codigo' => 6,
            'descripcion' => 'Recupero de costo',
        ],
        [
            'codigo' => 7,
            'descripcion' => 'Recupero de gasto',
        ],
        [
            'codigo' => 8,
            'descripcion' => 'Ajuste de precio',
        ],
    ];

    public $remisionesMotivos = [
        [
            'codigo' => 1,
            'descripcion' => 'Traslado por ventas',
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Traslado por consignación',
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Exportación',
        ],
        [
            'codigo' => 4,
            'descripcion' => 'Traslado por compra',
        ],
        [
            'codigo' => 5,
            'descripcion' => 'Importación',
        ],
        [
            'codigo' => 6,
            'descripcion' => 'Traslado por devolución',
        ],
        [
            'codigo' => 7,
            'descripcion' => 'Traslado entre locales de la empresa',
        ],
        [
            'codigo' => 8,
            'descripcion' => 'Traslado de bienes por transformación',
        ],
        [
            'codigo' => 9,
            'descripcion' => 'Traslado de bienes por reparación',
        ],
        [
            'codigo' => 10,
            'descripcion' => 'Traslado por emisor móvil',
        ],
        [
            'codigo' => 11,
            'descripcion' => 'Exhibición o demostración',
        ],
        [
            'codigo' => 12,
            'descripcion' => 'Participación en ferias',
        ],
        [
            'codigo' => 13,
            'descripcion' => 'Traslado de encomienda',
        ],
        [
            'codigo' => 14,
            'descripcion' => 'Decomiso',
        ],
        [
            'codigo' => 99,
            'descripcion' => 'Otro',
        ],
    ];

    public $remisionesResponsables = [
        [
            'codigo' => 1,
            'descripcion' => 'Emisor de la factura',
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Poseedor de la factura y bienes',
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Empresa transportista',
        ],
        [
            'codigo' => 4,
            'descripcion' => 'Despachante de Aduanas',
        ],
        [
            'codigo' => 5,
            'descripcion' => 'Agente de transporte o intermediario',
        ],
    ];

    public $condicionesOperaciones = [
        [
            'codigo' => 1,
            'descripcion' => 'Contado',
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Crédito',
        ],
    ];

    public $condicionesTiposPagos = [
        [
            'codigo' => 1,
            'descripcion' => 'Efectivo',
        ],
        [
            'codigo' => 2,
            'descripcion' => 'Cheque',
        ],
        [
            'codigo' => 3,
            'descripcion' => 'Tarjeta de crédito',
        ],
        [
            'codigo' => 4,
            'descripcion' => 'Tarjeta de débito',
        ],
        [
            'codigo' => 5,
            'descripcion' => 'Transferencia',
        ],
        [
            'codigo' => 6,
            'descripcion' => 'Giro',
        ],
        [
            'codigo' => 7,
            'descripcion' => 'Billetera electrónica',
        ],
        [
            'codigo' => 8,
            'descripcion' => 'Tarjeta empresarial',
        ],
        [
            'codigo' => 9,
            'descripcion' => 'Vale',
        ],
        [
            'codigo' => 10,
            'descripcion' => 'Retención',
        ],
        [
            'codigo' => 11,
            'descripcion' => 'Pago por anticipo',
        ],
        [
            'codigo' => 12,
            'descripcion' => 'Valor fiscal',
        ],
        [
            'codigo' => 13,
            'descripcion' => 'Valor comercial',
        ],
        [
            'codigo' => 14,
            'descripcion' => 'Compensación',
        ],
        [
            'codigo' => 15,
            'descripcion' => 'Permuta',
        ],
        [
            'codigo' => 16,
            'descripcion' => 'Pago bancario',
        ],
        [
            'codigo' => 17,
            'descripcion' => 'Pago Móvil',
        ],
        [
            'codigo' => 18,
            'descripcion' => 'Donación',
        ],
        [
            'codigo' => 19,
            'descripcion' => 'Promoción',
        ],
        [
            'codigo' => 20,
            'descripcion' => 'Consumo Interno',
        ],
        [
            'codigo' => 21,
            'descripcion' => 'Pago Electrónico',
        ],
        [
            'codigo' => 99,
            'descripcion' => 'Otro',
        ],
    ];

    /////////////////////////////////////////////////////////////////////////////////////////////
    
    public function getTiposDocumentos()
    {
        return $this->tiposDocumentos;
    }

    /*public function getTiposEventos()
    {
        return $this->tiposEventos;
    }*/

    public function getTiposEmisiones()
    {
        return $this->tiposEmisiones;
    }

    public function getTiposTransacciones()
    {
        return $this->tiposTransacciones;
    }

    public function getTiposImpuestos()
    {
        return $this->tiposImpuestos;
    }

    public function getMonedas()
    {
        return $this->monedas;
    }

    public function getGlobalPorItem()
    {
        return $this->globalPorItem;
    }

    public function getTiposRegimenes()
    {
        return $this->tiposRegimenes;
    }

    public function getTiposDocumentosIdentidades()
    {
        return $this->tiposDocumentosIdentidades;
    }

    public function getTiposDocumentosReceptor()
    {
        return $this->tiposDocumentosReceptor;
    }

    public function getTiposOperaciones()
    {
        return $this->tiposOperaciones;
    }

    public function getIndicadoresPresencias()
    {
        return $this->indicadoresPresencias;
    }

    public function getTipoReceptor()
    {
        return $this->tipoReceptor;
    }

    public function getNaturalezaVendedorAutofactura()
    {
        return $this->naturalezaVendedorAutofactura;
    }

    public function getNotasCreditosMotivos()
    {
        return $this->notasCreditosMotivos;
    }

    public function getRemisionesMotivos()
    {
        return $this->remisionesMotivos;
    }

    public function getRemisionesResponsables()
    {
        return $this->remisionesResponsables;
    }

    public function getCondicionesOperaciones()
    {
        return $this->condicionesOperaciones;
    }

    public function getCondicionesTiposPagos()
    {
        return $this->condicionesTiposPagos;
    }






}