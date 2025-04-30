<?php
require_once '../../app/models/UserModel.php';
require_once '../../app/services/UserService.php';
require_once '../../app/services/AuthService.php';
// AuthService::check();

$userModel = new UserModel();
$service = new UserService($userModel);
$users = $service->listUsers();
?>

<h2>Users List</h2>
<a href="create.php">Add User</a>
<table border="1">
<tr>
    <th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Actions</th>
</tr>
<?php foreach ($users as $u): ?>
<tr>
    <td><?= htmlspecialchars($u['name']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td><?= htmlspecialchars($u['phone_number']) ?></td>
    <td><?= htmlspecialchars($u['role']) ?></td>
    <td><?= htmlspecialchars($u['account_status']) ?></td>
    <td>
        <a href="view.php?id=<?= $u['user_id'] ?>">View</a> | 
        <a href="edit.php?id=<?= $u['user_id'] ?>">Edit</a> | 
        <a href="deactivate.php?id=<?= $u['user_id'] ?>" onclick="return confirm('Deactivate this user?')">Deactivate</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
