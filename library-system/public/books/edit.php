<?php
require_once '../../app/models/BookModel.php';
require_once '../../app/models/CategoryModel.php';
require_once '../../app/services/BookService.php';
require_once '../../app/services/AuthService.php';
require_once '../../app/utilities/CSRF.php';

AuthService::check();

$bookModel = new BookModel();
$categoryModel = new CategoryModel();
$bookService = new BookService($bookModel, $categoryModel);
$csrf = new CSRF();

if (!isset($_GET['id'])) {
    die('Book ID is required');
}

$id = $_GET['id'];
$book = $bookService->getBook($id);

if (!$book) {
    die('Book not found');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$csrf->verify($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category = trim($_POST['category']);
        $published_year = trim($_POST['published_year']);
        $total_copies = trim($_POST['total_copies']);
        $available_copies = trim($_POST['available_copies']);

        if (empty($title) || empty($author) || empty($category) || empty($published_year) || empty($total_copies)) {
            $errors[] = 'All fields except available copies are required';
        } elseif (!is_numeric($published_year) || !is_numeric($total_copies) || ($available_copies !== '' && !is_numeric($available_copies))) {
            $errors[] = 'Published year, total copies, and available copies must be numeric';
        } else {
            $data = [
                'title' => $title,
                'author' => $author,
                'category' => $category,
                'published_year' => (int)$published_year,
                'total_copies' => (int)$total_copies,
            ];

            if ($available_copies !== '') {
                $data['available_copies'] = (int)$available_copies;
            }

            $result = $bookService->updateBook($id, $data);

            if ($result) {
                $success = true;
                $book = $bookService->getBook($id);
            } else {
                $errors[] = 'Failed to update book';
            }
        }
    }
}

$csrfToken = $csrf->getToken();
?>

<h2>Edit Book</h2>

<?php if ($success): ?>
    <p>Book updated successfully. <a href="index.php">Back to list</a></p>
<?php else: ?>
    <?php if ($errors): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= htmlspecialchars($id) ?>">
        <?= $csrf->getTokenField() ?>
        <label>Title: <input name="title" value="<?= htmlspecialchars($book['title']) ?>"></label><br>
        <label>Author: <input name="author" value="<?= htmlspecialchars($book['author']) ?>"></label><br>
        <label>Category ID: <input name="category" value="<?= htmlspecialchars($book['category']) ?>"></label><br>
        <label>Published Year: <input name="published_year" value="<?= htmlspecialchars($book['published_year']) ?>"></label><br>
        <label>Total Copies: <input name="total_copies" value="<?= htmlspecialchars($book['total_copies']) ?>"></label><br>
        <label>Available Copies: <input name="available_copies" value="<?= htmlspecialchars($book['available_copies']) ?>"></label><br>
        <button type="submit">Update</button>
    </form>
<?php endif; ?>
