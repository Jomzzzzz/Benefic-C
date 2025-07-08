<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

if (!isset($_POST['monthYear'])) {
    die("No month selected.");
}

$selectedMonthYear = $_POST['monthYear']; // e.g. "June 2025"

// Extract year and month number from string
$dt = DateTime::createFromFormat('F Y', $selectedMonthYear);
if (!$dt) {
    die("Invalid month format.");
}

$year = $dt->format('Y');
$month = $dt->format('m');

// Prepare SQL query to fetch logs only for the selected month and year
$stmt = $conn->prepare("SELECT * FROM booking_logs WHERE YEAR(moved_at) = ? AND MONTH(moved_at) = ? ORDER BY moved_at DESC");
$stmt->bind_param("ii", $year, $month);
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($log = $result->fetch_assoc()) {
    $logs[] = $log;
}

// Include TCPDF and generate PDF
require_once('tcpdf/tcpdf.php');

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('SBHD System');
$pdf->SetAuthor('SBHD Admin');
$pdf->SetTitle('Booking Log Book - ' . $selectedMonthYear);
$pdf->SetSubject('Booking Logs for ' . $selectedMonthYear);
$pdf->SetKeywords('booking, logs, pdf');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

// Header Title
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetTextColor(223, 82, 25);
$pdf->Cell(0, 15, '📚 Booking Log Book - ' . $selectedMonthYear, 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetTextColor(0);
$pdf->SetFont('helvetica', '', 10);

if (empty($logs)) {
    $pdf->Cell(0, 10, "No booking logs found for this month.", 0, 1);
} else {
    // Headers and Column Widths
    $header = ['Name', 'Email', 'Phone', 'Room Type', 'Guests', 'Check-in', 'Check-out', 'Booked On', 'Moved To Log'];
    $w = [40, 50, 30, 25, 18, 25, 25, 25, 25];

    // Table Header
    $pdf->SetFillColor(223, 82, 25);
    $pdf->SetTextColor(255);
    $pdf->SetDrawColor(128, 0, 0);
    $pdf->SetLineWidth(0.3);
    $pdf->SetFont('', 'B');

    foreach ($header as $i => $col) {
        $pdf->Cell($w[$i], 7, $col, 1, 0, 'C', 1);
    }
    $pdf->Ln();

    // Reset fonts
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0);
    $pdf->SetFont('');

        $fill = 0;
foreach ($logs as $log) {
    $guests = $log['adults'] + $log['children'];

    $pdf->Cell($w[0], 6, mb_strimwidth($log['full_name'], 0, 40, '...'), 'LR', 0, 'L', $fill);
    $pdf->Cell($w[1], 6, mb_strimwidth($log['email'], 0, 50, '...'), 'LR', 0, 'L', $fill);
    $pdf->Cell($w[2], 6, $log['phone'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[3], 6, $log['room_type'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[4], 6, $guests . ' guest' . ($guests > 1 ? 's' : ''), 'LR', 0, 'C', $fill);
    $pdf->Cell($w[5], 6, date('M j, Y', strtotime($log['check_in'])), 'LR', 0, 'C', $fill);
    $pdf->Cell($w[6], 6, date('M j, Y', strtotime($log['check_out'])), 'LR', 0, 'C', $fill);
    $pdf->Cell($w[7], 6, date('M j, Y', strtotime($log['created_at'])), 'LR', 0, 'C', $fill);
    $pdf->Cell($w[8], 6, date('M j, Y', strtotime($log['moved_at'])), 'LR', 0, 'C', $fill);

    $pdf->Ln();
    $fill = !$fill;

    if ($pdf->GetY() > 180) {
        $pdf->Cell(array_sum($w), 0, '', 'T');
        $pdf->AddPage();
    }
}


    // Bottom line
    $pdf->Cell(array_sum($w), 0, '', 'T');
}

$pdf->Output('booking_log_' . str_replace(' ', '_', strtolower($selectedMonthYear)) . '.pdf', 'D');
exit;
?>
