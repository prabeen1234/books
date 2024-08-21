<?php
// Start a session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];
    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    }

    if (empty($errors)) {
        $query = "SELECT id, email, password FROM users WHERE email = '$email'";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['name'] = $name;
            $_SESSION['phone'] = $phone;
            $_SESSION['address'] = $address;
                header('Location: index.php');
                exit();
            } else {
                $errors[] = "Incorrect email or password.";
            }
        } else {
            $errors[] = "Incorrect email or password.";
        }
    }
}

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header('Location: login.php');
    exit();
}
?>
