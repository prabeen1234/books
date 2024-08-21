<?php
include ('navbar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #343a40;
            color: #fff;
        }

        .side-menu {
            background-color: #007bff;
            width: 250px;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 70px;
        }

        .side-menu a {
            display: block;
            padding: 15px 20px;
            color: #fff;
            font-size: 18px;
            text-decoration: none;
        }

        .side-menu a:hover {
            background-color: #0056b3;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit();
    }
    ?>

    <div class="side-menu">
        <a href="user-list.php">User</a>
        <a href="orders.php">Orders</a>
        <a href="#">Sells</a>
        <a href="book-list.php">Selling Books</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Welcome to the Admin Dashboard</h1>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
