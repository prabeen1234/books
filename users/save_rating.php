<?php 
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rating']) && isset($_POST['book_id']) && isset($_POST['user_id'])) {
        $rating = intval($_POST['rating']);
        $bookId = intval($_POST['book_id']);
        $userId = intval($_POST['user_id']);

        // Query to fetch the seller_id of the book
        $query = "SELECT seller_id FROM books WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "i", $bookId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $sellerId);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($sellerId === $userId) {
            echo "You cannot rate your own book.";
        } else {
            // Proceed with the rating insertion
            $insertQuery = "INSERT INTO ratings (user_id, book_id, rating) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($connection, $insertQuery);
            mysqli_stmt_bind_param($stmt, "iii", $userId, $bookId, $rating);

            if (mysqli_stmt_execute($stmt)) {
                echo "Rating saved successfully!";
            } else {
                echo "Failed to save the rating.";
            }

            mysqli_stmt_close($stmt);
        }
    } else {
        echo "Invalid data.";
    }
} else {
    echo "Invalid request.";
}
