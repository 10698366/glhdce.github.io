<?php
session_start();

include 'includes/dbconn.php';

if (!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'null';
} //If the user isn't logged in to prevent error message from showing

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $review_text = $_POST['review_text'];
    $rating = (int)$_POST['rating'];

    //Stores the user's review in the database
    $stmt = $pdo->prepare("INSERT INTO reviews (username, review_text, rating) VALUES (:username, :review_text, :rating)");
    $stmt->execute([
        ':username' => $username,
        ':review_text' => $review_text,
        ':rating' => $rating
    ]);
}

include_once 'includes/header.php'; //This will put the header at the top of the page
?>

<div class="sub-header-container">
    <div class="sub-header">
        <p>Reviews</p>
    </div>
</div>

<!-- Only accessible when the user is signed in -->
<?php if ($_SESSION['user_type'] === 'customer'): ?>

    <div class="review-form-container">

        <div class="review-form">

            <h2>Leave a Review</h2>
            <form method="POST" action="review.php">
                
                <!-- Where the user will actually write their review -->
                <textarea name="review_text" class="review-text" placeholder="Write your review..." required></textarea>

                <br>

                <!-- Allows star rating of review -->
                <div class = "review-form-bottom">
                    <select name="rating" required>
                        <option value="5">★★★★★</option>
                        <option value="4">★★★★</option>
                        <option value="3">★★★</option>
                        <option value="2">★★</option>
                        <option value="1">★</option>
                    </select>

                    <button type="submit">Submit Review</button>
                </div>

            </form>

        </div>

    </div>

<?php endif; ?>

<div class="wrapper">

    <div class="review-container">

    <!-- Review Area -->

    <!-- This is where the reviews will actually be displayed to the user -->
    <?php
    $sql = "SELECT * FROM reviews ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $reviews = $stmt->fetchAll();

    if ($reviews) {

        //Formats the way the reviews are displayed
        foreach ($reviews as $row) {
            echo "<div class= 'row-content-container'>";
            echo "<div class= 'row-content'>";
            echo "<h4>" . htmlspecialchars($row['username']) . "</h4>";
            echo "<p>" . htmlspecialchars($row['review_text']) . "</p>";
            echo "<p>Rating: " . str_repeat("★", $row['rating']) . "</p>";
            echo "<small>Posted on " . $row['created_at'] . "</small>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "No reviews yet. Be the first!";
    }

    ?>

    </div>

</div>

<?php
include_once 'includes/footer.php';//This will put the footer at the bottom of the page
?>