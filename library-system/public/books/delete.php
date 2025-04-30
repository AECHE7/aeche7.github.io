<?php
require_once '../../app/services/BookService.php';
require_once '../../app/services/AuthService.php';
AuthService::check();

$bookService = new BookService();
$bookService->deleteBook($_GET['id']);
header("Location: index.php");
exit;
