<?php

class Log {
    public static $EOL = PHP_EOL;

    public static function info($text) {
        echo "" . trim($text) . self::$EOL;
    }

    public static function warning($text) {
        echo "WARNING: " . trim($text) . self::$EOL;
    }

    public static function error($text) {
        echo "ERROR: " . trim($text) . self::$EOL;
    }
}

Log::$EOL = isset($argv) ? PHP_EOL : "<br>\n";
