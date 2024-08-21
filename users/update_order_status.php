<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    $update_query = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($connection, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Order status updated successfully.";
    } else {
        $_SESSION['update_order_status_error'] = "Failed to update order status. Please try again later.";
    }

    mysqli_stmt_close($stmt);
}

header('Location: sales.php');
exit();
?>
