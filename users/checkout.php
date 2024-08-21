<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('config.php');
ini_set('display_errors', 1);
require_once('navbar.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['book_ids']) && is_array($_GET['book_ids'])) {
        $bookIds = $_GET['book_ids'];
    } else {
        $_SESSION['cart_errors'] = "Invalid book IDs.";
        header('Location: cart.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book_ids']) && is_array($_POST['book_ids'])) {
        // Get the array of book IDs from the $_POST superglobal
        $bookIds = $_POST['book_ids'];
    } else {
        $_SESSION['cart_errors'] = "Invalid book IDs.";
        header('Location: cart.php');
        exit();
    }

    // Check if the required buyer information is provided
    if (empty($_POST['buyer_name']) || empty($_POST['buyer_address']) || empty($_POST['buyer_phone'])) {
        $_SESSION['cart_errors'] = "Please provide your name, address, and phone number.";
        header('Location: cart.php');
        exit();
    }

    // Process the order and payment here (you can implement this part)
    // For the sake of this example, let's assume the order is placed and the payment is successful.
    // After successful payment, insert the order details into the 'orders' table

    $buyerName = $_POST['buyer_name'];
    $buyerAddress = $_POST['buyer_address'];
    $buyerPhone = $_POST['buyer_phone'];
    $currentUserId = $_SESSION['user_id'];
    
    $name=$_SESSION['name'];
    $address=$_SESSION['address'];
    $phone=$_SESSION['phone'];

    $orderDate = date('Y-m-d H:i:s');

    // Loop through each book ID and process the checkout for each book
    foreach ($bookIds as $bookId) {
        $selectQuery = "SELECT * FROM books WHERE id = ?";
        $stmt = mysqli_prepare($connection, $selectQuery);
        mysqli_stmt_bind_param($stmt, "i", $bookId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $book = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $sellerId = $book['seller_id'];

        if ($sellerId == $_SESSION['user_id']) {
            $_SESSION['cart_errors'] = "You cannot buy your own book.";
            header('Location: cart.php');
            exit();
        }

        // Insert the order details into the 'orders' table for each book
        $insertQuery = "INSERT INTO orders (book_id, buyer_id, buyer_name, buyer_address, buyer_phone, seller_id, order_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($connection, $insertQuery);
        mysqli_stmt_bind_param($stmt, "iisssis", $bookId, $currentUserId, $buyerName, $buyerAddress, $buyerPhone, $sellerId, $orderDate);

        if (!mysqli_stmt_execute($stmt)) {
            $_SESSION['cart_errors'] = "Failed to place the order. Please try again later.";
            header('Location: cart.php');
            exit();
        }

        mysqli_stmt_close($stmt);
    }

    // Empty the cart after placing the orders
    $_SESSION['cart'] = array();
    $_SESSION['success_message'] = "Order(s) placed successfully. Thank you for your purchase!";
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <?php if (isset($_SESSION['cart_errors'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['cart_errors']; ?>
            </div>
            <?php unset($_SESSION['cart_errors']); ?>
        <?php endif; ?>
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
        <h2>Checkout</h2>
        <?php if (isset($bookIds) && !empty($bookIds)): ?>
            <?php foreach ($bookIds as $bookId): ?>
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
                    <img src="<?php echo $imageSrc; ?>" alt="Book Image" class="card-img-top" style="height: 300px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $book['name']; ?></h5>
                        <p class="card-text">Genre: <?php echo $book['genre']; ?></p>
                        <p class="card-text">Author: <?php echo $book['author']; ?></p>
                        <p class="card-text">Price: RS <?php echo $book['selling_price']; ?></p>
                        <p class="card-text">Location: <?php echo $book['address']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <h3>Your Details</h3>
            <form action="checkout.php" method="POST">
                <?php foreach ($bookIds as $bookId): ?>
                    <input type="hidden" name="book_ids[]" value="<?php echo $bookId; ?>">
                <?php endforeach; ?>
                <div class="form-group">
                    <label for="buyer_name">Name:</label>
                    <input type="text" value="<?php echo $_SESSION['name']; ?>" class="form-control" id="buyer_name" name="buyer_name" required>
                </div>
                <div class="form-group">
                    <label for="buyer_address">Address:</label>
                    <input class="form-control" id="buyer_address"value="<?php echo $_SESSION['address']; ?>"  name="buyer_address" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="buyer_phone">Phone:</label>
                    <input value="<?php echo $_SESSION['phone']; ?>"  type="text" class="form-control" id="buyer_phone" name="buyer_phone" required>
                </div>
                <button type="submit" class="btn btn-primary">Checkout</button>
            </form>
        <?php else: ?>
            <p>No books selected for checkout.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
