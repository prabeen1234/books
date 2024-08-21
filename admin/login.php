<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }

        .login-box {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #007bff;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}    include('config.php'); 
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        header("Location: admin_dashboard.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "SELECT * FROM admins WHERE username = ? AND password = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['id'] = $admin_user_id;

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $loginError = "Invalid username or password.";
        }
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="login-box">
                    <h2 class="text-center">Admin Login</h2>
                    <?php if (isset($loginError)): ?>
                        <div class="alert alert-danger"><?php echo $loginError; ?></div>
                    <?php endif; ?>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
