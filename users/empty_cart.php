<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['cart']);
$_SESSION['success_message'] = " cart has been empty.";

header('Location: cart.php');
exit();
