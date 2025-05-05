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
    $expenseCategory = $_POST['expenseCategory'];
    $description = $_POST['description'];

    // Create a connection
    $conn = getConnection();

    // Prepare the SQL statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO expense (userId, date, amount, expenseCategory, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $userId, $date, $amount, $expenseCategory, $description);

    if ($stmt->execute()) {
        $status = 'ok';
    } else {
        $status = 'err';
    }

    $stmt->close();
    $conn->close();

    header('Location: dashboard.php?status=' . $status);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeku - Tambah Pengeluaran</title>
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
            position: fixed;
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
            text-align: center;
        }

        .field, .textarea-field {
            box-sizing: border-box;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 16px;
            gap: 10px;
            width: 245px;
            background: #FFFFFF;
            border: 1px solid #828282;
            box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }

        .field input, .field select, .textarea-field textarea {
            width: 100%;
            border: none;
            outline: none;
            font-size: 16px;
            color: #828282;
            font-family: 'Inter', sans-serif;
        }

        .textarea-field textarea {
            resize: none;
            height: 120px;
            padding: 12px;
            box-sizing: border-box;
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

        /* Smartphone kecil (≤ 480px) */
        @media screen and (max-width: 480px) {
            .head-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                padding: 16px;
            }

            .financeku {
                font-size: 24px;
                margin-left: 0;
            }

            .menu {
                width: 100%;
                justify-content: space-between;
                padding: 0 10px;
                flex-wrap: wrap;
                gap: 8px;
            }

            .logout {
                width: 100%;
                justify-content: center;
                padding: 10px;
                font-size: 14px;
                text-align: center;
            }

            .content {
                position: static;
                transform: none;
                padding: 20px 16px;
                width: 100%;
                box-sizing: border-box;
            }

            .content .title {
                font-size: 26px;
                text-align: center;
                line-height: 1.3;
            }

            form {
                width: 100%;
                padding: 0;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .field,
            .textarea-field,
            .add-button {
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
            }

            .add-button {
                padding: 12px;
                font-size: 16px;
                text-align: center;
            }
        }
        /* Smartphone sedang / Tablet kecil (≤ 768px) */
        @media screen and (max-width: 768px) {
            .content {
                position: static;
                transform: none;
                padding: 20px;
                width: 100%;
            }

            .content .title {
                font-size: 36px;
            }

            .field,
            .textarea-field {
                width: 100%;
            }

            form {
                padding: 0 20px;
            }

            .add-button {
                width: 100%;
            }

            .menu {
                flex-wrap: wrap;
                gap: 8px;
                justify-content: center;
            }
        }

        /* Tablet besar dan layar kecil lainnya (≤ 1024px) */
        @media screen and (max-width: 1024px) {
            .content {
                max-width: 600px;
                margin: 0 auto;
            }

            .field,
            .textarea-field {
                width: 100%;
            }

            .add-button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="head-bar">
            <div class="financeku">
                <a href="dashboard.php">Financeku.</a>
            </div>
            <div class="menu">
                <a href="halaman_tambah.php">
                    <img src="img/plus.png" alt="Add Icon">
                </a>
                <a href="halaman_laporan.php">
                    <img src="img/report.png" alt="Report Icon">
                </a>
                <button class="logout">
                    <a href="logout.php">Log out</a>
                </button>
            </div>
        </div>

        <div class="content">
            <form method="POST">
                <div class="title">Tambah Pengeluaran</div>
                <div class="field">
                    <input type="number" name="amount" placeholder="Nominal" required>
                </div>
                <div class="field">
                    <input type="text" name="expenseName" placeholder="Nama pengeluaran" required>
                </div>
                <div class="field">
                    <select name="expenseCategory" required>
                        <option value="" disabled selected>Kategori</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Hiburan">Hiburan</option>
                        <option value="Pendididikan">Pendidikan</option>
                        <option value="Rumah Tangga">Rumah Tangga</option>
                        <option value="Hutang">Hutang</option>
                        <option value="Kesehatan">Kesehatan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="textarea-field">
                    <textarea name="description" placeholder="Deskripsi" required></textarea>
                </div>
                <button type="submit" class="add-button">Tambahkan</button>
            </form>
        </div>
    </div>
</body>
</html>
