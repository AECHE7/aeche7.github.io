<?php
require_once '../../app/services/AuthService.php';
require_once '../../app/models/TransactionModel.php';
require_once '../../app/services/TransactionService.php';

AuthService::requireRole('admin');

$transactionModel = new TransactionModel();
$transactionService = new TransactionService($transactionModel);

$transactions = $transactionService->getTransactionsWithDetails();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transactions</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <h2>Transactions</h2>
    <nav>
        <a href="borrow.php">Borrow Book</a> |
        <a href="return.php">Return Book</a> |
        <a href="overdue.php">Overdue Transactions</a> |
        <a href="penalties.php">Penalties</a> |
        <a href="../dashboard.php">Dashboard</a>
    </nav>
    <?php if (empty($transactions)): ?>
        <p>No transactions found.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Borrower Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction['transaction_id'] ?></td>
                    <td><?= htmlspecialchars($transaction['borrower_name']) ?></td>
                    <td><?= htmlspecialchars($transaction['book_title']) ?></td>
                    <td><?= htmlspecialchars($transaction['borrow_date']) ?></td>
                    <td><?= htmlspecialchars($transaction['due_date']) ?></td>
                    <td><?= htmlspecialchars($transaction['return_date'] ?? '') ?></td>
                    <td><?= htmlspecialchars($transaction['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
