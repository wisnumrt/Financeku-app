<?php
session_start();
include('dbconn.php');

$nickname = $_SESSION['nickname'];
$userId = $_SESSION['userId'];

$selectedYear = isset($_GET['year']) ? $_GET['year'] : date ("Y");
$selectedMonth = isset($_GET["month"]) ? $_GET['month'] : date ('m');
$selectedType = isset($_GET['type']) ? $_GET['type'] : 'semua';

// Create a connection
$conn = getConnection();

// Fetch income data
$incomeQuery = "SELECT id, date, incomeSource, amount FROM income WHERE userId = ? AND YEAR(date) = ? AND MONTH(date) = ?";
$stmtIncome = mysqli_prepare($conn, $incomeQuery);
mysqli_stmt_bind_param($stmtIncome, "iii", $userId, $selectedYear, $selectedMonth);
mysqli_stmt_execute($stmtIncome);
$resultIncome = mysqli_stmt_get_result($stmtIncome);
$incomes = mysqli_fetch_all($resultIncome, MYSQLI_ASSOC);
mysqli_stmt_close($stmtIncome);

// Fetch expense data   
$expenseQuery = "SELECT id, date, expenseCategory, description, amount FROM expense WHERE userId = ? AND YEAR(date) = ? AND MONTH(date) = ?";
$stmtExpense = mysqli_prepare($conn, $expenseQuery);
mysqli_stmt_bind_param($stmtExpense, "iii", $userId, $selectedYear, $selectedMonth);
mysqli_stmt_execute($stmtExpense);
$resultExpense = mysqli_stmt_get_result($stmtExpense);
$expenses = mysqli_fetch_all($resultExpense, MYSQLI_ASSOC);
mysqli_stmt_close($stmtExpense);

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeku Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
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
            padding: 0 20px;
            margin-bottom: 20px;
            margin-top: 15px;
        }

        .title {
            font-size: 35px;
            font-weight: 700;
            text-decoration: none;
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

        .summary {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            font-family: 'Inter', sans-serif;
            font-size: 24px;
            font-weight: 600;
            margin-top: 65px;
            color: black;
        }

        .summary div {
            padding: 10px 20px;
            border-radius: 10px;
            text-align: center;
        }

        .summary .income-label {
            font-size: 25px;
            font-weight: 600;
        }

        .summary .income-amount {
            font-size: 33px;
            font-weight: 700;
        }

        .summary .expense-label {
            font-size: 25px;
            font-weight: 600;
        }

        .summary .expense-amount {
            font-size: 33px;
            font-weight: 700;
        }

        .filter-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 20px auto;
            margin-top: 50px;
            padding: 15px;
            background-color: #f4f4f4;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: fit-content;
        }

        .filter-bar select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .tab-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 100%;
            margin: 20px auto;
            width: 1200px;
        }

        .tab {
            display: flex;
            gap: 5px;
        }

        .tab button {
            background-color: #f1f1f1;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 20px;
            transition: background-color 0.3s;
            border-radius: 5px;
            font-size: 17px;
        }

        .tab button:hover {
            background-color: #ddd;
        }

        .tab button.active {
            background-color: #ccc;
        }

        .tabcontent {
            display: block;
            padding: 0 20px;
            margin-top: -1px;
            width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table th {
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            text-align: center;
            background: #f4f4f4;
            font-weight: 700;
        }

        table td {
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            text-align: left;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr td:last-child, table tr th:last-child {
            border-right: none;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .actions img {
            width: 27px;
            height: 27px;
            cursor: pointer;
        }

        .actions-column {
            width: 100px;
            text-align: center;
        }

        .number-column {
            width: 40px;
            text-align: center;
        }

        .date-column {
            width: 150px;
            text-align: center;
        }

        .search-bar {
            text-align: right;
            flex-grow: 1;
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .search-bar input {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 200px;
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

        /* Pagination Styling */
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            gap: 5px;
        }

        .pagination button {
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .pagination button:hover {
            background-color: #ddd;
        }

        .pagination button.active {
            background-color: #000;
            color: white;
            border: 1px solid #000;
        }

        .pagination button:disabled {
            background-color: #f1f1f1;
            color: #999;
            cursor: not-allowed;
        }

        /* ========== RESPONSIVE STYLES ========== */
        
        /* Large screens (desktops) */
        @media screen and (max-width: 1280px) {
            .tab-container, .tabcontent {
                width: 95%;
            }
            
            table {
                width: 100%;
            }
        }
        
        /* Medium screens (tablets, smaller desktops) */
        @media screen and (max-width: 992px) {
            .summary {
                flex-direction: row;
                margin-top: 40px;
            }
            
            .summary div {
                padding: 5px 10px;
            }
            
            .summary .income-label, 
            .summary .expense-label {
                font-size: 22px;
            }
            
            .summary .income-amount, 
            .summary .expense-amount {
                font-size: 28px;
            }
            
            .filter-bar {
                flex-wrap: wrap;
                width: 80%;
            }
            
            .filter-bar select, 
            .filter-bar button {
                flex: 1 0 calc(50% - 10px);
                margin: 5px 0;
            }
            
            .tab-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-bar {
                width: 100%;
                justify-content: center;
                margin: 10px 0;
            }
            
            .search-bar input {
                width: 100%;
                max-width: 300px;
            }

            .pagination button {
                padding: 6px 12px;
                font-size: 14px;
            }
        }
        
        /* Tablets */
        @media screen and (max-width: 768px) {
            .head-bar {
                padding: 0 10px;
            }
            
            .title {
                font-size: 28px;
            }
            
            .logout {
                padding: 6px 12px;
                font-size: 14px;
            }
            
            .summary .income-label, 
            .summary .expense-label {
                font-size: 20px;
            }
            
            .summary .income-amount, 
            .summary .expense-amount {
                font-size: 24px;
            }
            
            .tab button {
                padding: 10px 15px;
                font-size: 15px;
            }
            
            table {
                margin: 10px auto;
            }
            
            table th, table td {
                padding: 8px 10px;
                font-size: 14px;
            }
            
            .actions img {
                width: 22px;
                height: 22px;
            }
            
            .date-column {
                width: 120px;
            }

            .pagination {
                flex-wrap: wrap;
            }
            
            .pagination button {
                padding: 5px 10px;
                font-size: 13px;
            }
        }
        
        /* Mobile devices */
        @media screen and (max-width: 576px) {
            .dashboard {
                padding: 10px;
            }
            
            .head-bar {
                margin-top: 5px;
                margin-bottom: 10px;
            }
            
            .title {
                font-size: 24px;
            }
            
            .menu {
                gap: 10px;
            }
            
            .summary {
                flex-direction: column;
                gap: 15px;
                margin-top: 30px;
            }
            
            .filter-bar {
                flex-direction: column;
                width: 90%;
                padding: 10px;
                gap: 5px;
            }
            
            .filter-bar select, 
            .filter-bar button {
                width: 100%;
            }
            
            .tab {
                width: 100%;
                justify-content: space-between;
            }
            
            .tab button {
                flex: 1;
                padding: 10px 5px;
                font-size: 14px;
            }
            
            .tabcontent {
                padding: 0 5px;
            }
            
            /* Make table responsive */
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .number-column {
                width: 30px;
            }
            
            .date-column {
                width: 100px;
            }
            
            .actions-column {
                width: 80px;
            }
            
            table th, table td {
                padding: 8px 6px;
                font-size: 12px;
            }
            
            .actions img {
                width: 20px;
                height: 20px;
            }
            
            .message {
                font-size: 14px;
                padding: 8px;
                margin: 10px auto;
            }

            .pagination button {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
        
        /* Very small mobile devices */
        @media screen and (max-width: 375px) {
            .title {
                font-size: 20px;
            }
            
            .menu img {
                width: 20px;
                height: 20px;
            }
            
            .logout {
                padding: 5px 10px;
                font-size: 12px;
            }
            
            .summary .income-label, 
            .summary .expense-label {
                font-size: 18px;
            }
            
            .summary .income-amount, 
            .summary .expense-amount {
                font-size: 20px;
            }
            
            .filter-bar select {
                font-size: 14px;
                padding: 8px;
            }
            
            .tab button {
                padding: 8px 5px;
                font-size: 12px;
            }

            .pagination button {
                padding: 3px 6px;
                font-size: 11px;
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
        <?php if (isset($_SESSION['update_message'])): ?>
            <div class="message"><?php echo $_SESSION['update_message']; unset($_SESSION['update_message']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['delete_message'])): ?>
            <div class="message"><?php echo $_SESSION['delete_message']; unset($_SESSION['delete_message']); ?></div>
        <?php endif; ?>
        <div class="summary">
            <div>
                <div class="income-label">Pemasukan</div>
                <div class="income-amount">Rp<?php echo number_format(array_sum(array_column($incomes, 'amount')), 2, ',', '.'); ?></div>
            </div>
            <div>
                <div class="expense-label">Pengeluaran</div>
                <div class="expense-amount">Rp<?php echo number_format(array_sum(array_column($expenses, 'amount')), 2, ',', '.'); ?></div>
            </div>
        </div>
        <div class="filter-bar">
            <form method="GET" action="halaman_laporan.php">
                <select name="year" id="yearSelect">
                    <?php
                    $currentYear = date("Y");
                    for ($year = $currentYear; $year >= 2000; $year--) {
                        echo "<option value=\"$year\">$year</option>";
                    }
                    ?>
                </select>
                <select name="month" id="monthSelect">
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
                <select name="type" id="typeSelect">
                <option value="semua" <?php echo ($selectedType == 'semua') ? 'selected' : ''; ?>>Semua</option>
                </select>
                <button type="submit">Cari</button>
            </form>
        </div>
   
        <div class="tab-container">
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'Pemasukan')">Pemasukan</button>
                <button class="tablinks" onclick="openTab(event, 'Pengeluaran')">Pengeluaran</button>
            </div>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Cari...">
            </div>
        </div>
        <div id="Pemasukan" class="tabcontent">
            <table id="pemasukanTable">
                <thead>
                    <tr>
                        <th class="number-column">No.</th>
                        <th class="date-column">Tanggal</th>
                        <th>Sumber</th>
                        <th>Jumlah</th>
                        <th class="actions-column">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incomes as $index => $income) : ?>
                    <tr class="income-row">
                        <td class="number-column"><?php echo $index + 1; ?></td>
                        <td class="date-column"><?php echo $income['date']; ?></td>
                        <td><?php echo $income['incomeSource']; ?></td>
                        <td>Rp<?php echo number_format($income['amount'], 2, ',', '.'); ?></td>
                        <td class="actions-column">
                            <div class="actions">
                                <a href="editincome.php?id=<?php echo $income['id']; ?>"><img src="img/edit.png" alt="Edit Icon"></a>
                                <a href="delete_income.php?id=<?php echo $income['id']; ?>"><img src="img/delete.png" alt="Delete Icon"></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Pagination for Income Table -->
            <div class="pagination" id="pemasukanPagination"></div>
        </div>
        <div id="Pengeluaran" class="tabcontent">
            <table id="pengeluaranTable">
                <thead>
                    <tr>
                        <th class="number-column">No.</th>
                        <th class="date-column">Tanggal</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th class="actions-column">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $index => $expense) : ?>
                    <tr class="expense-row">
                        <td class="number-column"><?php echo $index + 1; ?></td>
                        <td class="date-column"><?php echo $expense['date']; ?></td>
                        <td><?php echo $expense['expenseCategory']; ?></td>
                        <td><?php echo $expense['description']; ?></td>
                        <td>Rp<?php echo number_format($expense['amount'], 2, ',', '.'); ?></td>
                        <td class="actions-column">
                            <div class="actions">
                                <a href="editexpense.php?id=<?php echo $expense['id']; ?>"><img src="img/edit.png" alt="Edit Icon"></a>
                                <a href="delete_expense.php?id=<?php echo $expense['id']; ?>"><img src="img/delete.png" alt="Delete Icon"></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Pagination for Expense Table -->
            <div class="pagination" id="pengeluaranPagination"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Mempertahankan opsi yang dipilih setelah filter
            const urlParams = new URLSearchParams(window.location.search);
            const selectedYear = urlParams.get('year');
            const selectedMonth = urlParams.get('month');
            const selectedType = urlParams.get('type');
            const page = parseInt(urlParams.get('page')) || 1;

            if (selectedYear) {
                document.getElementById('yearSelect').value = selectedYear;
            }
            if (selectedMonth) {
                document.getElementById('monthSelect').value = selectedMonth; 
            }
            if (selectedType) {
                document.getElementById('typeSelect').value = selectedType;
            }

            // Tab dan search
            document.querySelector('.tab button:first-child').click();
            document.getElementById('searchInput').addEventListener('keyup', function() {
                var searchValue = this.value.toLowerCase();
                var tables = document.querySelectorAll('.tabcontent table tbody');
                tables.forEach(function(tbody) {
                    var rows = tbody.getElementsByTagName('tr');
                    for (var i = 0; i < rows.length; i++) {
                        var cells = rows[i].getElementsByTagName('td');
                        var found = false;
                        for (var j = 0; j < cells.length; j++) {
                            var cellValue = cells[j].textContent || cells[j].innerText;
                            cellValue = cellValue.toLowerCase();
                            if (cellValue.indexOf(searchValue) > -1) {
                                found = true;
                                break;
                            }
                        }
                        if (found) {
                            rows[i].style.display = '';
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }
                    // Reset pagination after search
                    updatePagination('pemasukanTable', 'pemasukanPagination', 3, 1); // Reset to page 1 after search
                    updatePagination('pengeluaranTable', 'pengeluaranPagination', 3, 1);
                });
            });

            // Initialize pagination
            setupPagination();
        });

        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        function setupPagination() {
            // Get page from URL or default to 1
            const urlParams = new URLSearchParams(window.location.search);
            const currentPage = parseInt(urlParams.get('page')) || 1;
            
            // Set up pagination for both tables with the current page
            updatePagination('pemasukanTable', 'pemasukanPagination', 3, currentPage);
            updatePagination('pengeluaranTable', 'pengeluaranPagination', 3, currentPage);
        }

        function updatePagination(tableId, paginationId, rowsPerPage, forcePage = null) {
            const table = document.getElementById(tableId);
            const pagination = document.getElementById(paginationId);
            const rows = table.querySelectorAll('tbody tr');
            
            // Clear pagination
            pagination.innerHTML = '';
            
            // Get current page (either from parameter or URL)
            const urlParams = new URLSearchParams(window.location.search);
            let currentPage = forcePage !== null ? forcePage : (parseInt(urlParams.get('page')) || 1);
            
            // Count visible rows after search filtering
            let visibleRows = 0;
            for (let i = 0; i < rows.length; i++) {
                if (rows[i].style.display !== 'none') {
                    visibleRows++;
                }
            }
            
            // If there are fewer visible rows than rowsPerPage, don't paginate
            if (visibleRows <= rowsPerPage) {
                for (let i = 0; i < rows.length; i++) {
                    if (rows[i].style.display !== 'none') {
                        rows[i].style.display = '';
                    }
                }
                return;
            }
            
            // Calculate pages
            const pageCount = Math.ceil(visibleRows / rowsPerPage);
            
            // Ensure current page is valid
            if (currentPage > pageCount) {
                currentPage = pageCount;
            }
            if (currentPage < 1) {
                currentPage = 1;
            }
            
            // Create pagination buttons
            // Previous button
            const prevButton = document.createElement('button');
            prevButton.innerText = '«';
            prevButton.disabled = currentPage === 1;
            prevButton.addEventListener('click', (e) => {
                e.preventDefault();
                goToPage(currentPage - 1, tableId, paginationId, rowsPerPage);
            });
            pagination.appendChild(prevButton);
            
            // Page number buttons
            for (let i = 1; i <= pageCount; i++) {
                const pageButton = document.createElement('button');
                pageButton.innerText = i;
                if (i === currentPage) {
                    pageButton.classList.add('active');
                }
                pageButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    goToPage(i, tableId, paginationId, rowsPerPage);
                });
                pagination.appendChild(pageButton);
            }
            
            // Next button
            const nextButton = document.createElement('button');
            nextButton.innerText = '»';
            nextButton.disabled = currentPage === pageCount;
            nextButton.addEventListener('click', (e) => {
                e.preventDefault();
                goToPage(currentPage + 1, tableId, paginationId, rowsPerPage);
            });
            pagination.appendChild(nextButton);
            
            // Apply pagination to rows
            let visibleIndex = 0;
            for (let i = 0; i < rows.length; i++) {
                // Only consider rows that aren't hidden by search
                if (rows[i].style.display !== 'none') {
                    // Show only rows for current page
                    if (visibleIndex >= (currentPage - 1) * rowsPerPage && visibleIndex < currentPage * rowsPerPage) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                    visibleIndex++;
                }
            }
        }

        function goToPage(page, tableId, paginationId, rowsPerPage) {
            // Update URL with page parameter but prevent page reload
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            window.history.pushState({}, '', url);
            
            // Update pagination display immediately, without reload
            updatePagination(tableId, paginationId, rowsPerPage, page);
        }
    </script>
</body>
</html>