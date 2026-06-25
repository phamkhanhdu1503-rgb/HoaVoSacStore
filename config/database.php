<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "HoaVoSacStore";

$db = mysqli_connect($host, $user, $password, $database);

if (!$db) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}