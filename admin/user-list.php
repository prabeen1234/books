<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}include('config.php');
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Disable foreign key checks
    mysqli_query($connection, "SET FOREIGN_KEY_CHECKS = 0");

    // Start a transaction
    mysqli_begin_transaction($connection);

    try {
        // Delete associated records from 'ratings' table
        $delete_ratings_query = "DELETE FROM ratings WHERE user_id = ?";
        $stmt_delete_ratings = mysqli_prepare($connection, $delete_ratings_query);
        mysqli_stmt_bind_param($stmt_delete_ratings, "i", $user_id);
        mysqli_stmt_execute($stmt_delete_ratings);
        mysqli_stmt_close($stmt_delete_ratings);

        // Delete associated records from 'books' table
        $delete_books_query = "DELETE FROM books WHERE seller_id = ?";
        $stmt_delete_books = mysqli_prepare($connection, $delete_books_query);
        mysqli_stmt_bind_param($stmt_delete_books, "i", $user_id);
        mysqli_stmt_execute($stmt_delete_books);
        mysqli_stmt_close($stmt_delete_books);

        // Delete user from 'users' table
        $delete_user_query = "DELETE FROM users WHERE id = ?";
        $stmt_delete_user = mysqli_prepare($connection, $delete_user_query);
        mysqli_stmt_bind_param($stmt_delete_user, "i", $user_id);
        mysqli_stmt_execute($stmt_delete_user);
        mysqli_stmt_close($stmt_delete_user);

        // Commit the transaction
        mysqli_commit($connection);

        // Re-enable foreign key checks
        mysqli_query($connection, "SET FOREIGN_KEY_CHECKS = 1");

        $_SESSION['success_message'] = "User, associated records, and ratings deleted successfully.";
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        mysqli_rollback($connection);

        // Re-enable foreign key checks
        mysqli_query($connection, "SET FOREIGN_KEY_CHECKS = 1");

        $_SESSION['delete_user_error'] = "Failed to delete user, associated records, and ratings. Error: " . $e->getMessage();
    }
}

$users_query = "SELECT * FROM users";
$users_result = mysqli_query($connection, $users_query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>User List - Admin</title>
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

        <?php if (isset($_SESSION['delete_user_error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['delete_user_error']; ?>
            </div>
            <?php unset($_SESSION['delete_user_error']); ?>
        <?php endif; ?>

        <h2>User List</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($users_result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
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
