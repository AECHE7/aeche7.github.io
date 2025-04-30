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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$csrf->verify($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'phone_number' => trim($_POST['phone_number']),
            'password' => $_POST['password'],
            'role' => $_POST['role'],
            'account_status' => $_POST['account_status'] ?? 'active'
        ];

        $result = $userService->createUser($data);

        if ($result['success']) {
            $success = true;
        } else {
            $errors[] = $result['message'];
        }
    }
}

$csrfToken = $csrf->getToken();
?>

<h2>Add New User</h2>

<?php if ($success): ?>
    <p>User created successfully. <a href="index.php">Back to list</a></p>
<?php else: ?>
    <?php if ($errors): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="create.php">
        <?= $csrf->getTokenField() ?>
        <label>Name: <input type="text" name="name" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Phone Number: <input type="text" name="phone_number" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <label>Role:
            <select name="role" required>
                <option value="borrower">Borrower</option>
                <option value="admin">Admin</option>
                <option value="super-admin">Super Admin</option>
            </select>
        </label><br>
        <label>Account Status:
            <select name="account_status">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </label><br>
        <button type="submit">Create User</button>
    </form>
<?php endif; ?>
