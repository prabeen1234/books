<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f0f0;
        }
        .containers {
            max-width: 500px;
            margin-top: 50px;
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php
    require_once('navbar.php');
    include('config.php');
    ?>

    <div class="containers container">
        <img src="images/logo.png" alt="Logo" class="img-fluid mx-auto d-block mb-4" style="max-width: 200px;">

        <h2>User Registration</h2>
        <form action="submit-register.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="8">
                <small class="form-text text-muted">Password must be at least 8 characters long.</small>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" class="form-control" id="phone" name="phone" required pattern="[0-9]{10}">
                <small class="form-text text-muted">Please enter a 10-digit phone number.</small>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
