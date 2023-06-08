<?php
if (!isset($_SESSION['usernameError'])) $_SESSION['usernameError'] = "";
if (!isset($_SESSION['firstnameError'])) $_SESSION['firstnameError'] = "";
if (!isset($_SESSION['lastnameError'])) $_SESSION['lastnameError'] = "";
if (!isset($_SESSION['passwordError'])) $_SESSION['passwordError'] = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign up to my silly projects</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script src="main.js" defer></script>
</head>

<body>
    <?php
    require_once realpath(__DIR__ . '/../vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $conn = new PDO("mysql:host=dirkdev.com;dbname=projects", $_ENV['USER'], $_ENV['PASS']);

    if (isset($_GET['submit'])) {
        ValidateForm($conn);
    }

    function ValidateForm($conn)
    {
        $sth = $conn->prepare("SELECT 1 FROM `projects`.`users` WHERE `username` = ?");
        $sth->execute([$_GET['username']]);

        if ($sth->fetchColumn()) {
            $_SESSION['usernameError'] = "alreadyExists";
            return;
        }

        $username = $_GET['username'];
        try {
            $sth = $conn->prepare("INSERT INTO `projects`.`users` (`username`, `first-name`, `last-name`, `password`) VALUES (?, ?, ?, ?)");
            $sth->execute([$_GET['username'], $_GET['first-name'], $_GET['last-name'], password_hash($_GET['password'], PASSWORD_BCRYPT)]);
        } catch (Exception) {
            header("Location: landing.php?username={$username}");
        }

        header("Location: landing.php?username={$username}");
    }
    ?>
    <h1 class="text-center mt-4 mb-5">Sign up</h1>
    <form autocomplete="off" class="position-relative d-flex flex-column text-center align-items-center w-50 mx-auto">
        <input type="text" style="display:none"><input type="password" style="display:none"><!--Prevent Firefox from autocompleting -->
        <input required id="username-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="username" placeholder="Username">
        <div id="username-error" class="text-danger text-start w-100 mw-40" style="display: <?= (isset($_SESSION['usernameError'])) ? "block" : "none" ?>">
            <?php
            switch ($_SESSION['usernameError']) {
                case "alreadyExists":
                    echo "A user with the username you entered already exists! Choose another username";
                    break;
            }
            ?>
        </div>
        <?php
        unset($_SESSION['usernameError']);
        ?>
        <input required id="firstname-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="first-name" placeholder="First name">
        <div id="firstname-error" class="text-danger text-start w-100 mw-40" style="display: <?= (isset($_SESSION['usernameError'])) ? "block" : "none" ?>">
            <?php
            switch ($_SESSION['firstnameError']) {
            }
            ?>
        </div>
        <?php
        unset($_SESSION['firstnameError']);
        ?>
        <input required id="lastname-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="last-name" placeholder="Last name">
        <div id="lastname-error" class="text-danger text-start w-100 mw-40" style="display: <?= (isset($_SESSION['usernameError'])) ? "block" : "none" ?>">
            <?php
            switch ($_SESSION['lastnameError']) {
            }
            ?>
        </div>
        <?php
        unset($_SESSION['lastnameError']);
        ?>
        <input required id="password-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="password" name="password" placeholder="Password">
        <div id="password-error" class="text-danger text-start w-100 mw-40" style="display: <?= (isset($_SESSION['usernameError'])) ? "block" : "none" ?>">
            <?php
            switch ($_SESSION['passwordError']) {
            }
            ?>
        </div>
        <?php
        unset($_SESSION['passwordError']);
        ?>

        <input type="submit" id="submit" name="submit" value="Sign up" class="input-group-submit p-2 px-4 mx-auto mt-3 mw-40 btn btn-primary cursor-pointer">
    </form>
</body>

</html>