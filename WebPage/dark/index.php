<?php
if (!file_exists("install/install.lock")) {
    header("LOCATION: install");
}
include "lib/functions.php";
require __DIR__ . '/lib/dotenv.php';
require __DIR__ . '/SourceQuery/bootstrap.php';


use DevCoder\DotEnv;
use xPaw\SourceQuery\SourceQuery;

define('SQ_TIMEOUT', 1);
define('SQ_ENGINE', SourceQuery :: SOURCE);

(new DotEnv(".env"))->load();


// Page Generator Time Start Script
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

//require_once ('SourceQuery/SourceQuery.class.php'); // If you get and error reguarding this line, comment out the line above and use this one :)

// Config to your database - Edit this!
$dbhost = getEnvironmentValue("MYSQL_HOST");            // Server IP/Domain of where the datab-base resides.
$dbdatabase = getEnvironmentValue("MYSQL_DB");            // Data-base Name.
$dbuser = getEnvironmentValue("MYSQL_USER");                // Username.
$dbpassword = getEnvironmentValue("MYSQL_PASSWORD");                    // Password.
$webname = getEnvironmentValue("COMMUNITY_NAME");        // Title of Community/Server/Website/Domain, pick one.
?>
<?php
// MySQL Connect/Query
$connection = new mysqli($dbhost, $dbuser, $dbpassword, $dbdatabase);
if ($connection->connect_error) {
    die("DB Connection failed: " . $connection->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta HTTP-EQUIV="refresh" CONTENT="70"/>
    <meta name="keywords" content="ulx, global, ban, banning, bans, gmod, garrys, mod, addon, ulib">
    <title><?php echo $webname ?> - Global Bans</title>
    <link rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/global.css?v=<?php echo filemtime("css/global.css");?>">

</head>
<body>
<div class="container mt-4">
    <div class="header"></div>
    <div id="content-title" class="float-end"><?php echo "Servertime: ". date('H:i d.n.Y');?></div>
    <div id="content-title"><?php echo $webname ?> - Global Bans List</div>
    <div class="content">
        <div id="topic-title">Server List</div>
        <table width="100%" class="status" border="0">
            <tr>
                <td width="30">&nbsp;</td>
                <td width="160">
                    <strong>IP Address</strong>
                </td>
                <td width="432">
                    <strong>Server Name</strong>
                </td>
                <td width="182">
                    <strong>Gamemode</strong>
                </td>
                <td width="159">
                    <strong>Map</strong>
                </td>
                <td width="51">
                    <strong>Players</strong>
                </td>
            </tr>
        </table>
        <?php
        $query = "SELECT * FROM servers";
        $result = $connection->query($query);
        while ($row = mysqli_fetch_assoc($result)) {

            $fullip = explode(":", $row['IPAddress']);
            $ip = $fullip[0];
            $port = $fullip[1];


            $Timer = MicroTime(true);
            $Query = new SourceQuery();

            $Info = array();
            $Rules = array();
            $Players = array();

            try {
                $Query->Connect($ip, $port, SQ_TIMEOUT, SQ_ENGINE);

                $Info = $Query->GetInfo();
                $Players = $Query->GetPlayers();
            } catch (Exception $e) {
                $Exception = $e;
            }

            $Query->Disconnect();
            ?>
            <table width="100%" class="status" border="0">
                <tr>
                    <td width="30">&nbsp;<img src="css/img/gameicon/gmod.png"/></td>
                    <td width="160"><a href="steam://connect/<?php echo $ip; ?>:<?php echo $port; ?>"><?php echo $ip; ?>
                            :<?php echo $port; ?></a></td>
                    <td width="438"><?php echo $row['HostName']; ?></td>
                <td width="182"><?php echo (isset($Info['ModDesc']) ? htmlspecialchars($Info['ModDesc']) : "N/A"); ?></td>
                 <td width="159"><?php echo (isset($Info['Map']) ? htmlspecialchars($Info['Map']) : "N/A"); ?></td>
                  <td width="51"><?php echo (isset($Info['Players']) ? htmlspecialchars($Info['Players']) . " / " . htmlspecialchars($Info['MaxPlayers']) : "N/A"); ?></td>            </tr>
                </tr>
            </table>
        <?php } ?>
        <div id="topic-title">
            <p>&nbsp;</p>
            <?php
            $query = "SELECT COUNT(BanID) FROM bans WHERE NOT `bans`.`Length`= 0";
            $result = mysqli_fetch_array($connection->query($query));
            echo('Bans List - ' . $result[0] . ' temporary Bans and Counting');
            ?>
        </div>
        <table class="status">
            <tr>
                <td width="28"></td>
                <td width="155">
                    <strong>Steam ID</strong>
                </td>
                <td width="162">
                    <strong>Alias</strong></td>
                <td width="323">
                    <strong>Reason</strong>
                </td>
                <td width="185">
                    <strong>Expires on</strong>
                </td>
                <td width="169">
                    <strong>Banned by</strong>
                </td>
            </tr>
        </table>

        <?php
        $query = "SELECT * FROM `servers`, `bans` WHERE `servers`.`ServerID` = `bans`.`ServerID` AND NOT `bans`.`Length`= 0 ORDER BY BanID DESC";
        $result = $connection->query($query);
        while ($row = mysqli_fetch_assoc($result)) {

            ?>
            <table width="100%" class="status" border="0">

                <tr>
                    <td width="28"><img src="css/img/gameicon/gmod.png" title="<?php echo $row['HostName']; ?>"
                                              width="16" height="16"/></td>
                    <td width="155"><a href="<?php echo calcSteamID($row['OSteamID']) ?>"><?php echo $row['OSteamID']; ?></a></td>
                    <td width="162"><?php echo $row['OName']; ?></td>
                    <td width="323"><?php echo $row['Reason'] ?></td>
                    <td width="185">
                        <?php if ($row['Length'] == '0') {
                            echo "<em>Permanent</em>";
                        } elseif ($row['Length'] < time()) {
                            echo "Unbanned";
                        } else {
                            echo date("H:m - d.m.Y", $row['Length']);
                        }
                        ?>
                    </td>

                    <td width="169"><a href="<?php echo calcSteamID($row['ASteamID']); ?>"><?php echo $row['AName']; ?></a></td>
                </tr>
            </table>

        <?php } ?>
    </div>

    <div id="topic-title" class="mt-5">
        <p>&nbsp;</p>
        <?php
        $query = "SELECT COUNT(BanID) FROM bans WHERE `bans`.`Length`= 0";
        $result = mysqli_fetch_array($connection->query($query));
        echo('Bans List - ' . $result[0] . ' permanent Bans and Counting');
        ?>
    </div>
<table class="status">

    <tr>
        <td width="28"></td>
        <td width="155">
            <strong>Steam ID</strong>
        </td>
        <td width="162">
            <strong>Alias</strong></td>
        <td width="323">
            <strong>Reason</strong>
        </td>
        <td width="169">
            <strong>Banned by</strong>
        </td>
    </tr>
</table>

    <?php
    $query = "SELECT * FROM `servers`, `bans` WHERE `servers`.`ServerID` = `bans`.`ServerID` AND `bans`.`Length`= 0 ORDER BY BanID DESC";
    $result = $connection->query($query);
    while ($row = mysqli_fetch_assoc($result)) {

        ?>
        <table width="100%" class="status" border="0">

            <tr>
                <td width="28">&nbsp;<img src="css/img/gameicon/gmod.png" title="<?php echo $row['HostName']; ?>"
                               width="16" height="16"/></td>
                <td width="155"><a href="<?php echo calcSteamID($row['OSteamID']) ?>"><?php echo $row['OSteamID']; ?></a></td>
                <td width="162"><?php echo $row['OName']; ?></td>
                <td width="323"><?php echo $row['Reason'] ?></td>

                <td width="169"><a href="<?php echo calcSteamID($row['ASteamID']); ?>"><?php echo $row['AName']; ?></a></td>
            </tr>
        </table>
    <?php } ?>
    </div>


<footer>
    <div class="container">
        <p class="pull-right">Generated in <span class="badge badge-success">
                    <?php
                    // Page Generator Time Finish
                    $time = microtime();
                    $time = explode(' ', $time);
                    $time = $time[1] + $time[0];
                    $finish = $time;
                    $total_time = round(($finish - $start), 4);
                    echo $total_time;
                    echo "s";
                    ?></span>
        </p>
        <p>
            Skin Implemented by <a href="http://ban-hammer.net" title="Q4's Website">Q4-Bi.</a> for <a
                    href="http://facepunch.com/showthread.php?t=1231554" title="Offical Thread" target="_blank">ULX
                Global Ban (0.6)</a> v1.2 fixed up by 1Day2Die
        </p>
    </div>
</footer>
</body>
</html>