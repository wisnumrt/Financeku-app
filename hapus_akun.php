<?php
session_start();
include('dbconn.php');

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];
$conn = getConnection();

// Hapus data pengguna dan terkait
$conn->query("DELETE FROM income WHERE userId = $userId");
$conn->query("DELETE FROM expense WHERE userId = $userId");
$conn->query("DELETE FROM budgets WHERE userId = $userId");
$conn->query("DELETE FROM users WHERE id = $userId");

// Hapus sesi
session_destroy();

header("Location: login.php");
exit();
?>
