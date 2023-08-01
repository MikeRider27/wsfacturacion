<?php

class StringUtilService {
    public function leftZero($value, $size) {
        $s = (string)$value;
        while (strlen($s) < $size) {
            $s = '0' . $s;
        }
        return $s;
    }
}