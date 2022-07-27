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
    <meta HTTP-EQUIV="refresh" CONTENT="180"/>
    <title><?php echo $webname ?> - Global Bans</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css?v=<?php echo filemtime("css/global.css"); ?>">
    <link rel="stylesheet" href="https:////cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="headercontent"> <!-- open headercontent -->
    <div id="headerimage"><img src="css/img/logo.png"></div>
    <div id="content-title" class="float-end"><?php echo "Servertime: " . date('H:i - d.n.Y'); ?></div>
    <div id="content-title"><?php echo $webname ?> - Global Bans List</div>


    <table class="table table-dark servertable">
        <thead>
        <tr>
            <th>#</th>
            <th>IP Adress</th>
            <th>Server Name</th>
            <th>Gamemode</th>
            <th>Map</th>
            <th>Players</th>
        </tr>
        </thead>
        <tbody>

        <?php
        //Server Source Query
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
            <tr>
                <td width="30"><img src="css/img/gameicon/gmod.png"/></td>
                <td><a href="steam://connect/<?php echo $ip; ?>:<?php echo $port; ?>"><?php echo $ip; ?>
                        :<?php echo $port; ?></a></td>
                <td><?php echo $row['HostName']; ?></td>
                <td><?php echo htmlspecialchars($Info['ModDesc']); ?></td>
                <td><?php echo htmlspecialchars($Info['Map']); ?></td>
                <td><?php echo htmlspecialchars($Info['Players']) . " / " . htmlspecialchars($Info['MaxPlayers']); ?></td>
            </tr>
        <?php } ?>


        </tbody>
    </table>

</div>  <!-- close headercontent -->


<div class="banlistcontent">

    <div class="tabletempban float-start">
        <div id="table-title">
            <?php
            $query = "SELECT COUNT(BanID) FROM bans WHERE NOT `bans`.`Length`= 0";
            $result = mysqli_fetch_array($connection->query($query));
            echo('Temporary Bans List - ' . $result[0] . ' temporary Bans and Counting');
            ?>
        </div>
        <table class="table table-dark table-hover" id="tempbantable">
            <thead>
            <tr>
                <th>Steam ID</th>
                <th>Target Name</th>
                <th>Reason</th>
                <th>Expires on</th>
                <th>Banned by</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $query = "SELECT * FROM `servers`, `bans` WHERE `servers`.`ServerID` = `bans`.`ServerID` AND NOT `bans`.`Length`= 0 ORDER BY BanID DESC";
            $result = $connection->query($query);
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><a href="<?php echo calcSteamID($row['OSteamID']) ?>"><?php echo $row['OSteamID']; ?></a></td>
                    <td><?php echo $row['OName']; ?></td>
                    <td><?php echo $row['Reason'] ?></td>
                    <td><?php if ($row['Length'] < time()) {
                            echo "Unbanned";
                        } else {
                            echo date("H:m - d.m.Y", $row['Length']);
                        } ?></td>
                    <td><a href="<?php echo calcSteamID($row['ASteamID']); ?>"><?php echo $row['AName']; ?></a></td>
                </tr>
                <tr class="separator"/>
                <?php
            }
            ?>

            </tbody>
            </tbody>
        </table>

    </div>


    <div class="tablepermban float-end">
        <div id="table-title">
            <?php
            $query = "SELECT COUNT(BanID) FROM bans WHERE `bans`.`Length`= 0";
            $result = mysqli_fetch_array($connection->query($query));
            echo('Permanent Bans List - ' . $result[0] . ' Bans and Counting');
            ?>
        </div>
        <table class="table table-dark table-hover" id="permbantable">
            <thead>
            <tr>
                <th>#</th>
                <th>Steam ID</th>
                <th>Target Name</th>
                <th>Reason</th>
                <th>Banned by</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $query = "SELECT * FROM `servers`, `bans` WHERE `servers`.`ServerID` = `bans`.`ServerID` AND `bans`.`Length`= 0 ORDER BY BanID DESC";
            $result = $connection->query($query);
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['BanID']; ?></td>
                    </td>
                    <td><a href="<?php echo calcSteamID($row['OSteamID']) ?>"><?php echo $row['OSteamID']; ?></a></td>
                    <td><?php echo $row['OName']; ?></td>
                    <td><?php echo $row['Reason'] ?></td>
                    <td><a href="<?php echo calcSteamID($row['ASteamID']); ?>"><?php echo $row['AName']; ?></a></td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>


</div> <!-- close banlistcontent -->


<footer class="footer mt-auto py-3 bg-dark">

    <span id="credits" class="text-muted">[ULX Global Ban Fix] Modern Theme by <a
                href="https://github.com/1day2die">1Day2Die</a></span>

</footer>


</body>


<script>
    $(document).ready(function () {
        $('#permbantable').dataTable({
            "order": [[0, "desc"]],
        });
        $('#tempbantable').dataTable({
            "order": [[0, "desc"]],
        });
    });
</script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"
        crossorigin="anonymous"></script>

</html>