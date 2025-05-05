<?php
session_start();
include('dbconn.php');

if (!isset($_SESSION['userId']) || !isset($_SESSION['nickname'])) {
    header('Location: login.php');
    exit();
}

$nickname = $_SESSION['nickname'];
$userId = $_SESSION['userId'];

$conn = getConnection();

$currentYear = date('Y');
$currentMonth = date('n');

// Ensure ending_balance column exists in budgets table
$conn->query("ALTER TABLE budgets ADD COLUMN IF NOT EXISTS ending_balance DECIMAL(10,2) DEFAULT 0");

// Check if there's a budget entry for the current month
$checkBudget = "SELECT id, amount, ending_balance FROM budgets WHERE userId = ? AND year = ? AND month = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($checkBudget);
$stmt->bind_param("iii", $userId, $currentYear, $currentMonth);
$stmt->execute();
$result = $stmt->get_result();
$currentMonthData = $result->fetch_assoc();
$stmt->close();

// Initialize variables
$currentBudget = 0.00;
$saldoAkhir = 0.00;

// Check if we need to create a new month entry
if (!$currentMonthData) {
    // Get the previous month's ending balance if any
    $lastMonth = $currentMonth - 1;
    $lastYear = $currentYear;
    if ($lastMonth == 0) {
        $lastMonth = 12;
        $lastYear--;
    }

    $lastQuery = "SELECT ending_balance FROM budgets WHERE userId = ? AND year = ? AND month = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($lastQuery);
    $stmt->bind_param("iii", $userId, $lastYear, $lastMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $lastRow = $result->fetch_assoc();
    $previousEnding = isset($lastRow['ending_balance']) ? $lastRow['ending_balance'] : 0.00;
    $stmt->close();

    // Create a new month entry with zero budget but carrying over the previous ending balance
    $insertQuery = "INSERT INTO budgets (userId, year, month, amount, ending_balance, last_updated) VALUES (?, ?, ?, 0, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iiid", $userId, $currentYear, $currentMonth, $previousEnding);
    $stmt->execute();
    $stmt->close();
    
    // For a new month, saldo awal is 0, but saldo akhir carries the previous month's ending balance
    $currentBudget = 0.00;
    $saldoAkhir = $previousEnding;
} else {
    // Existing month - use the stored values
    $currentBudget = $currentMonthData['amount'];
    $saldoAkhir = $currentMonthData['ending_balance'];
}

// Handle adding or updating budget from form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['budget'])) {
    $newBudget = floatval($_POST['budget']);
    
    // Get previous month's ending balance (if any and if it hasn't been accounted for yet)
    $lastMonth = $currentMonth - 1;
    $lastYear = $currentYear;
    if ($lastMonth == 0) {
        $lastMonth = 12;
        $lastYear--;
    }
    
    $lastQuery = "SELECT ending_balance FROM budgets WHERE userId = ? AND year = ? AND month = ?";
    $stmt = $conn->prepare($lastQuery);
    $stmt->bind_param("iii", $userId, $lastYear, $lastMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $lastRow = $result->fetch_assoc();
    $previousEnding = isset($lastRow['ending_balance']) ? $lastRow['ending_balance'] : 0.00;
    $stmt->close();
    
    // Check if entry exists before updating
    $checkEntry = "SELECT id FROM budgets WHERE userId = ? AND year = ? AND month = ?";
    $stmt = $conn->prepare($checkEntry);
    $stmt->bind_param("iii", $userId, $currentYear, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $entryExists = $result->num_rows > 0;
    $stmt->close();
    
    if ($entryExists) {
        // Update existing budget entry
        $updateBudgetQuery = "UPDATE budgets SET amount = ?, last_updated = NOW() WHERE userId = ? AND year = ? AND month = ?";
        $stmt = $conn->prepare($updateBudgetQuery);
        $stmt->bind_param("diii", $newBudget, $userId, $currentYear, $currentMonth);
        $stmt->execute();
        $stmt->close();
    } else {
        // Create new budget entry (should not happen here, but just in case)
        $insertQuery = "INSERT INTO budgets (userId, year, month, amount, ending_balance, last_updated) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        $saldoAkhir = $newBudget + $previousEnding;
        $stmt->bind_param("iiidd", $userId, $currentYear, $currentMonth, $newBudget, $saldoAkhir);
        $stmt->execute();
        $stmt->close();
    }
    
    // Update current budget for display
    $currentBudget = $newBudget;
}

// Get income for current month
$incomeQuery = "SELECT SUM(amount) AS totalIncome FROM income WHERE userId = ? AND YEAR(date) = ? AND MONTH(date) = ?";
$stmt = $conn->prepare($incomeQuery);
$stmt->bind_param("iii", $userId, $currentYear, $currentMonth);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalIncome = isset($row['totalIncome']) ? $row['totalIncome'] : 0.00;
$stmt->close();

// Get expenses for current month
$expenseQuery = "SELECT SUM(amount) AS totalExpenses FROM expense WHERE userId = ? AND YEAR(date) = ? AND MONTH(date) = ?";
$stmt = $conn->prepare($expenseQuery);
$stmt->bind_param("iii", $userId, $currentYear, $currentMonth);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalExpenses = isset($row['totalExpenses']) ? $row['totalExpenses'] : 0.00;
$stmt->close();

// Get previous month's ending balance (if any)
$lastMonth = $currentMonth - 1;
$lastYear = $currentYear;
if ($lastMonth == 0) {
    $lastMonth = 12;
    $lastYear--;
}

$lastQuery = "SELECT ending_balance FROM budgets WHERE userId = ? AND year = ? AND month = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($lastQuery);
$stmt->bind_param("iii", $userId, $lastYear, $lastMonth);
$stmt->execute();
$result = $stmt->get_result();
$lastRow = $result->fetch_assoc();
$previousEnding = isset($lastRow['ending_balance']) ? $lastRow['ending_balance'] : 0.00;
$stmt->close();

// For the current month, we only use previous ending balance on a new month or when a budget is first set
// After that, we rely on the existing record's ending_balance and just adjust for new income/expenses
if (!isset($_POST['budget'])) {
    // Calculate ending balance following the formula: budget + previous ending balance + income - expenses
    $saldoAkhir = $currentBudget + $previousEnding + $totalIncome - $totalExpenses;
} else {
    // If budget was just updated, calculate with the new budget value
    $saldoAkhir = $newBudget + $previousEnding + $totalIncome - $totalExpenses;
}

// Update the ending balance in the database - make sure to update only the latest entry for this month
$updateEndingQuery = "UPDATE budgets SET ending_balance = ? WHERE userId = ? AND year = ? AND month = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($updateEndingQuery);
$stmt->bind_param("diii", $saldoAkhir, $userId, $currentYear, $currentMonth);
$stmt->execute();

// If update didn't work (ORDER BY in UPDATE not supported in some MySQL versions)
if ($stmt->affected_rows == 0) {
    // Get the latest budget ID for this month
    $stmt->close();
    $getLatestId = "SELECT id FROM budgets WHERE userId = ? AND year = ? AND month = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($getLatestId);
    $stmt->bind_param("iii", $userId, $currentYear, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $latestRow = $result->fetch_assoc();
    $stmt->close();
    
    if ($latestRow) {
        $latestId = $latestRow['id'];
        $updateEndingById = "UPDATE budgets SET ending_balance = ? WHERE id = ?";
        $stmt = $conn->prepare($updateEndingById);
        $stmt->bind_param("di", $saldoAkhir, $latestId);
        $stmt->execute();
        $stmt->close();
    }
} else {
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #FFFFFF 0%, #D0D0D0 100%);
            background-attachment: fixed;
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

        .title {
            font-size: 35px;
            font-weight: 700;
            color: #000000;
            margin-left: 20px;
        }

        .title a {
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

        .hero {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 12px;
            width: 100%;
            margin-bottom: 20px;
            margin-top: 60px;
        }

        .hero .hello {
            font-weight: 500;
            font-size: 40px;
            text-align: center;
            color: #000000;
        }

        .hero .budget-info {
            font-weight: 400;
            font-size: 16px;
            text-align: center;
            color: #828282;
        }

        .hero .budget-amount {
            font-weight: 700;
            font-size: 85px;
            text-align: center;
            color: #000000;
        }

        .frame-2 {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            width: 300px;
            height: 40px;
            margin: auto;
        }

        .primary-button {
            background-color: #000;
            color: #fff;
            border-radius: 15px;
            padding: 8px 16px;
            cursor: pointer;
        }

        .primary-button a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
        }

        .recap-keuangan {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            width: 100%;
            max-width: 1457px;
            margin: auto;
            margin-top: 70px;
            padding: 0 150px;
            box-sizing: border-box;
        }

        .frame-7 {
            display: grid;
            grid-template-columns: 1fr auto;
            width: 120%;
            box-sizing: border-box;
            padding: 0 43px;
        }

        .frame-7 .mei-2024,
        .frame-7 .lihat-detail {
            font-weight: 700;
            color: #828282;
        }

        .frame-7 .mei-2024 {
            font-size: 18px;
        }

        .frame-7 .lihat-detail {
            font-size: 16px;
            text-decoration: underline;
            cursor: pointer;
        }

        .frame-6 {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 20px;
            width: 120%;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .budgetting {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 16px;
            gap: 16px;
            width: 250px;
            background: #F6F6F6;
            box-shadow: -4px 8px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            margin-right: 20px; 
            margin-left: 20px;
        }

        .budgetting .amount {
            font-weight: 700;
            font-size: 24px;
            color: #000000;
        }

        .budgetting .details {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .budgetting .details .edit {
            font-weight: 400;
            font-size: 14px;
            text-decoration: underline;
            color: #828282;
            cursor: pointer;
        }

        .budgetting .details .text {
            font-weight: 400;
            font-size: 14px;
            color: #000000;
        }
        .message {
            color: green;
            font-size: 18px;
            margin: 15px auto; /* Margin otomatis untuk menempatkan di tengah */
            text-align: center;
            background-color: #e0ffe0;
            border: 1px solid green;
            padding: 10px;
            border-radius: 5px;
            max-width: 500px; /* Atur lebar maksimal */
        }

        .message.error {
            color: red;
            background-color: #ffe0e0;
            border: 1px solid red;
        }
        @keyframes popUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard .head-bar {
            animation: popUp 0.5s ease-in-out 0s forwards;
        }

        .dashboard .hero {
            animation: popUp 0.5s ease-in-out 0.2s forwards;
        }

        .dashboard .recap-keuangan {
            animation: popUp 0.5s ease-in-out 0.4s forwards;
        }

        /* RESPONSIVE STYLES */
        
        /* Large screens (desktops) */
        @media screen and (max-width: 1400px) {
            .recap-keuangan {
                padding: 0 100px;
            }
            
            .frame-6 {
                width: 100%;
            }
            
            .frame-7 {
                width: 100%;
            }
        }
        
        /* Medium screens (tablets, smaller desktops) */
        @media screen and (max-width: 1200px) {
            .recap-keuangan {
                padding: 0 50px;
            }
            
            .budgetting {
                width: 200px;
                margin-right: 10px;
                margin-left: 10px;
            }
        }
        
        /* Tablets and small laptops */
        @media screen and (max-width: 992px) {
            .recap-keuangan {
                padding: 0 20px;
            }
            
            .frame-6 {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .budgetting {
                width: calc(50% - 40px);
                margin: 10px;
                min-width: 200px;
            }
            
            .hero .budget-amount {
                font-size: 65px;
            }
            
            .hero .hello {
                font-size: 32px;
            }
        }
        
        /* Tablets portrait */
        @media screen and (max-width: 768px) {
            .hero .budget-amount {
                font-size: 55px;
            }
            
            .title {
                font-size: 30px;
                margin-left: 10px;
            }
            
            .logout {
                margin-right: 10px;
                padding: 6px 12px;
                font-size: 14px;
            }
            
            .frame-7 {
                padding: 0 20px;
            }
        }
        
        /* Mobile devices */
        @media screen and (max-width: 576px) {
            .dashboard {
                padding: 10px;
            }
            
            .head-bar {
                margin-top: 10px;
                margin-bottom: 10px;
            }
            
            .title {
                font-size: 24px;
                margin-left: 5px;
            }
            
            .menu {
                gap: 10px;
            }
            
            .logout {
                padding: 5px 10px;
                font-size: 12px;
                margin-right: 5px;
            }
            
            .hero {
                margin-top: 30px;
            }
            
            .hero .hello {
                font-size: 24px;
            }
            
            .hero .budget-info {
                font-size: 14px;
            }
            
            .hero .budget-amount {
                font-size: 40px;
            }
            
            .frame-2 {
                flex-direction: column;
                height: auto;
                gap: 10px;
            }
            
            .primary-button {
                width: 100%;
                text-align: center;
            }
            
            .recap-keuangan {
                margin-top: 40px;
                padding: 0 10px;
            }
            
            .frame-7 {
                padding: 0 10px;
                grid-template-columns: 1fr;
                justify-items: center;
                text-align: center;
                gap: 5px;
            }
            
            .frame-7 .mei-2024,
            .frame-7 .lihat-detail {
                font-size: 14px;
            }
            
            .frame-6 {
                padding: 0;
            }
            
            .budgetting {
                width: 100%;
                margin: 5px 0;
                padding: 10px;
            }
            
            .budgetting .amount {
                font-size: 20px;
            }
            
            .budgetting .details .text {
                font-size: 12px;
            }
            
            .message {
                font-size: 14px;
                padding: 8px;
                margin: 10px auto;
            }
        }
        
        /* Very small mobile devices */
        @media screen and (max-width: 375px) {
            .title {
                font-size: 20px;
            }
            
            .hero .hello {
                font-size: 20px;
            }
            
            .hero .budget-amount {
                font-size: 32px;
            }
            
            .budgetting .amount {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="head-bar">
            <div class="title">
                <a href="dashboard.php">Financeku.</a>
            </div>
            <div class="menu">
                <a href="aturBudget.php">
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

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <div class="hero">
            <div class="hello" id="hello">Hello, <?php echo htmlspecialchars($nickname); ?></div>
            <div class="budget-info">Budget bulanan anda tersisa:</div>
            <div class="budget-amount" id="budget-amount">Rp <?php echo number_format($saldoAkhir, 2); ?></div>
            <div class="frame-2">
                <div class="primary-button">
                    <a href="pemasukan.php">+ Pemasukan</a>
                </div>
                <div class="primary-button">
                    <a href="pengeluaran.php">+ Pengeluaran</a>
                </div>
            </div>
        </div>
        <div class="recap-keuangan">
            <div class="frame-7">
                <div class="mei-2024" id="current-month-year">April 2025</div>
                <a href="halaman_laporan.php" class="lihat-detail-link">
                    <div class="lihat-detail">Lihat Detail</div>
                </a>
            </div>
            <div class="frame-6">
                <div class="budgetting">
                    <div class="amount" id="saldo-awal">Rp <?php echo number_format($currentBudget, 2); ?></div>
                    <div class="details">
                        <div class="text">Saldo Awal</div>
                    </div>
                </div>
                <div class="budgetting">
                    <div class="amount" id="pemasukan">Rp <?php echo number_format($totalIncome, 2); ?></div>
                    <div class="details">
                        <div class="text">Pemasukan</div>
                    </div>
                </div>
                <div class="budgetting">
                    <div class="amount" id="pengeluaran">Rp <?php echo number_format($totalExpenses, 2); ?></div>
                    <div class="details">
                        <div class="text">Pengeluaran</div>
                    </div>
                </div>
                <div class="budgetting">
                    <div class="amount" id="saldo-akhir">Rp <?php echo number_format($saldoAkhir, 2); ?></div>
                    <div class="details">
                        <div class="text">Saldo Akhir</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const now = new Date();
            const currentMonth = monthNames[now.getMonth()];
            const currentYear = now.getFullYear();
            $('#current-month-year').text(`${currentMonth} ${currentYear}`);
        });
    </script>
</body>
</html>