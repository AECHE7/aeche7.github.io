<?php
require_once '../../app/services/AuthService.php';
require_once '../../app/services/TransactionService.php';
require_once '../../app/services/PenaltyService.php';

AuthService::requireRole('admin');

require_once '../../app/models/TransactionModel.php';
require_once '../../app/models/PenaltyModel.php';

$transactionModel = new TransactionModel();
$penaltyModel = new PenaltyModel();

$transactionService = new TransactionService($transactionModel);
$penaltyService = new PenaltyService($penaltyModel);

$overdueTransactions = $transactionService->getOverdueTransactions();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overdue Transactions</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <h2>Overdue Transactions</h2>
    <a href="../dashboard.php">Back to Dashboard</a>
    <?php if (empty($overdueTransactions)): ?>
        <p>No overdue transactions found.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Borrower Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                    <th>Penalty Amount (₱)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($overdueTransactions as $transaction): 
                    $penaltyData = $penaltyService->calculatePenaltyForTransaction($transaction['transaction_id']);
                    $daysOverdue = $penaltyData['days_overdue'] ?? 0;
                    $penaltyAmount = $penaltyData['penalty_amount'] ?? 0;
                ?>
                <tr>
                    <td><?= htmlspecialchars($transaction['borrower_name']) ?></td>
                    <td><?= htmlspecialchars($transaction['book_title']) ?></td>
                    <td><?= htmlspecialchars($transaction['borrow_date']) ?></td>
                    <td><?= htmlspecialchars($transaction['due_date']) ?></td>
                    <td><?= $daysOverdue ?></td>
                    <td><?= number_format($penaltyAmount, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
