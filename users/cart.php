<?php
session_start();
include('config.php');
require_once('navbar.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book_id']) && !empty($_POST['book_id'])) {
        $bookId = $_POST['book_id'];
        unset($_SESSION['cart'][$bookId]);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
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

        <h2>Cart</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $bookId => $value): ?>
                <?php
                $selectQuery = "SELECT * FROM books WHERE id = ?";
                $stmt = mysqli_prepare($connection, $selectQuery);
                mysqli_stmt_bind_param($stmt, "i", $bookId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $book = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);
                ?>

                <div class="card mb-4">
                    <?php
                    $imageData = base64_encode($book['image']);
                    $imageSrc = "data:image/jpeg;base64," . $imageData;
                    ?>
                    <img src="<?php echo $imageSrc; ?>" alt="Book Image" class="card-img-top" style="height: 150px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $book['name']; ?></h5>
                        <p class="card-text">Genre: <?php echo $book['genre']; ?></p>
                        <p class="card-text">Author: <?php echo $book['author']; ?></p>
                        <p class="card-text">Price: RS <?php echo $book['selling_price']; ?></p>
                        <p class="card-text">Location: <?php echo $book['address']; ?></p>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
                            <button type="submit" class="btn btn-danger">Remove from Cart</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <form action="checkout.php" method="GET">
                <?php foreach ($_SESSION['cart'] as $bookId => $value): ?>
                    <input type="hidden" name="book_ids[]" value="<?php echo $bookId; ?>">
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary">Checkout</button>
            </form>

            <form action="empty_cart.php" method="POST">
                <button type="submit" class="btn btn-danger mt-3">Empty Cart</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty. Continue shopping!</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
