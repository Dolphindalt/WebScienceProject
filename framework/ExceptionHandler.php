<?php

namespace Dalton\Framework;

require_once ROOT_PATH.'config.php';

class ExceptionHandler {

    public static function errorHandler($level, $message, $file, $line) {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception) {
        $code = $exception->getCode();
        http_response_code($code);
        if (SHOW_SERVER_ERROR) {
            echo "<h1>Server side exception</h1>";
            echo "<p>From class '" . get_class($exception) . "'</p>";
            echo "<p>'" . $exception->getMessage() . "'</p>";
            echo "<p>Stacktrace: <pre>'" . $exception->getTraceAsString() . "'</pre></p>";
            echo "<p>File: '" . $exception->getFile() . "' Line: '" . $exception->getLine() . "</p>";
        }
    }

}

?>