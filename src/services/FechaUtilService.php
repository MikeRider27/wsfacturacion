<?php
class FechaUtilService {
    private $stringUtilService;

    public function __construct() {
        $this->stringUtilService = new StringUtilService();
    }

    public function convertToAAAAMMDD($fecha) {
        $year = $fecha->format('Y');
        $month = str_pad($fecha->format('n'), 2, '0', STR_PAD_LEFT);
        $day = str_pad($fecha->format('j'), 2, '0', STR_PAD_LEFT);
        return $year . $month . $day;
    }

    public function convertToJSONFormat($fecha) {
        $year = $fecha->format('Y');
        $month = str_pad($fecha->format('n'), 2, '0', STR_PAD_LEFT);
        $day = str_pad($fecha->format('j'), 2, '0', STR_PAD_LEFT);
        $hours = str_pad($fecha->format('G'), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($fecha->format('i'), 2, '0', STR_PAD_LEFT);
        $seconds = str_pad($fecha->format('s'), 2, '0', STR_PAD_LEFT);

        return $year . '-' . $month . '-' . $day . 'T' . $hours . ':' . $minutes . ':' . $seconds;
    }

    public function isIsoDateTime($str) {
        return (bool)preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $str);
    }

    public function isIsoDate($str) {
        return (bool)preg_match('/\d{4}-\d{2}-\d{2}/', $str);
    }
}
?>