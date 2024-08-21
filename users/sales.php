<?php
include('config.php');
require_once('navbar.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

$query = "SELECT orders.order_id, books.name AS book_name, orders.buyer_address, orders.buyer_phone, orders.order_date, orders.status
          FROM orders
          INNER JOIN books ON orders.book_id = books.id
          WHERE orders.seller_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Sales</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-warning">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Book Name</th>
                    <th>Buyer Address</th>
                    <th>Buyer Phone</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['book_name']; ?></td>
                        <td><?php echo $order['buyer_address']; ?></td>
                        <td><?php echo $order['buyer_phone']; ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td><?php echo $order['status']; ?></td>
                        <td>
                    <?php if ($order['status'] === 'Pending'): ?>
                        <form action="update_order_status.php" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <select name="new_status" class="form-control">
                                <option value="Accepted">Accept</option>
                                <option value="Rejected">Reject</option>
                            </select>
                            <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                        </form>
                    <?php endif; ?>
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
