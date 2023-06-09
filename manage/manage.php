<?php
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
    <link rel="stylesheet" href="style.css">
    <script src="main.js" defer></script>
</head>

<body>
    <h1 class="mb-5">Manage your account</h1>
</body>

</html>