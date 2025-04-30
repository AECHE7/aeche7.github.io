<?php
require_once '../../app/models/UserModel.php';
require_once '../../app/services/UserService.php';
require_once '../../app/services/AuthService.php';
require_once '../../app/utilities/CSRF.php';

AuthService::check();

$userModel = new UserModel();
$userService = new UserService($userModel);
$csrf = new CSRF();

$errors = [];
$success = false;

if (!isset($_GET['id'])) {
    die('User ID is required');
}

$id = $_GET['id'];
$user = $userService->getUserWithBooks($id);

if (!$user) {
    die('User not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$csrf->verify($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'phone_number' => trim($_POST['phone_number']),
            'role' => $_POST['role'],
            'account_status' => $_POST['account_status'] ?? 'active'
        ];

        // Only update password if provided
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        $result = $userService->updateUser($id, $data);

        if ($result['success']) {
            $success = true;
            // Refresh user data
            $user = $userService->getUserWithBooks($id);
        } else {
            $errors[] = $result['message'];
        }
    }
}

$csrfToken = $csrf->getToken();
?>

<h2>Edit User</h2>

<?php if ($success): ?>
    <p>User updated successfully. <a href="index.php">Back to list</a></p>
<?php else: ?>
    <?php if ($errors): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="edit.php?id=<?= htmlspecialchars($id) ?>">
        <?= $csrf->getTokenField() ?>
        <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></label><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label><br>
        <label>Phone Number: <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required></label><br>
        <label>Password: <input type="password" name="password" placeholder="Leave blank to keep current password"></label><br>
        <label>Role:
            <select name="role" required>
                <option value="borrower" <?= $user['role'] === 'borrower' ? 'selected' : '' ?>>Borrower</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="super-admin" <?= $user['role'] === 'super-admin' ? 'selected' : '' ?>>Super Admin</option>
            </select>
        </label><br>
        <label>Account Status:
            <select name="account_status">
                <option value="active" <?= $user['account_status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $user['account_status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </label><br>
        <button type="submit">Update User</button>
    </form>
<?php endif; ?>
