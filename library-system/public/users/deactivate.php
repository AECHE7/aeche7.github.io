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

$result = $userService->deactivateUser($id);

if ($result['success']) {
    header('Location: index.php?message=User deactivated successfully');
    exit;
} else {
    die('Error: ' . htmlspecialchars($result['message']));
}
?>
