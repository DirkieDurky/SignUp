<?php
session_start();

require_once("db.php");
$conn = DB::getConn();

if (!isset($_SESSION['username-error'])) $_SESSION['username-error'] = "";
if (!isset($_SESSION['firstname-error'])) $_SESSION['firstname-error'] = "";
if (!isset($_SESSION['lastname-error'])) $_SESSION['lastname-error'] = "";
if (!isset($_SESSION['password-error'])) $_SESSION['password-error'] = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign up to my silly projects</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="public/style.css">
    <script src="main.js" defer></script>
</head>

<body>
    <?php
    if (isset($_POST['submit'])) {
        ValidateForm($conn);
    }

    function ValidateForm($conn)
    {
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
    ?>
    <h1 class="text-center mt-4 mb-5">Sign up</h1>
    <form method="post" autocomplete="off" class="position-relative d-flex flex-column text-center align-items-center w-50 mx-auto">
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
        <input required id="password-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="password" name="password" placeholder="Password">
        <div id="password-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['password-error']) {
            }
            ?>
        </div>
        <?php
        unset($_SESSION['password-error']);
        ?>

        <input type="submit" id="submit" name="submit" value="Sign up" class="input-group-submit p-2 px-4 mx-auto mt-3 mw-40 btn btn-primary cursor-pointer">
    </form>
</body>

</html>