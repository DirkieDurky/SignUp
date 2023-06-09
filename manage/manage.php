<?php

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

$generator = new ComputerPasswordGenerator();

require_once("db.php");
$conn = DB::getConn();

$sth = $conn->prepare("SELECT `username` FROM `users` WHERE `id` = ?");
$sth->execute([$_SESSION['user-id']]);

$username = $sth->fetch()['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage your account</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css">
    <link rel="stylesheet" href="public/style.css">
    <script src="public/main.js" defer></script>
</head>

<?php
if (!isset($_SESSION['username-error'])) $_SESSION['username-error'] = "";
if (!isset($_SESSION['firstname-error'])) $_SESSION['firstname-error'] = "";
if (!isset($_SESSION['lastname-error'])) $_SESSION['lastname-error'] = "";

if (!isset($_SESSION['apikey-error'])) $_SESSION['apikey-error'] = "";

if (!isset($_SESSION['old-password-error'])) $_SESSION['old-password-error'] = "";
if (!isset($_SESSION['new-password-error'])) $_SESSION['new-password-error'] = "";
if (!isset($_SESSION['confirm-password-error'])) $_SESSION['confirm-password-error'] = "";

$apiKey = "";
?>

<body>
    <?php
    if (isset($_POST['submit'])) {
        ValidateForm();
    }

    echo isset($_POST['generate-api-key']);
    if (isset($_POST['generate-api-key'])) {
        $generator
            ->setLength(100)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, true);

        $apiKey = $generator->generatePassword();

        $sth = $conn->prepare("UPDATE `projects`.`users` SET `api-key` = ? WHERE `id` = ?");
        $sth->execute([password_hash($apiKey, PASSWORD_BCRYPT), $_SESSION['user-id']]);

        unset($_POST['generate-api-key']);
    }

    if (isset($_POST['password-submit'])) {
        ValidatePasswordForm();
    }

    function ValidateForm()
    {
        global $conn;

        $sth = $conn->prepare("SELECT 1 FROM `projects`.`users` WHERE `username` = ?");
        $sth->execute([$_POST['username']]);

        if ($sth->fetchColumn()) {
            $_SESSION['username-error'] = "alreadyExists";
            return;
        }

        $username = $_POST['username'];
        try {
            $sth = $conn->prepare("INSERT INTO `projects`.`users` (`username`, `first-name`, `last-name`, `password`) VALUES (?, ?, ?, ?)");
            $sth->execute([$_POST['username'], $_POST['first-name'], $_POST['last-name'], password_hash($_POST['password'], PASSWORD_BCRYPT)]);
        } catch (Exception) {
            header("Location: landing.php?username={$username}");
        }

        header("Location: landing.php?username={$username}");
    }

    function ValidatePasswordForm()
    {
        global $conn;

        $sth = $conn->prepare("SELECT `password` FROM `users` WHERE `id` = ?");
        $sth->execute([$_SESSION['userID']]);

        $row = $sth->fetch();

        if (!password_verify($_POST['password'], $row['password'])) {
            $_SESSION['password-error'] = "incorrect";
            return;
        }
    }
    ?>
    <h1 class="text-center mt-4 mb-5">Manage your account</h1>
    <h2 class="text-center mt-4">Your credentials</h2>
    <form method="post" autocomplete="off" class="position-relative d-flex flex-column text-center align-items-center w-50 mx-auto mb-5">
        <input type="text" style="display:none"><input type="password" style="display:none"><!--Prevent Firefox from autocompleting -->
        <input required id="username-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="username" placeholder="Username">
        <div id="username-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['username-error']) {
                case "alreadyExists":
                    echo "A user with the username you entered already exists! Choose another username";
                    break;
            }
            ?>
        </div>
        <?php
        unset($_SESSION['username-error']);
        ?>
        <input required id="firstname-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="first-name" placeholder="First name">
        <div id="firstname-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['firstname-error']) {
            }
            ?>
        </div>
        <?php
        unset($_SESSION['firstname-error']);
        ?>
        <input required id="lastname-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="last-name" placeholder="Last name">
        <div id="lastname-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['lastname-error']) {
            }
            ?>
        </div>
        <?php
        unset($_SESSION['lastname-error']);
        ?>
        <input type="submit" id="submit" name="submit" value="Submit changes" class="input-group-submit p-2 px-4 mx-auto mt-4 mw-40 btn btn-primary cursor-pointer">
    </form>
    <form method="post" autocomplete="off" class="position-relative d-flex flex-column text-center align-items-center w-50 mx-auto mb-5">
        <h2 class="text-center mt-4">Your API-key</h2>
        <input type="submit" id="generate-api-key" name="generate-api-key" value="Generate API-key" class="input-group-submit p-2 px-4 mx-auto mt-4 mw-40 btn btn-primary cursor-pointer">
        <?php
        if ($apiKey != "") {
        ?>
            <div class="d-flex flex-column content-card-block mw-40">
                <span class="mt-4 d-block">
                    This is your API-key. You can use it for any of my projects that require one.<br>
                    Make sure you copy it and keep it somewhere safe, because if anyone else gets this API-key,
                    they can make API requests as if they are you!<br>
                    Make sure you store it now, because I won't show it to you again. It will disappear after you reload the page.
                </span>
                <div class="content-card-input mt-4">
                    <input readonly type="text" id="api-key" class="" value="<?= $apiKey ?>">
                </div>
                <span id="copy-api-key" class="mt-2 text-start copy-btn">Click to copy</span>
            </div>
            <!-- <div class="container">
                    <div class="input-group">
                        <span id="copyButton" class="input-group-addon btn" title="Click to copy">
                            <i class="fa fa-clipboard" aria-hidden="true"></i>
                        </span>
                        <input type="text" id="copyTarget" class="form-control" value="<?= $apiKey ?>">
                    </div>
                    <span class="text-center w-100 copied">Copied!</span>
                </div> -->
        <?php
            $apiKey = "";
        }
        ?>
        <div id="apikey-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['apikey-error']) {
            }
            ?>
        </div>
        <?php
        unset($_SESSION['apikey-error']);
        ?>
    </form>
    <form method="post" autocomplete="off" class="position-relative d-flex flex-column text-center align-items-center w-50 mx-auto mb-5">
        <h2 class="text-center mt-4">Change your password</h2>
        <input required id="old-password-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="password" name="old-password" placeholder="Current password">
        <div id="old-password-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['old-password-error']) {
                case "incorrect":
                    echo "This password is incorrect";
                    break;
            }
            ?>
        </div>
        <?php
        unset($_SESSION['password-error']);
        ?>
        <input required id="new-password-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="password" name="new-password" placeholder="New password">
        <div id="new-password-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['new-password-error']) {
            }
            ?>
        </div>
        <?php
        unset($_SESSION['password-error']);
        ?>
        <input required id="confirm-password-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="password" name="confirm-password" placeholder="Confirm password">
        <div id="confirm-password-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['confirm-password-error']) {
            }
            ?>
        </div>
        <?php
        unset($_SESSION['confirm-password-error']);
        ?>
        <input type="submit" id="submit" name="password-submit" value="Change password" class="input-group-submit p-2 px-4 mx-auto mt-4 mw-40 btn btn-primary cursor-pointer">
    </form>
</body>

</html>