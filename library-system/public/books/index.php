<?php
require_once '../../app/services/AuthService.php';
require_once '../../app/services/BookService.php';
require_once '../../app/models/BookModel.php';
require_once '../../app/models/CategoryModel.php';

AuthService::check();

$service = new BookService(new BookModel(), new CategoryModel());
$books = $service->getBooksWithCategories();
?>

<h2>Books List</h2>
<a href="create.php">Add Book</a>
<table border="1">
<tr>
    <th>Title</th><th>Author</th><th>Category</th><th>Year</th><th>Available</th><th>Actions</th>
</tr>
<?php foreach ($books as $book): ?>
<tr>
    <td><?= htmlspecialchars($book['title']) ?></td>
    <td><?= htmlspecialchars($book['author']) ?></td>
    <td><?= htmlspecialchars($book['category_name']) ?></td>
    <td><?= $book['published_year'] ?></td>
    <td><?= $book['available_copies'] ?>/<?= $book['total_copies'] ?></td>
    <td>
        <a href="view.php?id=<?= $book['book_id'] ?>">View</a> | 
        <a href="edit.php?id=<?= $book['book_id'] ?>">Edit</a> | 
        <a href="delete.php?id=<?= $book['book_id'] ?>" onclick="return confirm('Delete this book?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
