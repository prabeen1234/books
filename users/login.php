<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
if (isset($_SESSION['login_message'])) {
    $loginMessage = $_SESSION['login_message'];
    unset($_SESSION['login_message']);
} else {
    $loginMessage = '';
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
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
    <?php
    require_once('navbar.php');
    include('config.php');

    ?>
    

    <div class="container mt-4">
        <?php if (isset($_SESSION['login_errors'])): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($_SESSION['login_errors'] as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['login_errors']); ?>
        <?php endif; ?>
        <?php if ($loginMessage): ?>
            <div class="alert alert-info"><?php echo $loginMessage; ?></div>
        <?php endif; ?>
        <h2>Login</h2>
        <form action="submit-login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
