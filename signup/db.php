<?php
require_once realpath(__DIR__ . '/../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class DB
{
    static $conn = null;

    static function getConn()
    {
        if (DB::$conn == null) {
            DB::$conn = new PDO("mysql:host=dirkdev.com;dbname=projects", $_ENV['USER'], $_ENV['PASS']);
        }

        return DB::$conn;
    }
}
