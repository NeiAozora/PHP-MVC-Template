<?php

// Pengaturan mode deployment, Jika butuh deploy ke web
define('WEB_DOMAIN_MODE', false);

// Mendefinisikan konstanta untuk views dan koneksi database
define("ROOT", dirname(dirname(__DIR__)) . "/");
define("ROOT_DIRECTORY_NAME", basename(ROOT));
define("VIEWS", ROOT . "views/");

// Konfigurasi Database 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'NAMA_DB');

// Pengaturan URL dasar
$host = $_SERVER['HTTP_HOST'];
if (WEB_DOMAIN_MODE) {
    define('BASEURL', "https://www.example.com"); // Masukkan URL website saat live
} else {
    define('BASEURL', "http://localhost/" . ROOT_DIRECTORY_NAME . "/");
}

// Koneksi ke database dengan try-catch manual
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Jika ada error koneksi
    if ($conn->connect_error) {
        throw new Exception("Koneksi gagal: " . $conn->connect_error);
    }

    // Mengecek apakah database yang dihubungkan ada
    $db_selected = $conn->select_db(DB_NAME);
    if (!$db_selected) {
        echo '<h3>Warning: database ' . DB_NAME . ' tidak ada!</h3>';
    }

    // Mengambil zona waktu sesi MySQL
    $result = $conn->query("SELECT @@session.time_zone AS time_zone");
    if ($result) {
        $row = $result->fetch_assoc();
        $mysqlTimeZone = $row['time_zone'];

        // Mengatur zona waktu PHP berdasarkan zona waktu MySQL
        if ($mysqlTimeZone !== 'SYSTEM') {
            date_default_timezone_set($mysqlTimeZone);
        } else {
            date_default_timezone_set(ini_get('date.timezone')); // Menggunakan zona waktu sistem server
        }
    }

} catch (Exception $e) {
    // Menangkap dan mencetak pesan error
    // echo '<h5>Database Error: ' . $e->getMessage() . '</h5>';
}

?>
