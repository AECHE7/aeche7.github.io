<?php
require_once '../../app/core/Database.php';
require_once '../../app/utilities/PDFGenerator.php';

$db = Database::getInstance()->getConnection();
$id = $_GET['id'];

$book = $db->prepare("SELECT title, author FROM books WHERE book_id = ?");
$book->execute([$id]);
$book = $book->fetch();

$borrowers = $db->prepare("SELECT u.name, t.borrow_date, t.due_date, t.return_date, t.status
    FROM transactions t 
    JOIN users u ON t.user_id = u.user_id 
    WHERE t.book_id = ?");
$borrowers->execute([$id]);
$data = $borrowers->fetchAll();

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, "Borrower List for '{$book['title']}'", 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);

foreach ($data as $row) {
    $pdf->Cell(0, 10, "Name: {$row['name']} | Borrowed: {$row['borrow_date']} | Status: {$row['status']}", 0, 1);
}
$pdf->Output();
