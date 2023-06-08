<?php
if (!isset($_SESSION['usernameError'])) $_SESSION['usernameError'] = "";
if (!isset($_SESSION['firstnameError'])) $_SESSION['firstnameError'] = "";
if (!isset($_SESSION['lastnameError'])) $_SESSION['lastnameError'] = "";
if (!isset($_SESSION['passwordError'])) $_SESSION['passwordError'] = "";
?>
<!DOCTYPE html>
<html>

<head>
    <title>Sign up to my silly projects</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script src="main.js" defer></script>
</head>

<body>
    <?php
    require_once realpath(__DIR__ . '/vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    if (isset($_GET['submit'])) {
        ValidateForm();
    }

    function ValidateForm()
    {
        $conn = new PDO("mysql:host=dirkdev.com;dbname=projects", $_ENV['USER'], $_ENV['PASS']);

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
    <form class="position-relative d-flex flex-column text-center align-items-center w-50 mx-auto">
        <input required autocomplete="off" id="username-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="username" placeholder="Username">
        <?php if ($_SESSION['usernameError']) { ?>
            <div id="username-error" class="text-danger text-start w-100 mw-40">
                <?php
                switch ($_SESSION['usernameError']) {
                    case "alreadyExists":
                        echo "A user with this username already exists! Choose another username";
                        break;
                }
                ?>
            </div>
        <?php
            unset($_SESSION['usernameError']);
        } ?>
        <input required autocomplete="off" id="first-name-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="first-name" placeholder="First name">
        <input required autocomplete="off" id="last-name-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="last-name" placeholder="Last name">
        <input required autocomplete="off" id="password-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="password" name="password" placeholder="Password">

        <input type="submit" id="submit" name="submit" value="Sign up" class="input-group-submit p-2 w-50 mt-4 cursor-pointer mw-40">
    </form>
</body>

</html>