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

$query = "SELECT o.*, b.name AS book_name, b.seller_id, b.selling_price
          FROM orders o
          INNER JOIN books b ON o.book_id = b.id
          WHERE o.buyer_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .status-pending {
            color: #ffc107;
        }
        .status-accepted {
            color: #28a745;
        }
        .status-delivered {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">My Orders</h2>
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
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Order ID</th>
                    <th>Book Name</th>
                    <th>Seller ID</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['book_name']; ?></td>
                        <td><?php echo $order['seller_id']; ?></td>
                        <td><?php echo 'RS ' . $order['selling_price']; ?></td>
                        <td class="status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></td>
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
