<?php

namespace Dalton\Framework;

require_once ROOT_PATH.'config.php';

// Should probably create a different exception handler for production cases.
class ExceptionHandler {

    public static function errorHandler($level, $message, $file, $line) {
        if (error_reporting() !== 0) {
            http_response_code(500);
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception) {
        $code = $exception->getCode();
        if (is_int($code)) {
            http_response_code($code);
        } else {
            http_response_code(500);
        }
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