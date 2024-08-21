<?php
include('config.php');
require_once('navbar.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book_id']) && !empty($_POST['book_id'])) {
        $bookId = $_POST['book_id'];
      
        $_SESSION['cart'][$bookId] = true;
    }
}

$purchasedBookIds = array_keys($_SESSION['cart'] ?? []);

$ordersQuery = "SELECT DISTINCT book_id FROM orders";
$ordersResult = mysqli_query($connection, $ordersQuery);
$orderedBookIds = array();
while ($row = mysqli_fetch_assoc($ordersResult)) {
    $orderedBookIds[] = $row['book_id'];
}
$excludedBookIds = array_merge($purchasedBookIds, $orderedBookIds);

if (isset($_POST['search'])) {
    $searchTerm = "%" . $_POST['search'] . "%";
    $inClause = rtrim(str_repeat('?,', count($excludedBookIds)), ','); // Create placeholders
    $types = str_repeat('s', count($excludedBookIds)); // Types for binding

    if (empty($excludedBookIds)) {
        // If $excludedBookIds is empty, set $sql without the IN clause
        $sql = "SELECT * FROM books WHERE name LIKE ? OR author LIKE ? OR genre LIKE ?";
        $types = "sss";
        $params = array($types, $searchTerm, $searchTerm, $searchTerm);
    } else {
        // $excludedBookIds is not empty, include it in the query
        $sql = "SELECT * FROM books WHERE (name LIKE ? OR author LIKE ? OR genre LIKE ?) AND id NOT IN ($inClause)";
        $params = array_merge(array($types, $searchTerm, $searchTerm, $searchTerm), $excludedBookIds);
    }

    $stmt = mysqli_prepare($connection, $sql);

    if ($stmt === false) {
        die(mysqli_error($connection));
    }

    // Bind the parameters
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $params));

    // Execute the prepared statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Your code for handling other cases remains the same
    if (empty($excludedBookIds)) {
        $query = "SELECT * FROM books";
    } else {
        $inClause = implode(',', $excludedBookIds);
        $query = "SELECT * FROM books WHERE id NOT IN ($inClause)";
    }

    $result = mysqli_query($connection, $query);
}

if (isset($_SESSION['user_id'])) {
 $userId=$_SESSION['user_id'];

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
    <title>Online Marketplace for Used Books</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
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
     

        <div class="row">
            <?php while ($book = mysqli_fetch_assoc($result)): ?>
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
                                <?php
                                $hasRated = false;
                                $userRating = 0;
                                if (isset($_SESSION['user_id'])) {
                                    $userId = $_SESSION['user_id'];
                                    $checkRatingQuery = "SELECT * FROM ratings WHERE user_id = ? AND book_id = ?";
                                    $stmt = mysqli_prepare($connection, $checkRatingQuery);
                                    mysqli_stmt_bind_param($stmt, "ii", $userId, $book['id']);
                                    mysqli_stmt_execute($stmt);
                                    $ratingResult = mysqli_stmt_get_result($stmt);
                                    if (mysqli_num_rows($ratingResult) > 0) {
                                        $hasRated = true;
                                        $ratingRow = mysqli_fetch_assoc($ratingResult);
                                        $userRating = $ratingRow['rating'];
                                    }
                                    mysqli_stmt_close($stmt);
                                }
                                ?>
     

                                <?php if (!$hasRated && isset($_SESSION['user_id'])): ?>
                                    <span class="star"  data-user-id="<?php echo $_SESSION['user_id']; ?>" data-value="1" data-book-id="<?php echo $book['id']; ?>">&#9733;</span>
                                    <span class="star"  data-user-id="<?php echo $_SESSION['user_id']; ?>" data-value="2" data-book-id="<?php echo $book['id']; ?>">&#9733;</span>
                                    <span class="star"  data-user-id="<?php echo $_SESSION['user_id']; ?>" data-value="3" data-book-id="<?php echo $book['id']; ?>">&#9733;</span>
                                    <span class="star"  data-user-id="<?php echo $_SESSION['user_id']; ?>" data-value="4" data-book-id="<?php echo $book['id']; ?>">&#9733;</span>
                                    <span class="star"  data-user-id="<?php echo $_SESSION['user_id']; ?>" data-value="5" data-book-id="<?php echo $book['id']; ?>">&#9733;</span>
                                <?php else: ?>
                                    
                                    <span class="star <?php echo $userRating >= 1 ? 'active' : ''; ?>">&#9733;</span>
                                    <span class="star <?php echo $userRating >= 2 ? 'active' : ''; ?>">&#9733;</span>
                                    <span class="star <?php echo $userRating >= 3 ? 'active' : ''; ?>">&#9733;</span>
                                    <span class="star <?php echo $userRating >= 4 ? 'active' : ''; ?>">&#9733;</span>
                                    <span class="star <?php echo $userRating >= 5 ? 'active' : ''; ?>">&#9733;</span>
                                <?php endif; ?>
                            </div>
                            <?php
                            $averageRatingQuery = "SELECT AVG(rating) AS average_rating FROM ratings WHERE book_id = ?";
                            $stmt = mysqli_prepare($connection, $averageRatingQuery);
                            mysqli_stmt_bind_param($stmt, "i", $book['id']);
                            mysqli_stmt_execute($stmt);
                            $averageRatingResult = mysqli_stmt_get_result($stmt);
                            $averageRatingRow = mysqli_fetch_assoc($averageRatingResult);
                            $averageRating = $averageRatingRow['average_rating'];
                            mysqli_stmt_close($stmt);

                            if ($averageRating < 3) {
                                $ratingClass = 'low-rating';
                            } elseif ($averageRating >= 3 && $averageRating < 4) {
                                $ratingClass = 'medium-rating';
                            } else {
                                $ratingClass = 'high-rating';
                            }
                            ?>

                            <p class="<?php echo $ratingClass; ?>">Average Rating: <?php echo number_format($averageRating, 1); ?> &#9733;</p>
                          
                            <form action="index.php" method="POST">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">


                                <button type="submit" class="btn btn-primary">
                                   Add to cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php if (isset($_POST['search']) && mysqli_num_rows($result) === 0): ?>
                <div class="col-lg-12">
                    <p>No books found matching the search term.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        const stars = document.querySelectorAll('.star');

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const rating = parseInt(star.getAttribute('data-value'));
                const bookId = star.getAttribute('data-book-id');
                const userId = star.getAttribute('data-user-id');
                console.log('User ID:', userId);
                sendRatingToServer(rating, bookId, userId);
            });
        });
        function sendRatingToServer(rating, bookId, userId) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_rating.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Rating saved:', rating);
            updateRatingUI(rating, bookId);
        }
    };
    const data = 'rating=' + encodeURIComponent(rating) + '&book_id=' + encodeURIComponent(bookId) + '&user_id=' + encodeURIComponent(userId); // Pass user_id to save_rating.php
    xhr.send(data);
}

function updateRatingUI(rating, bookId, userId) {
    const bookRating = document.querySelector(`.rating[data-book-id="${bookId}"]`);
    const selectedRatingElement = bookRating.querySelector('.selected-rating');
    const stars = bookRating.querySelectorAll('.star');
    selectedRatingElement.textContent = rating;

    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });

            const addButton = document.querySelector(`button[data-book-id="${bookId}"]`);
            addButton.textContent = 'Rated';
            addButton.disabled = true;
        }

    </script>
<?php
include('recommended_books.php');
?>


    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
