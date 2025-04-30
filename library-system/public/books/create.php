<?php
require_once '../../app/models/BookModel.php';
require_once '../../app/models/CategoryModel.php';
require_once '../../app/services/BookService.php';
require_once '../../app/services/AuthService.php';
require_once '../../app/utilities/CSRF.php';

// AuthService::check();

$bookModel = new BookModel();
$categoryModel = new CategoryModel();
$bookService = new BookService($bookModel, $categoryModel);
$csrf = new CSRF();

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
            // Validate category exists
            if (!$categoryModel->find($category)) {
                $errors[] = 'Invalid category selected';
            } else {
                $data = [
                    'title' => $title,
                    'author' => $author,
                    'category' => $category,
                    'published_year' => (int)$published_year,
                    'total_copies' => (int)$total_copies,
                ];

                // available_copies is set automatically in addBook, but if provided, override
                if ($available_copies !== '') {
                    $data['available_copies'] = (int)$available_copies;
                }

                $result = $bookService->addBook($data);

                if ($result) {
                    $success = true;
                } else {
                    $errors[] = 'Failed to create book';
                }
            }
        }
    }
}

$csrfToken = $csrf->getToken();
?>

<h2>Create Book</h2>

<?php if ($success): ?>
    <p>Book created successfully. <a href="index.php">Back to list</a></p>
<?php else: ?>
    <?php if ($errors): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="create.php">
        <?= $csrf->getTokenField() ?>
        <label>Title: <input name="title" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>"></label><br>
        <label>Author: <input name="author" value="<?= isset($_POST['author']) ? htmlspecialchars($_POST['author']) : '' ?>"></label><br>
        <label>Category ID: <input name="category" value="<?= isset($_POST['category']) ? htmlspecialchars($_POST['category']) : '' ?>"></label><br>
        <label>Published Year: <input name="published_year" value="<?= isset($_POST['published_year']) ? htmlspecialchars($_POST['published_year']) : '' ?>"></label><br>
        <label>Total Copies: <input name="total_copies" value="<?= isset($_POST['total_copies']) ? htmlspecialchars($_POST['total_copies']) : '' ?>"></label><br>
        <label>Available Copies: <input name="available_copies" value="<?= isset($_POST['available_copies']) ? htmlspecialchars($_POST['available_copies']) : '' ?>"></label><br>
        <button type="submit">Save</button>
    </form>
<?php endif; ?>
