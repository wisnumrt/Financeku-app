<?php
session_start();
include('dbconn.php'); // Ensure this file path is correct

$status = '';
$result = '';
$data = null;

// Check if there is a GET variable
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // Query SQL
        $idToDelete = $_GET['id'];
        
        // Create a connection
        $conn = getConnection(); // Ensure this function is defined in dbconn.php
        
        if ($conn) {
            $stmt = $conn->prepare("DELETE FROM income WHERE id = ?");
            $stmt->bind_param("i", $idToDelete);
            
            if ($stmt->execute()) {
                $_SESSION['delete_message'] = 'Data berhasil dihapus.';
                $status = 'ok';
            } else {
                $_SESSION['delete_message'] = 'Data gagal dihapus.';
                $status = 'err';
            }
            
            $stmt->close();
            $conn->close();
        } else {
            $_SESSION['delete_message'] = 'Koneksi database gagal.';
            $status = 'err';
        }

        header('Location: halaman_laporan.php?status=' . $status);
        exit();
    }
}
?>
