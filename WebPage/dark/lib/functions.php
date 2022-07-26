<?php

$requirements = [
    "mysql" => "5.7.22",
];


function getEnvironmentValue($envKey)
{
    $envFile = dirname(__DIR__) . "/.env";
    $str = file_get_contents($envFile);

    $str .= "\n"; // In case the searched variable is in the last line without \n
    $keyPosition = strpos($str, "{$envKey}=");
    $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
    $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
    $value = substr($oldLine, strpos($oldLine, "=") + 1);



    return $value;
}

function setEnvironmentValue($envKey, $envValue)
{

    $envFile = dirname(__DIR__) . "/.env";
    $str = file_get_contents($envFile);

    $str .= "\n"; // In case the searched variable is in the last line without \n
    $keyPosition = strpos($str, "{$envKey}=");
    $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
    $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
    $str = substr($str, 0, -1);

    $fp = fopen($envFile, 'w');
    fwrite($fp, $str);
    fclose($fp);
}
function getMySQLVersion()
{
    global $requirements;

    $output = shell_exec('mysql -V');
    preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);

    $versionoutput = $version[0] ?? "0";

    return (intval($versionoutput) > intval($requirements["mysql"]) ? "OK" : $versionoutput);
}

function checkWriteable()
{
    return is_writable("../.env");
}


function wh_log($log_msg)
{
    $log_filename = "logs";
    if (!file_exists($log_filename)) {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename . '/installer.log';
    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
    file_put_contents($log_file_data, "[" . date('h:i:s') . "] " . $log_msg . "\n", FILE_APPEND);
}
?>