<?php


function pplcz_format_args($args) {
    $formatted = [];
    foreach ($args as $arg) {
        if (is_object($arg)) {
            $formatted[] = get_class($arg);
        } elseif (is_array($arg)) {
            $formatted[] = 'Array(' . count($arg) . ')';
        } elseif (is_resource($arg)) {
            $formatted[] = 'Resource';
        } elseif (is_null($arg)) {
            $formatted[] = 'NULL';
        } elseif (is_bool($arg)) {
            $formatted[] = $arg ? 'true' : 'false';
        } elseif (is_string($arg)) {
            $formatted[] = "'" . (mb_strlen($arg) > 20 ? mb_substr($arg, 0, 20) . '...' : $arg) . "'";
        } else {
            $formatted[] = (string) $arg;
        }
    }
    return join(', ', $formatted);
}
$pplcz_in_handler = false;

function pplcz_error_handler ($errno, $errstr, $errfile, $errline) {
    global $pplcz_in_handler;

    if ($pplcz_in_handler)
        return;

    $pplcz_in_handler = true;

    $backtrace = debug_backtrace();
    $path = realpath(__DIR__ . '/../..');
    $inplugin = strpos($errfile, $path) !== false;
    $out= [
        $errstr,
        "Stack trace:",

    ];

    foreach ($backtrace as $key => $frame)
    {
        if (!$key) {
            continue;
        }

        $inplugin = $inplugin || strpos($frame['file'], $path) !== false;
        $file = isset($frame['file']) ? $frame['file'] : '[internal function]';
        $line = isset($frame['line']) ? $frame['line'] : '';
        $function = isset($frame['function']) ? $frame['function'] : '';
        $args = isset($frame['args']) ? pplcz_format_args($frame['args']) : '';
        $out[] = "#$key $file($line): $function($args)";
    }

    if ($inplugin)
    {
        try {
            $error = $errstr . "\n" . join("\n", $out);
            $logdata = new \PPLLog();
            $logdata->message = $error;
            $logdata->errorhash = sha1($error);
            $logdata->timestamp = date('Y-m-d H:i:s');
            $logdata->save();
        }
        catch (Throwable $ex)
        {

        }
    }
    $pplcz_in_handler = false;
}

function pplcz_shutdown_handler()
{
    global $pplcz_in_handler;

    if ($pplcz_in_handler)
        return;

    $pplcz_in_handler = true;

    $error = error_get_last();
    if ($error)
    {
        $message = explode("\n", $error['message']);
        $path = realpath(__DIR__ . '/../../');
        if (array_filter($message, function($item) use($path){
            return strpos($item, $path) !== false;
        }))
        {
            try {
                $logdata = new \PPLCZ\Data\LogData();
                $logdata->message = $error['message'];
                $logdata->errorhash = sha1($error['message']);
                $logdata->timestamp = date('Y-m-d H:i:s');
                $logdata->save();
            }
            catch (\Throwable $ex)
            {

            }
        }
        return;
    }

}
set_error_handler("pplcz_error_handler");
register_shutdown_function("pplcz_shutdown_handler");
