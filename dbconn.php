<?php
function getConnection()
{
    // membuat koneksi ke database system
    $dbServer = 'localhost';
    $dbUser = 'root';
    $dbPass = '';
    $dbName = "dbfinanceku";;

    // membuat koneksi
    $conn = mysqli_connect($dbServer, $dbUser, $dbPass, $dbName);

    // mengecek apakah koneksi berhasil
    if (!$conn) {
        die('Koneksi gagal: ' . mysqli_connect_error());
    }

    return $conn;
}
?>
