<?php
require __DIR__ . '/steamid.php';

$requirements = [
    "mysql" => "5.7.22",
];


function calcSteamID($steamID, $onlyID = false){
        if($steamID === "CONSOLE"){
            return "http://steamcommunity.com";
        }
        $s = new SteamID( $steamID );
    
        if ($onlyID == false) {
            return "http://steamcommunity.com/profiles/".$s->ConvertToUInt64();
        }else{
            return $s->ConvertToUInt64();
        }
}

function obtainPlayerInfo($steamID){
    $json = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".getEnvironmentValue("STEAM_API_KEY")."&steamids=".calcSteamID($steamID,true));
    return json_decode($json);
}


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

    return(version_compare($versionoutput, $requirements["mysql"], ">") ? "OK" : $versionoutput);
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