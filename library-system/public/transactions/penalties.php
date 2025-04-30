<?php
require_once '../../app/services/AuthService.php';
require_once '../../app/models/PenaltyModel.php';
require_once '../../app/services/PenaltyService.php';

AuthService::requireRole('admin');

$penaltyModel = new PenaltyModel();
$penaltyService = new PenaltyService($penaltyModel);

$penalties = $penaltyService->getAllPenaltiesWithDetails();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Penalties</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <h2>Penalties</h2>
    <a href="index.php">Back to Transactions</a>
    <?php if (empty($penalties)): ?>
        <p>No penalties found.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Borrower Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Penalty Amount (₱)</th>
                    <th>Penalty Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($penalties as $penalty): ?>
                <tr>
                    <td><?= htmlspecialchars($penalty['borrower_name']) ?></td>
                    <td><?= htmlspecialchars($penalty['book_title']) ?></td>
                    <td><?= htmlspecialchars($penalty['borrow_date']) ?></td>
                    <td><?= htmlspecialchars($penalty['due_date']) ?></td>
                    <td><?= htmlspecialchars($penalty['return_date']) ?></td>
                    <td><?= number_format($penalty['penalty_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($penalty['penalty_date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
