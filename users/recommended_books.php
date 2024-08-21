<?php
include('config.php');
require_once('navbar.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);

// Fetch and display recommended books based on data received from Flask API
if (isset($_SESSION['user_id'])) {
    $user_id=$_SESSION['user_id'];
   

$data = json_encode(['user_id' => $user_id]);
$ch = curl_init('http://localhost:5000/recommendations');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$recommendations = json_decode($response, true);
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
    .star {
        cursor: pointer;
        font-size: 20px;
        color: gray;
        transition: color 0.3s;
    }

    .star:hover, .star.active {
        color: yellow;
    }

    .low-rating {
        color: red;
    }

    .medium-rating {
        color: orange;
    }

    .high-rating {
        color: green;
    }
    </style>
    <title>Recommended Books</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Recommended Books</h1>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <div class="row">
            <?php
            if (isset($recommendations['recommendations']) && !empty($recommendations['recommendations'])) {
                include('config.php');
                $recommendedBookIds = $recommendations['recommendations'];
                $recommendedBooks = array();

                foreach ($recommendedBookIds as $book_id) {
                    $query = "SELECT * FROM books WHERE id = $book_id";
                    $result = mysqli_query($connection, $query);
                    $book = mysqli_fetch_assoc($result);
                    $recommendedBooks[] = $book;
                }

                foreach ($recommendedBooks as $book) {
                    // Display book information with the same styling as the index page
                    ?>
                    <div class="col-lg-4">
                        <div class="card mb-4 bg-light">
                            <?php
                            $imageData = base64_encode($book['image']);
                            $imageSrc = "data:image/jpeg;base64," . $imageData;
                            ?>
                            <img src="<?php echo $imageSrc; ?>" alt="Book Image" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $book['name']; ?></h5>
                                <p class="card-text">Genre: <?php echo $book['genre']; ?></p>
                                <p class="card-text">Author: <?php echo $book['author']; ?></p>
                                <p class="card-text">Price: RS <?php echo $book['selling_price']; ?></p>
                                <p class="card-text">Location: <?php echo $book['address']; ?></p>
                                <div class="rating">
                                    <!-- Add your rating code here -->
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Add to cart
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo 'No recommendations available.';
            }
            ?>
        </div>
    </div>

   

    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

    <script>
        const stars = document.querySelectorAll('.star');

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const rating = parseInt(star.getAttribute('data-value'));
                const bookId = star.getAttribute('data-book-id');
                
                sendRatingToServer(rating, bookId);
            });
        });

        function sendRatingToServer(rating, bookId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_rating.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log('Rating saved:', rating);
                    updateRatingUI(rating, bookId);
                }
            };
            const data = 'rating=' + encodeURIComponent(rating) + '&book_id=' + encodeURIComponent(bookId);
            xhr.send(data);
        }

        function updateRatingUI(rating, bookId) {
            const bookRating = document.querySelector(`.rating[data-book-id="${bookId}"]`);
            const selectedRatingElement = bookRating.querySelector('.selected-rating');
            const stars = bookRating.querySelectorAll('.star');
            selectedRatingElement.textContent = rating;

            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList remove('active');
                }
            });

            const addButton = document.querySelector(`button[data-book-id="${bookId}"]`);
            addButton.textContent = 'Rated';
            addButton.disabled = true;
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
