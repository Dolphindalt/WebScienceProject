<?php

namespace Dalton\Framework;

class View {
    
    public static function render($view, $args = []) {
        // Converts [key => map] into $key = map.
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/3leaf/Views/$view";

        if (is_readable($file)) {
            require $file;
        } 
        else {
            throw new \Exception("Failed to find view: " . $file);
        }
    }

}

?>