<?php
require_once("db.php");
$conn = DB::getConn();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Sign up to my silly projects</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form class="d-flex flex-column text-center align-items-center">
        <?php
        $sth = $conn->prepare("SELECT 1 FROM `projects`.`users` WHERE `username` = ?");
        $sth->execute([$_GET['username']]);

        if ($sth->fetch()) {
        ?>
            <p class="text-success mt-4">Your account was successfully created! You can manage it at https://account.dirkdev.com/manage/<?= $_GET['username'] ?></p>

            <a href="index.php" class="p-2 px-4 mx-auto mt-3 mw-40 btn btn-secondary">Return</a>
        <?php
        } else {
        ?>
            <p class="text-danger mt-4">Oops! It looks like something went wrong creating your account. Try again, or if it keeps not working tell Dirk he should fix his shit</p>

            <a href="index.php" class="p-2 px-4 mx-auto mt-3 mw-40 btn btn-secondary">Try again</a>
        <?php
        }
        ?>
    </form>

</body>

</html>