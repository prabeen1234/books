<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}include('config.php');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['delete_book'])) {
    $book_id = $_POST['book_id'];

    $delete_query = "DELETE FROM books WHERE id = ?";
    $stmt = mysqli_prepare($connection, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $book_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Book deleted successfully.";
    } else {
        $_SESSION['delete_book_error'] = "Failed to delete book. Please try again later.";
    }

    mysqli_stmt_close($stmt);
}

$books_query = "SELECT * FROM books";
$books_result = mysqli_query($connection, $books_query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Book List - Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['delete_book_error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['delete_book_error']; ?>
            </div>
            <?php unset($_SESSION['delete_book_error']); ?>
        <?php endif; ?>

        <h2>Book List</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Book Name</th>
                    <th>Author</th>
                    <th>Original Price</th>
                    <th>Selling Price</th>
                    <th>Address</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($books_result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                       
                        <td><?php echo $row['author']; ?></td>
                        <td><?php echo $row['original_price']; ?></td>
                        <td><?php echo $row['selling_price']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_book" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
