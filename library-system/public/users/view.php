<?php
require_once '../../app/models/UserModel.php';
require_once '../../app/services/UserService.php';
require_once '../../app/services/AuthService.php';

AuthService::check();

if (!isset($_GET['id'])) {
    die('User ID is required');
}

$id = $_GET['id'];

$userModel = new UserModel();
$userService = new UserService($userModel);

$user = $userService->getUserWithBooks($id);

if (!$user) {
    die('User not found');
}
?>

<h2>User Details</h2>

<p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
<p><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone_number']) ?></p>
<p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
<p><strong>Account Status:</strong> <?= htmlspecialchars($user['account_status']) ?></p>

<?php if (!empty($user['borrowed_books'])): ?>
    <h3>Borrowed Books</h3>
    <ul>
        <?php foreach ($user['borrowed_books'] as $book): ?>
            <li><?= htmlspecialchars($book['title']) ?> (Due: <?= htmlspecialchars($book['due_date']) ?>)</li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="edit.php?id=<?= htmlspecialchars($id) ?>">Edit User</a> | <a href="index.php">Back to List</a></p>
