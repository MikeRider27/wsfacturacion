<?php

class ArrayToXmlConverter {
   public static function convert($data, &$xml_data) {
      foreach ($data as $key => $value) {
            if (is_array($value)) {
               if (is_numeric($key)) {
                  $key = 'item';
               }
               $subnode = $xml_data->addChild($key);
               self::convert($value, $subnode);
            } else {
               $xml_data->addChild($key, htmlspecialchars($value));
            }
      }
   }
}

?>
