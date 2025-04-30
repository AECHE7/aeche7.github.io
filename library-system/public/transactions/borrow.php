<?php
require_once '../../app/services/AuthService.php';
require_once '../../app/models/TransactionModel.php';
require_once '../../app/models/UserModel.php';
require_once '../../app/models/BookModel.php';
require_once '../../app/services/TransactionService.php';

AuthService::requireRole('admin');

$transactionModel = new TransactionModel();
$userModel = new UserModel();
$bookModel = new BookModel();

$transactionService = new TransactionService($transactionModel);

$users = $userModel->findAll("role IN ('students', 'staff', 'others') AND account_status = 'active'");
$books = $bookModel->findAll("available_copies > 0");

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $bookId = $_POST['book_id'] ?? null;
    $dueDate = $_POST['due_date'] ?? null;

    if ($userId && $bookId && $dueDate) {
        $result = $transactionService->borrowBook($userId, $bookId, $dueDate);
        if ($result['success']) {
            $message = "Book borrowed successfully.";
        } else {
            $message = "Error: " . $result['message'];
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrow Book</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <h2>Borrow Book</h2>
    <a href="index.php">Back to Transactions</a>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="user_id">Select Borrower:</label>
        <select name="user_id" id="user_id" required>
            <option value="">-- Select Borrower --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="book_id">Select Book:</label>
        <select name="book_id" id="book_id" required>
            <option value="">-- Select Book --</option>
            <?php foreach ($books as $book): ?>
                <option value="<?= $book['book_id'] ?>"><?= htmlspecialchars($book['title']) ?> (Available: <?= $book['available_copies'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" id="due_date" required>
        <br><br>
        <button type="submit">Borrow</button>
    </form>
</body>
</html>
