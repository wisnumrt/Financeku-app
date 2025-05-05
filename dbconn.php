<?php
function getConnection()
{
    // membuat koneksi ke database system
    $dbServer = 'sql300.infinityfree.com';
    $dbUser = 'if0_38901134';
    $dbPass = '0506Sandi';
    $dbName = "if0_38901134_if0_financeku2";

    // membuat koneksi
    $conn = mysqli_connect($dbServer, $dbUser, $dbPass, $dbName);

    // mengecek apakah koneksi berhasil
    if (!$conn) {
        die('Koneksi gagal: ' . mysqli_connect_error());
    }

    return $conn;
}
?>
