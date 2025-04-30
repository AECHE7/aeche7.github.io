<?php
require_once '../../app/services/AuthService.php';
require_once '../../app/core/Database.php';
AuthService::check();

$userId = $_SESSION['user_id'];
$db = Database::getInstance()->getConnection();

// Fetch borrowed books
$query = $db->prepare("SELECT b.title, b.author, c.category_name, t.borrow_date, t.due_date, t.return_date, t.status
    FROM transactions t
    JOIN books b ON t.book_id = b.book_id
    JOIN book_categories c ON b.category = c.category_id
    WHERE t.user_id = :user_id");
$query->execute(['user_id' => $userId]);
$books = $query->fetchAll();

// Fetch available books
$available = $db->query("SELECT title FROM books WHERE available_copies > 0")->fetchAll(PDO::FETCH_COLUMN);
?>

<h2>My Borrowed Books</h2>
<table border="1">
    <tr>
        <th>Title</th><th>Author</th><th>Category</th>
        <th>Borrowed</th><th>Due</th><th>Returned</th><th>Status</th>
    </tr>
    <?php foreach ($books as $book): ?>
        <tr>
            <td><?= htmlspecialchars($book['title']) ?></td>
            <td><?= htmlspecialchars($book['author']) ?></td>
            <td><?= htmlspecialchars($book['category_name']) ?></td>
            <td><?= $book['borrow_date'] ?></td>
            <td><?= $book['due_date'] ?></td>
            <td><?= $book['return_date'] ?? '-' ?></td>
            <td style="color:<?= $book['status'] === 'overdue' ? 'red' : 'black' ?>"><?= $book['status'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Available Books</h2>
<ul>
    <?php foreach ($available as $title): ?>
        <li><?= htmlspecialchars($title) ?></li>
    <?php endforeach; ?>
</ul>
