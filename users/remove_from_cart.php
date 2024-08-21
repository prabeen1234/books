<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book_id']) && !empty($_POST['book_id'])) {
        $bookId = $_POST['book_id'];

        // Check if the book exists in the cart
        if (isset($_SESSION['cart'][$bookId])) {
            // Remove the book from the cart
            unset($_SESSION['cart'][$bookId]);
            $_SESSION['success_message'] = "Book removed from the cart successfully.";
        } else {
            $_SESSION['cart_errors'] = "Book not found in the cart.";
        }
    } else {
        $_SESSION['cart_errors'] = "Invalid book ID.";
    }
}

header('Location: cart.php');
exit();
?>
