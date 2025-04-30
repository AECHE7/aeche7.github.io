<?php
// public/dashboard.php
require_once '../app/services/AuthService.php';
AuthService::check();

$role = $_SESSION['role'];

switch ($role) {
    case 'super-admin':
        header("Location: ../templates/dashboard/super_admin.php");
        break;
    case 'admin':
        header("Location: ../templates/dashboard/admin.php");
        break;
    case 'students':
    case 'staff':
    case 'others':
        header("Location: ../templates/dashboard/borrower.php");
        break;
    default:
        echo "Unknown role.";
}
exit;
