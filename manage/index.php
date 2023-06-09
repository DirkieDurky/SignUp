<?php
session_start();

require_once("db.php");
$conn = DB::getConn();

$username = $_SERVER['REDIRECT_USERNAME'];

//Check if the uid and the username align
$uidFromUsername = "";
if ($username != "") {
    $sth = $conn->prepare("SELECT `id` FROM `users` WHERE `username` = ?");
    $sth->execute([$username]);

    $row = $sth->fetch();
    if ($row) $uidFromUsername = $row['id'];
}

if ($uidFromUsername != "" && $uidFromUsername == $_SESSION['user-id']) {
    require "manage.php";
} else {
    require "signin.php";
}
