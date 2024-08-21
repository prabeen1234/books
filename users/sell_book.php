<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('config.php');
// ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $author = $_POST['author'];
    $originalPrice = floatval($_POST['original_price']);
    $address = $_POST['address'];
    $genre = $_POST['genre']; 

    $sellingPrice = $originalPrice - ($originalPrice * 0.3);

    $sellerId = $_SESSION['user_id'];
    
    if (isset($_FILES['bookImage'])) {
        $file = $_FILES['bookImage'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_type = $file['type'];
    
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Invalid file type. Please upload an image in JPG, PNG, or JPEG format.";
        } else {
            $image_data = file_get_contents($file_tmp);

            $insertQuery = "INSERT INTO books (name, image, author, original_price, selling_price, address, seller_id, genre) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($connection, $insertQuery);
            mysqli_stmt_bind_param($stmt, "ssssdsis", $name, $image_data, $author, $originalPrice, $sellingPrice, $address, $sellerId, $genre);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Book details added successfully.";
                header('Location: index.php');
                exit();
            } else {
                $_SESSION['sell_book_errors'] = "Failed to add book details. Please try again later.";
                header('Location: sell_book.php');
                exit();
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Sell Your Book</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php
    require_once('navbar.php');
    ?>

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
        <?php if (isset($_SESSION['sell_book_errors'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['sell_book_errors']; ?>
            </div>
            <?php unset($_SESSION['sell_book_errors']); ?>
        <?php endif; ?>

        <h2>Sell Your Book</h2>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Book Name:</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="author">Author:</label>
        <input type="text" class="form-control" id="author" name="author" required>
    </div>
    <div class="form-group">
        <label for="original_price">Original Price:</label>
        <input type="number" step="0.01" class="form-control" id="original_price" name="original_price" required
               oninput="calculateSellingPrice()">
    </div>
    <div class="form-group">
        <label for="image">Book Image:</label>
        <input type="file" class="form-control-file" id="image" name="bookImage">
        <small class="form-text text-muted">Supported formats: jpg, jpeg, png, gif.</small>
    </div>
    <div class="form-group">
        <label for="genre">Genre:</label>
        <select class="form-control" id="genre" name="genre">
            <option value="fiction">Fiction</option>

            <option value="non-fiction">Non-Fiction</option>
            <option value="study">study</option>

            <option value="mystery">Mystery</option>
            <option value="romance">Romance</option>
            <option value="science-fiction">Science Fiction</option>
        </select>
    </div>
    <div class="form-group">
        <label for="selling_price">Selling Price:</label>
        <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" readonly>
    </div>
    <div class="form-group">
        <label for="address">Address to Receive Book:</label>
        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>


    <script>
        function calculateSellingPrice() {
            const originalPrice = parseFloat(document.getElementById('original_price').value);
            const discount = 0.3;
            const sellingPrice = originalPrice - (originalPrice * discount);

            document.getElementById('selling_price').value = sellingPrice.toFixed(2);
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
