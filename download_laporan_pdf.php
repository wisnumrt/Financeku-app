<?php
require 'vendor/autoload.php'; // Pastikan Dompdf ter-load
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
include('dbconn.php');

$userId = $_SESSION['userId'];
$nickname = $_SESSION['nickname'];

$year = $_GET['year'] ?? date("Y");
$month = $_GET['month'] ?? date("m");
$type = $_GET['type'] ?? 'semua';

$conn = getConnection();

// Fetch income data
$incomeQuery = "SELECT date, incomeSource, amount FROM income WHERE userId = ? AND YEAR(date) = ? AND MONTH(date) = ?";
$stmtIncome = $conn->prepare($incomeQuery);
$stmtIncome->bind_param("iii", $userId, $year, $month);
$stmtIncome->execute();
$incomeResult = $stmtIncome->get_result();
$incomes = $incomeResult->fetch_all(MYSQLI_ASSOC);
$stmtIncome->close();

// Fetch expense data
$expenseQuery = "SELECT date, expenseCategory, description, amount FROM expense WHERE userId = ? AND YEAR(date) = ? AND MONTH(date) = ?";
$stmtExpense = $conn->prepare($expenseQuery);
$stmtExpense->bind_param("iii", $userId, $year, $month);
$stmtExpense->execute();
$expenseResult = $stmtExpense->get_result();
$expenses = $expenseResult->fetch_all(MYSQLI_ASSOC);
$stmtExpense->close();

$conn->close();

// Generate HTML content
$html = '<h2 style="text-align:center;">Laporan Keuangan - ' . $nickname . '</h2>';
$html .= '<p style="text-align:center;">Periode: ' . date("F Y", strtotime("$year-$month-01")) . '</p>';

// Pemasukan
$html .= '<h3>Pemasukan</h3>';
$html .= '<table border="1" cellspacing="0" cellpadding="5" width="100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Sumber</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>';
$totalIncome = 0;
foreach ($incomes as $i => $income) {
    $html .= '<tr>
                <td>'.($i+1).'</td>
                <td>'.$income['date'].'</td>
                <td>'.$income['incomeSource'].'</td>
                <td>Rp' . number_format($income['amount'], 2, ',', '.') . '</td>
              </tr>';
    $totalIncome += $income['amount'];
}
$html .= '<tr><td colspan="3"><strong>Total</strong></td><td><strong>Rp' . number_format($totalIncome, 2, ',', '.') . '</strong></td></tr>';
$html .= '</tbody></table>';

// Pengeluaran
$html .= '<h3 style="margin-top: 30px;">Pengeluaran</h3>';
$html .= '<table border="1" cellspacing="0" cellpadding="5" width="100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>';
$totalExpense = 0;
foreach ($expenses as $i => $expense) {
    $html .= '<tr>
                <td>'.($i+1).'</td>
                <td>'.$expense['date'].'</td>
                <td>'.$expense['expenseCategory'].'</td>
                <td>'.$expense['description'].'</td>
                <td>Rp' . number_format($expense['amount'], 2, ',', '.') . '</td>
              </tr>';
    $totalExpense += $expense['amount'];
}
$html .= '<tr><td colspan="4"><strong>Total</strong></td><td><strong>Rp' . number_format($totalExpense, 2, ',', '.') . '</strong></td></tr>';
$html .= '</tbody></table>';

// Generate PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_keuangan_$month-$year.pdf", array("Attachment" => true));
exit;
