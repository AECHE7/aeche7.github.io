<?php
require_once '../../app/services/AuthService.php';
require_once '../../app/models/TransactionModel.php';
require_once '../../app/models/BookModel.php';
require_once '../../app/services/TransactionService.php';

AuthService::requireRole('admin');

$transactionModel = new TransactionModel();
$bookModel = new BookModel();
$transactionService = new TransactionService($transactionModel);

$message = '';
$borrowedTransactions = $transactionService->getTransactionsWithDetails('borrowed');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionId = $_POST['transaction_id'] ?? null;
    if ($transactionId) {
        $result = $transactionService->returnBook($transactionId);
        if ($result['success']) {
            $message = "Book returned successfully.";
            // Refresh the list after return
            $borrowedTransactions = $transactionService->getTransactionsWithDetails('borrowed');
        } else {
            $message = "Error: " . $result['message'];
        }
    } else {
        $message = "Invalid transaction selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Book</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <h2>Return Book</h2>
    <a href="index.php">Back to Transactions</a>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <?php if (empty($borrowedTransactions)): ?>
        <p>No borrowed books to return.</p>
    <?php else: ?>
        <form method="POST" action="">
            <label for="transaction_id">Select Borrowed Book to Return:</label>
            <select name="transaction_id" id="transaction_id" required>
                <option value="">-- Select Transaction --</option>
                <?php foreach ($borrowedTransactions as $transaction): ?>
                    <option value="<?= $transaction['transaction_id'] ?>">
                        <?= htmlspecialchars($transaction['borrower_name']) ?> - <?= htmlspecialchars($transaction['book_title']) ?> (Due: <?= $transaction['due_date'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <button type="submit">Return Book</button>
        </form>
    <?php endif; ?>
</body>
</html>
