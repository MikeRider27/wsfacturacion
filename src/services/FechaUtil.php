<?php

require_once 'StringUtilService.php'; // Assuming you have defined StringUtilService with leftZero function

class FechaUtilService {
    private $stringUtilService;

    public function __construct() {
        $this->stringUtilService = new StringUtilService();
    }

    public function convertToAAAAMMDD($fecha) {
        $year = $fecha->format('Y');
        $month = $this->stringUtilService->leftZero($fecha->format('n'), 2);
        $day = $this->stringUtilService->leftZero($fecha->format('j'), 2);
        return $year . $month . $day;
    }

    public function convertToJSONFormat($fecha) {
        $year = $fecha->format('Y');
        $month = $this->stringUtilService->leftZero($fecha->format('n'), 2);
        $day = $this->stringUtilService->leftZero($fecha->format('j'), 2);
        $hours = $this->stringUtilService->leftZero($fecha->format('G'), 2);
        $minutes = $this->stringUtilService->leftZero($fecha->format('i'), 2);
        $seconds = $this->stringUtilService->leftZero($fecha->format('s'), 2);

        return $year . '-' . $month . '-' . $day . 'T' . $hours . ':' . $minutes . ':' . $seconds;
    }

    public function isIsoDateTime($str) {
        return (bool)preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $str);
    }

    public function isIsoDate($str) {
        return (bool)preg_match('/\d{4}-\d{2}-\d{2}/', $str);
    }
}

