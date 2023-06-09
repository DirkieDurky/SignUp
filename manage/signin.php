<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage your account</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="public/style.css">
    <script src="main.js" defer></script>
</head>
<?php
if (!isset($_SESSION['username-error'])) $_SESSION['username-error'] = "";
if (!isset($_SESSION['firstname-error'])) $_SESSION['firstname-error'] = "";
if (!isset($_SESSION['lastname-error'])) $_SESSION['lastname-error'] = "";
if (!isset($_SESSION['password-error'])) $_SESSION['password-error'] = "";
?>

<body>
    <?php
    require_once("db.php");
    $conn = DB::getConn();

    if (isset($_POST['submit'])) {
        ValidateForm($conn);
    }

    function ValidateForm($conn)
    {
        $username = $_POST['username'];

        $sth = $conn->prepare("SELECT `id`,`password` FROM `users` WHERE `username` = ?");
        $sth->execute([$username]);

        $row = $sth->fetch();

        if (!$row) {
            $_SESSION['username-error'] = "noUserFound";
            return;
        }

        if (!password_verify($_POST['password'], $row['password'])) {
            $_SESSION['password-error'] = "incorrect";
            return;
        }

        $_SESSION['user-id'] = $row['id'];
        echo $_SESSION['user-id'];
        header("Location: {$username}");
    }
    ?>
    <h1 class="text-center mt-4 mb-5">Sign in to manage your account</h1>
    <form method="post" autocomplete="off" class="position-relative d-flex flex-column text-center align-items-center w-50 mx-auto">
        <input type="text" style="display:none"><input type="password" style="display:none"><!--Prevent Firefox from autocompleting -->
        <input required id="username-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="text" name="username" placeholder="Username">
        <div id="username-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['username-error']) {
                case "noUserFound":
            ?>
                    There is no account that uses this username. Would like to <a href="https://accounts.dirkdev.com/signup">create one</a>?
            <?php
                    break;
            }
            ?>
        </div>
        <?php
        unset($_SESSION['username-error']);
        ?>
        <input required id="password-input-field" class="input-group-text form-control text-start p-2 mt-4 mw-40" type="password" name="password" placeholder="Password">
        <div id="password-error" class="text-danger text-start w-100 mw-40">
            <?php
            switch ($_SESSION['password-error']) {
                case "incorrect":
                    echo "Combination of username and password was incorrect";
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