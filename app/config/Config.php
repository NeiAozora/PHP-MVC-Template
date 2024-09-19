<?php

// Pengaturan mode deployment, Jika butuh deploy ke web
define('WEB_DOMAIN_MODE', false);

// Mendefinisikan konstanta untuk views dan koneksi database
define("ROOT", dirname(dirname(__DIR__)) . "/");
define("ROOT_DIRECTORY_NAME", basename(ROOT));
define("VIEWS", ROOT . "views/");

// Konfigurasi
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'NAMA TABELMU');

// Pengaturan URL dasar
$host = $_SERVER['HTTP_HOST'];
if (WEB_DOMAIN_MODE) {
    define('BASEURL', "https://www.example.com"); // Masukkan URL website saat live
} else {
    define('BASEURL', "http://localhost/" . ROOT_DIRECTORY_NAME . "/");
}

// Koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengambil zona waktu sesi MySQL
$result = $conn->query("SELECT @@session.time_zone AS time_zone");
$row = $result->fetch_assoc();
$mysqlTimeZone = $row['time_zone'];

// Mengatur zona waktu PHP berdasarkan zona waktu MySQL
if ($mysqlTimeZone !== 'SYSTEM') {
    date_default_timezone_set($mysqlTimeZone);
} else {
    date_default_timezone_set(ini_get('date.timezone')); // Menggunakan zona waktu sistem server
}
?>
