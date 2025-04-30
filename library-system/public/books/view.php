<?php
require_once '../../app/services/BookService.php';
require_once '../../app/core/Database.php';
require_once '../../app/services/AuthService.php';
AuthService::check();

$bookService = new BookService();
$book = $bookService->getBook($_GET['id']);

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT u.name, t.borrow_date, t.due_date, t.return_date, t.status
    FROM transactions t 
    JOIN users u ON t.user_id = u.user_id 
    WHERE t.book_id = ?");
$stmt->execute([$_GET['id']]);
$borrowers = $stmt->fetchAll();
?>

<h2>Book Detail</h2>
<p><strong>Title:</strong> <?= $book['title'] ?></p>
<p><strong>Author:</strong> <?= $book['author'] ?></p>

<h3>Borrower History</h3>
<table border="1">
    <tr><th>Name</th><th>Borrowed</th><th>Due</th><th>Returned</th><th>Status</th></tr>
    <?php foreach ($borrowers as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['name']) ?></td>
            <td><?= $b['borrow_date'] ?></td>
            <td><?= $b['due_date'] ?></td>
            <td><?= $b['return_date'] ?? '-' ?></td>
            <td style="color:<?= $b['status'] === 'overdue' ? 'red' : 'black' ?>"><?= $b['status'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<a href="../../public/reports/book_pdf.php?id=<?= $book['book_id'] ?>">Export as PDF</a>
