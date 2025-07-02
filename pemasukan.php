<?php
session_start();
include('dbconn.php');

$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get userId from session
    $userId = $_SESSION['userId'];
    // Get current date
    $date = date('Y-m-d');
    $amount = $_POST['amount'];
    $incomeSource = $_POST['incomeSource'];

    // Create a connection
    $conn = getConnection();

    // Prepare the SQL statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO income (userId, date, amount, incomeSource) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $userId, $date, $amount, $incomeSource);

    if ($stmt->execute()) {
        $_SESSION['status'] = 'Data berhasil ditambahkan!';
    } else {
        $_SESSION['status'] = 'Data gagal ditambahkan';
    }

    $stmt->close();
    $conn->close();

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemasukan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #FFFFFF 0%, #D0D0D0 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .dashboard {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .head-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0;
            margin-bottom: 20px;
            margin-top: 15px;
        }

        .financeku {
            font-size: 35px;
            font-weight: 700;
            color: #000000;
            margin-left: 20px;
        }

        .financeku a {
            text-decoration: none;
            color: #000000;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu img {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        .logout {
            display: flex;
            align-items: center;
            cursor: pointer;
            margin-right: 20px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 15px;
            padding: 8px 16px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
        }

        .dropdown-setting {
            position: relative;
            display: inline-block;
        }

        .dropdown-setting img {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 140px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 6px;
            overflow: hidden;
        }

        .dropdown-content a {
            color: black;
            padding: 10px 14px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .logout a {
            color: #fff;
            text-decoration: none;
        }

        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            position: fixed; /* Fixed positioning */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .content .title {
            font-weight: 700;
            font-size: 45px;
            line-height: 58px;
            letter-spacing: -0.02em;
            color: #000000;
        }

        .field {
            box-sizing: border-box;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 16px;
            gap: 10px;
            width: 245px;
            height: 50px;
            background: #FFFFFF;
            border: 1px solid #828282;
            box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }

        .field input {
            width: 100%;
            border: none;
            outline: none;
            font-size: 16px;
            color: #828282;
        }

        .add-button {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 115px;
            height: 38px;
            background: #000000;
            box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 16px;
            line-height: 100%;
            color: #FFFFFF;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .head-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                margin-left: 10px;
            }

            .financeku {
                margin-left: 0;
                font-size: 28px;
            }

            .menu {
                gap: 12px;
                margin-left: 0;
            }

            .logout {
                margin-right: 0;
                padding: 6px 12px;
                font-size: 14px;
            }

            .content .title {
                font-size: 32px;
                text-align: center;
            }

            .field {
                width: 90%;
                max-width: 300px;
            }

            .add-button {
                width: 90%;
                max-width: 150px;
            }

            form {
                width: 100%;
                padding: 0 10px;
            }
        }

        @media (max-width: 480px) {
            .content .title {
                font-size: 26px;
            }

            .field input {
                font-size: 14px;
            }

            .add-button {
                font-size: 14px;
                height: 34px;
            }
        }
    </style>
</head>
<body>
<?php
    if ($status == 'ok') {
        echo '<div class="success">SUKSES!, Data pemasukan berhasil ditambahkan</div>';
    } elseif ($status == 'err') {
        echo '<div class="error">ERROR!, Data gagal ditambahkan</div>';
    }
?>
    <div class="dashboard">
        <div class="head-bar">
            <div class="financeku">
                <a href="dashboard.php">Financeku.</a>
            </div>
            <div class="menu">
                <a href="aturBudget.php">
                    <img src="img/plus.png" alt="Add Icon">
                </a>
                <a href="halaman_laporan.php">
                    <img src="img/report.png" alt="Report Icon">
                </a>
                <!-- Tambahkan di sini -->
                <div class="dropdown-setting">
                    <img src="img/setting.png" alt="Setting Icon" id="setting-icon">
                    <div class="dropdown-content" id="dropdown-menu">
                        <a href="edit_profile.php">Edit Profile</a>
                        <a href="hapus_akun.php" onclick="return confirm('Apakah Anda yakin ingin menghapus akun?');">Hapus Akun</a>
                    </div>
                </div>
                <!-- Sampai sini -->
                <button class="logout">
                    <a href="logout.php">Log out</a>
                </button>
            </div>
        </div>

        <div class="content">
            <form action="pemasukan.php" method="POST">
                <div class="title">Tambah Pemasukan</div>
                <div class="field">
                    <input type="text" name="amount" placeholder="Nominal" required>
                </div>
                <div class="field">
                    <input type="text" name="incomeSource" placeholder="Sumber" required>
                </div>
                <button type="submit" class="add-button">Tambahkan</button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById("setting-icon").addEventListener("click", function(event) {
            event.stopPropagation();
            const menu = document.getElementById("dropdown-menu");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        });

        document.addEventListener("click", function(event) {
            const menu = document.getElementById("dropdown-menu");
            if (menu && !menu.contains(event.target)) {
                menu.style.display = "none";
            }
        });
    </script>
</body>
</html>
