<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('config.php');

// ini_set('display_errors', 1);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchTerm = $_POST['search'];
    
    if (!empty($searchTerm)) {
        $searchPattern = "%" . $searchTerm . "%";
        $sql = "SELECT * FROM books WHERE name LIKE ? OR author LIKE ? OR genre LIKE ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $searchPattern, $searchPattern, $searchPattern);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }
}
   
   ?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Online Marketplace</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <?php if (!isset($_SESSION['user_id'])): ?>
               
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signup.php">Signup</a>
                </li>
            <?php else: ?>
            
                <li class="nav-item">
                    <a class="nav-link" href="sell_book.php">Sell Book</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="my_orders.php">My Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sales.php">Sales</a>
                </li>
                 <a class="navbar-brand" href="index.php">Online Marketplace</a>
    <a class="nav-link ml-auto" href="cart.php">
        <i class="fas fa-shopping-cart">cart</i>
        <?php
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            echo '<span class="badge badge-danger">' . count($_SESSION['cart']) . '</span>';
        }
        ?>
    </a>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <?php endif; ?>
            <li class="nav-item ml-2">
    <form class="form-inline my-2 my-lg-0" action="index.php" method="POST">
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="search" value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
</li>

        </ul>
    </div>
</nav>
