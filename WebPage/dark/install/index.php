<?php
include "../lib/functions.php";
include "../lib/dotenv.php";


if (file_exists("install.lock")) {
    header("LOCATION: /");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Global Bans Install</title>
    <link rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">
    <link rel="stylesheet" href="css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<style>


    .ok {
        color: green;
    }

    .ok::before {
        content: "✔️";
    }

    .notok {
        color: red;
    }

    .notok::before {
        content: "❌";
    }
</style>
<body class="bg-dark d-flex align-items-center justify-content-center">
<?php
$cardheader = '
        <div class="card card-outline-success bg-success w-50 mt-5 ">
        <div class="card-header text-center">
            <b class="mr-1 text-light">ULX Global bans Rewrite</b>
        </div>
        <div class="card-body bg-light">';


if (!isset($_GET['step'])) {

    echo $cardheader;
    ?>
    <p class="login-box-msg">This installer will lead you through the most crucial Steps of ULX-Globalbans-Rewrite</p>

    <p class="<?php print(checkWriteable() == true ? "ok" : "notok"); ?>">Write-permissions on .env-file</p>

    <p class="<?php print(getMySQLVersion() === "OK" ? "ok" : "notok"); ?>"> mysql
        version: <?php echo getMySQLVersion(); ?> (minimum required <?php echo $requirements["mysql"]; ?>)</p>


    <a href="?step=2">
        <button class="btn btn-primary">Lets go</button>
    </a>
    </div>
    </div>

    <?php
}
if (isset($_GET['step']) && $_GET['step'] == 2) {
    echo $cardheader;
    ?>
    <p class="login-box-msg">Lets start with your Database</p>
    <?php if (isset($_GET['message'])) {
        echo "<p class='notok'>" . $_GET['message'] . "</p>";
    }
    ?>

    <form method="POST" enctype="multipart/form-data" class="mb-3"
          action="forms.php" name="checkDB">

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="custom-control mb-3">
                        <label for="databasehost">Database Host</label>
                        <input x-model="databasehost" id="databasehost" name="databasehost" type="text"
                               required
                               value="127.0.0.1" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control mb-3">
                        <label for="databaseport">Database Port</label>
                        <input x-model="databaseport" id="databaseport" name="databaseport"
                               type="number" required
                               value="3306" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control mb-3">
                        <label for="databaseuser">Database User</label>
                        <input x-model="databaseuser" id="databaseuser" name="databaseuser" type="text"
                               required
                               value="globalbans" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control mb-3">
                        <label for="databaseuserpass">Database User Password</label>
                        <input x-model="databaseuserpass" id="databaseuserpass" name="databaseuserpass"
                               type="text" required
                               class="form-control ">
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control mb-3">
                        <label for="database">Database</label>
                        <input x-model="database" id="database" name="database" type="text" required
                               value="globalbans" class="form-control">
                    </div>
                </div>

            </div>

            <button class="btn btn-primary" name="checkDB">Submit</button>
        </div>
    </form>
    <?php
}
if (isset($_GET['step']) && $_GET['step'] == 3) {
echo $cardheader;
?>
<p class="login-box-msg">Last step: Whats your Community Name?</p>
<?php if (isset($_GET['message'])) {
    echo "<p class='notok'>" . $_GET['message'] . "</p>";
}
?>

<form method="POST" enctype="multipart/form-data" class="mb-3"
      action="forms.php" name="insertName">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="custom-control mb-3">
                    <label for="name">Community Name</label>
                    <input x-model="cname" id="cname" name="cname" type="text"
                           required
                           value="My Awesome Community" class="form-control">
                </div>
            </div>
        </div>
        <button class="btn btn-primary" name="insertName">Submit</button>
    </form>
    </div>
    <?php
    }
    ?>


</body>
</html>