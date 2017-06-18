<?php
class FrameworkMisc {
    static function stackTrace() {
        $stack = debug_backtrace();
        $output = 'Stack trace:' . PHP_EOL;
     
        $stackLen = count($stack);
        for ($i = 1; $i < $stackLen; $i++) {
            $entry = $stack[$i];
     
            $func = $entry['function'] . '(';
            $argsLen = count($entry['args']);
            for ($j = 0; $j < $argsLen; $j++) {
                $func .= $entry['args'][$j];
                if ($j < $argsLen - 1) $func .= ', ';
            }
            $func .= ')';
     
            $output .= '#' . ($i - 1) . ' ' . $entry['file'] . ':' . $entry['line'] . ' - ' . $func . PHP_EOL;
        }
     
        return $output;
    }
}
