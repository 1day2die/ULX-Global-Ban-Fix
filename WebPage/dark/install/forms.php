<?php
include "../lib/functions.php";
if (isset($_POST['checkDB'])) {

$values = [
//SETTINGS::VALUE => REQUEST-VALUE (coming from the html-form)
"MYSQL_HOST" => "databasehost",
"MYSQL_DB" => "database",
"MYSQL_USER" => "databaseuser",
"MYSQL_PASSWORD" => "databaseuserpass",
"MYSQL_PORT" => "databaseport"
];

try {
    $db = new mysqli($_POST["databasehost"], $_POST["databaseuser"], $_POST["databaseuserpass"], $_POST["database"], $_POST["databaseport"]);
}catch(Exception $e) {
    wh_log($e);
    header("LOCATION: index.php?step=2&message=Could not connect to the Database. Check the logs at /install/logs.");
    die();
}

foreach ($values as $key => $value) {
    $param = $_POST[$value];
    setEnvironmentValue($key, $param);
}

$bansTableQuery = "CREATE TABLE `bans` (
    `BanID` int(255) NOT NULL,
  `OSteamID` varchar(255) NOT NULL,
  `OName` varchar(255) DEFAULT NULL,
  `Length` bigint(255) NOT NULL,
  `Time` bigint(255) NOT NULL,
  `AName` varchar(255) NOT NULL,
  `ASteamID` varchar(255) NOT NULL,
  `Reason` varchar(255) NOT NULL,
  `ServerID` int(255) NOT NULL,
  `MAdmin` varchar(255) NOT NULL,
  `MTime` bigint(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$serversTableQuery = "CREATE TABLE `servers` (
  `ServerID` int(255) NOT NULL,
  `IPAddress` varchar(255) NOT NULL,
  `HostName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$alterBansQuery = "ALTER TABLE `bans`
  ADD PRIMARY KEY (`BanID`);";
$alterServersQuery = "ALTER TABLE `servers`
  ADD PRIMARY KEY (`ServerID`);";
$alterBansQuery2 = "ALTER TABLE `bans`
  MODIFY `BanID` int(255) NOT NULL AUTO_INCREMENT;";
$alterServersQuery2 = "ALTER TABLE `servers`
  MODIFY `ServerID` int(255) NOT NULL AUTO_INCREMENT;";

try{
$db->query($bansTableQuery);
$db->query($serversTableQuery);
$db->query($alterBansQuery);
$db->query ($alterBansQuery2);
$db->query($alterServersQuery);
$db->query($alterServersQuery2);
}catch(Exception $e) {
    wh_log($e);
    header("LOCATION: index.php?step=2&message=Error while creating the Database Tables. Check the logs at /install/logs.");
    die();
}


header("LOCATION: index.php?step=3");

}

if (isset($_POST['insertName'])) {

        setEnvironmentValue("COMMUNITY_NAME", $_POST["cname"]);


    $lockfile = fopen("install.lock", "w") or die("Unable to open file!");
    fwrite($lockfile, "locked");
    fclose($lockfile);
    header("LOCATION: ../");
}

?>