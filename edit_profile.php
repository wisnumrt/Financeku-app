<?php
session_start();
include('dbconn.php');

if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}

$conn = getConnection();
$userId = $_SESSION['userId'];
$message = '';

// Ambil data user saat ini
$query = "SELECT nickname, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($nickname) && !empty($email)) {
        // Ubah password jika diisi
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $update = "UPDATE users SET nickname = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("sssi", $nickname, $email, $hashedPassword, $userId);
        } else {
            $update = "UPDATE users SET nickname = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("ssi", $nickname, $email, $userId);
        }

        if ($stmt->execute()) {
            $_SESSION['nickname'] = $nickname;
            $message = "Profil berhasil diperbarui.";
        } else {
            $message = "Terjadi kesalahan saat memperbarui profil.";
        }
        $stmt->close();
    } else {
        $message = "Nickname dan email tidak boleh kosong.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            padding: 40px;
            background: #f4f4f4;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            font-weight: 500;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 6px 0 16px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background-color: #000;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        .message {
            margin-top: 10px;
            font-size: 14px;
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profil</h2>
        <form method="post">
            <label for="nickname">Nickname:</label>
            <input type="text" id="nickname" name="nickname" value="<?php echo htmlspecialchars($user['nickname']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">Password Baru (opsional):</label>
            <input type="password" id="password" name="password">

            <button type="submit">Simpan Perubahan</button>
             <!-- Tombol kembali -->
            <button type="button" onclick="window.location.href='dashboard.php'" style="margin-top: 10px; background-color: #ccc; color: #000;">
                Kembali
            </button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
