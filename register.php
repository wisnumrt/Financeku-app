<?php
session_start();
require 'dbconn.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $fullName = $_POST['fullName'];
    $nickname = $_POST['nickname'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Ensure correct variable name

    $conn = getConnection();

    // Check if email already exists
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists
        $error = 'Email already registered. Please use a different email or login.';
    } else {
        // Insert new user
        $stmt = $conn->prepare('INSERT INTO users (username, fullName, nickname, email, password) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $username, $fullName, $nickname, $email, $password); // Ensure correct variable name

        if ($stmt->execute()) {
            // Registration successful, redirect to login page
            header('Location: login.php');
            exit;
        } else {
            // Error in registration
            $error = 'Error in registration. Please try again later.';
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeku - Sign Up</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <div class="container">
        <h1><a href="login.php">Financeku.</a></h1>
        <p>Teman Financemu!</p>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="register.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="fullName" placeholder="Nama lengkap" required>
            <input type="text" name="nickname" placeholder="Nama panggilan" required>
            <input type="email" name="email" placeholder="E-mail" required>
            
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">Show</button>
            </div>

            <div class="center-button">
                <button type="submit">Sign Up</button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const toggleBtn = document.querySelector(".toggle-password");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleBtn.textContent = "Hide";
            } else {
                passwordInput.type = "password";
                toggleBtn.textContent = "Show";
            }
        }
    </script>
</body>
</html>