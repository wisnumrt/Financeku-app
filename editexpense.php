<?php
session_start();
include('dbconn.php');

$status = '';
$result = '';
$data = null;

// Check if there is a GET variable
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $idToUpdate = $_GET['id'];
        $conn = getConnection();
        
        if ($conn) {
            $stmt = $conn->prepare("SELECT * FROM expense WHERE id = ?");
            $stmt->bind_param("i", $idToUpdate);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
            }

            $stmt->close();
            $conn->close();
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['userId'])) {
        // Get userId from session
        $userId = $_SESSION['userId'];
        // Get form data
        $date = $_POST['date'];
        $amount = $_POST['amount'];
        $expenseCategory = $_POST['expenseCategory'];
        $description = $_POST['description'];
        $expenseId = $_POST['expenseId'];

        // Create a connection
        $conn = getConnection();

        if ($conn) {
            // Prepare the SQL statement to avoid SQL injection
            $stmt = $conn->prepare("UPDATE expense SET date = ?, amount = ?, expenseCategory = ?, description = ? WHERE id = ? AND userId = ?");
            $stmt->bind_param("sdssii", $date, $amount, $expenseCategory, $description, $expenseId, $userId);

            if ($stmt->execute()) {
                $_SESSION['update_message'] = 'Data berhasil diperbarui.';
                header('Location: halaman_laporan.php');
                exit();
            } else {
                $_SESSION['update_message'] = 'Data gagal diperbarui.';
            }

            $stmt->close();
            $conn->close();
        } else {
            echo "Connection error: " . mysqli_connect_error();
        }
    } else {
        echo "User not authenticated.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeku - Update Pengeluaran</title>
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

        .title2 {
            font-size: 35px;
            font-weight: 700;
            text-decoration: none;
        }

        .title2 a {
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
        
        @media screen and (max-width: 480px) {
            .head-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 0 16px;
                gap: 12px;
            }

            .financeku {
                font-size: 28px;
                margin-left: 0;
            }

            .menu {
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-start;
            }

            .logout {
                width: 100%;
                justify-content: center;
                font-size: 14px;
                padding: 10px;
            }

            .content {
                position: static;
                transform: none;
                padding: 16px;
                width: 100%;
                box-sizing: border-box;
            }

            .content .title {
                font-size: 30px;
                line-height: 1.3;
                text-align: center;
            }

            form {
                width: 100%;
                padding: 0;
            }

            .field, .textarea-field, .add-button {
                width: 100%;
                max-width: 100%;
                padding: 12px;
            }

            .add-button {
                height: auto;
                font-size: 16px;
                padding: 12px;
            }
        }

        @media screen and (max-width: 768px) {
            .head-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 0 16px;
                gap: 12px;
            }

            .financeku {
                font-size: 28px;
                margin-left: 0;
            }

            .menu {
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-start;
            }

            .logout {
                width: 100%;
                justify-content: center;
                font-size: 14px;
                padding: 10px;
            }
        }

        @media screen and (max-width: 1024px) {
            .head-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 0 16px;
                gap: 12px;
            }

            .financeku {
                font-size: 28px;
                margin-left: 0;
            }

            .menu {
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-start;
            }

            .logout {
                width: 100%;
                justify-content: center;
                font-size: 14px;
                padding: 10px;
            }
        }

        @media screen and (max-width: 1200px) {
            .head-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 0 16px;
                gap: 12px;
            }

            .financeku {
                font-size: 28px;
                margin-left: 0;
            }

            .menu {
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-start;
            }

            .logout {
                width: 100%;
                justify-content: center;
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="head-bar">
            <div class="title2">
                <a href="dashboard.php">Financeku.</a>
            </div>
            <div class="menu">
                <a href="halaman_tambah.php">
                    <img src="img/plus.png" alt="Add Icon">
                </a>
                <a href="halaman_laporan.php">
                    <img src="img/report.png" alt="Report Icon">
                </a>
                <a href="halaman_pengaturan.php">
                    <img src="img/config.png" alt="Settings Icon">
                </a>
                <button class="logout">
                    <a href="logout.php">Log out</a>
                </button>
            </div>
        </div>

        <div class="content">
            <form action="editexpense.php" method="POST">
                <?php if ($data): ?>
                <div class="title">Update Pengeluaran</div>
                <input type="hidden" name="expenseId" value="<?php echo $data['id']; ?>">
                <div class="field">
                    <input type="date" name="date" value="<?php echo $data['date']; ?>" placeholder="Tanggal" required>
                </div>
                <div class="field">
                    <select name="expenseCategory" required>
                        <option value="" disabled>Kategori</option>
                        <option value="Makanan" <?php if($data['expenseCategory'] == 'Makanan') echo 'selected'; ?>>Makanan</option>
                        <option value="Transportasi" <?php if($data['expenseCategory'] == 'Transportasi') echo 'selected'; ?>>Transportasi</option>
                        <option value="Hiburan" <?php if($data['expenseCategory'] == 'Hiburan') echo 'selected'; ?>>Hiburan</option>
                        <option value="Pendidikan" <?php if($data['expenseCategory'] == 'Pendidikan') echo 'selected'; ?>>Pendidikan</option>
                        <option value="Rumah Tangga" <?php if($data['expenseCategory'] == 'Rumah Tangga') echo 'selected'; ?>>Rumah Tangga</option>
                        <option value="Hutang" <?php if($data['expenseCategory'] == 'Hutang') echo 'selected'; ?>>Hutang</option>
                        <option value="Kesehatan" <?php if($data['expenseCategory'] == 'Kesehatan') echo 'selected'; ?>>Kesehatan</option>
                        <option value="Lainnya" <?php if($data['expenseCategory'] == 'Lainnya') echo 'selected'; ?>>Lainnya</option>
                    </select>
                </div>
                <div class="textarea-field">
                    <textarea name="description" placeholder="Deskripsi" required><?php echo $data['description']; ?></textarea>
                </div>
                <div class="field">
                    <input type="number" name="amount" value="<?php echo $data['amount']; ?>" placeholder="Jumlah" required>
                </div>
                <button class="add-button" type="submit">Update</button>
                <?php else: ?>
                <div class="title">Pengeluaran tidak ditemukan</div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
