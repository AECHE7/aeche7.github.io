<?php
// public/login.php
session_start();
require_once '../app/models/UserModel.php';
require_once '../app/services/AuthService.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel = new UserModel(); // Assuming UserModel is the required class
    $authService = new AuthService($userModel);
    $result = $authService->login($_POST['email'], $_POST['password']);

    if (is_array($result) && $result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = is_array($result) ? $result['message'] : 'An unexpected error occurred.';
    }
}
?>

<!-- Simple login form -->
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
