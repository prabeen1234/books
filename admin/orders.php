<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('config.php');
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    // Delete order from the orders table
    $delete_query = "DELETE FROM orders WHERE order_id = ?";
    $stmt = mysqli_prepare($connection, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $order_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Order deleted successfully.";
    } else {
        $_SESSION['delete_order_error'] = "Failed to delete order. Please try again later.";
    }

    mysqli_stmt_close($stmt);
}

$orders_query = "SELECT o.order_id, o.book_id, o.buyer_name, o.buyer_address, o.buyer_phone, o.seller_id, o.order_date, b.name AS book_name, u.name AS seller_name 
                 FROM orders o
                 JOIN books b ON o.book_id = b.id
                 JOIN users u ON o.seller_id = u.id";
$orders_result = mysqli_query($connection, $orders_query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders - Admin</title>
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

        <?php if (isset($_SESSION['delete_order_error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['delete_order_error']; ?>
            </div>
            <?php unset($_SESSION['delete_order_error']); ?>
        <?php endif; ?>

        <h2>Orders</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Book Name</th>
                    <th>Seller Name</th>
                    <th>Buyer Name</th>
                    <th>Buyer Address</th>
                    <th>Buyer Phone</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['book_name']; ?></td>
                        <td><?php echo $row['seller_name']; ?></td>
                        <td><?php echo $row['buyer_name']; ?></td>
                        <td><?php echo $row['buyer_address']; ?></td>
                        <td><?php echo $row['buyer_phone']; ?></td>
                        <td><?php echo $row['order_date']; ?></td>
                        <td>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <button type="submit" name="delete_order" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</button>
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
