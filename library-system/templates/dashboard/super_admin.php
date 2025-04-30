<?php
require_once '../../app/services/AuthService.php';
require_once '../../app/core/Database.php';
AuthService::requireRole('super-admin');

$db = Database::getInstance()->getConnection();

// Fetch counts
$totalBooks = $db->query("SELECT COUNT(*) FROM books")->fetchColumn();
$borrowedBooks = $db->query("SELECT COUNT(*) FROM transactions WHERE status = 'borrowed'")->fetchColumn();
$overdueBooks = $db->query("SELECT COUNT(*) FROM transactions WHERE status = 'overdue'")->fetchColumn();
$totalPenalties = $db->query("SELECT SUM(penalty_amount) FROM penalties")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAdmins = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

?>

<h2>Super Admin Dashboard</h2>
<ul>
    <li>Total Books: <?= $totalBooks ?></li>
    <li>Currently Borrowed Books: <?= $borrowedBooks ?></li>
    <li>Overdue Books: <?= $overdueBooks ?></li>
    <li>Total Penalties (₱): <?= number_format($totalPenalties ?? 0, 2) ?></li>
    <li>Total Users: <?= $totalUsers ?></li>
    <li>Admin Accounts: <?= $totalAdmins ?></li>
</ul>
