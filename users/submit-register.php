<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: afterlogin.php');
    exit();
}

require_once('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $errors = [];
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($address)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Please enter a 10-digit phone number.";
    }

    $emailCheckQuery = "SELECT id FROM users WHERE email = '$email'";
    $emailCheckResult = mysqli_query($connection, $emailCheckQuery);
    if (mysqli_num_rows($emailCheckResult) > 0) {
        $errors[] = "Email already exists. Please use a different email address.";
    }

    $phoneCheckQuery = "SELECT id FROM users WHERE phone = '$phone'";
    $phoneCheckResult = mysqli_query($connection, $phoneCheckQuery);
    if (mysqli_num_rows($phoneCheckResult) > 0) {
        $errors[] = "Phone number already exists. Please use a different phone number.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (name, email, password, phone, address) 
                  VALUES ('$name', '$email', '$hashedPassword', '$phone', '$address')";

        if (mysqli_query($connection, $query)) {
            header('Location: login.php?success=1');
            exit();
        } else {
            $errors[] = "Error: Unable to register user. Please try again later.";
        }
    }
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    header('Location: register.php');
    exit();
}
?>
