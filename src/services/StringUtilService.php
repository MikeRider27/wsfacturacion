<?php
class StringUtilService {
    public function leftZero($value, $size) {
        return str_pad($value, $size, '0', STR_PAD_LEFT);
    }
}
?>