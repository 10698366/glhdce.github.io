<?php
session_start();

require_once 'includes/dbconn.php'; // Creates the connection to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Takes all the info about the product required
    $product = $_POST['product'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $producer_id = $_SESSION['user_id'];
    $recalled = $_POST['recalled'];

    // Prepare statement
    $sql = "INSERT INTO inventory (product, stock, price, producer_id, recalled) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$product, $stock, $price, $producer_id, $recalled])) {
        header("Location: prod-dash.php?signup=success");
        exit;
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

include_once 'includes/header.php'; //This will put the header at the top of the page
?>

<!-- Checks if the user on the page is logged in as an producer -->
<?php if ($_SESSION['user_type'] != 'producer'):
    header("Location: index.php");
    exit;
?>

<?php endif; ?>

<!-- This is where the data for the new product will be taken from -->
<!-- I use the same div classes as in the login and sign up forms due to their layouts being the same -->
<div class="form-container">
    <div class="form">
        <h1>Add Product</h1>

        <form method="POST">

            <!-- Use basic types of validation to prevent invalid data from being entered -->
            <div class="input-area">
                <label for="product" class="form-label">Product Name:</label>
                <input type="text" id="product" name="product" required autocomplete="off">
            </div>

            <div class="input-area">
                <label for="stock" class="form-label">Stock:</label>
                <input type="number" id="stock" name="stock" min="0" required autocomplete="off">
            </div>

            <div class="input-area">
                <label for="price" class="form-label">Price:</label>
                <input type="number" id="price" name="price" step="0.01" min="0.01" required autocomplete="off">
            </div>

            <input type="hidden" name="recalled" value="0">

            <div class="login-signup-button">
                <button type="submit">Add Product</button>
            </div>

        </form>
    </div>
</div>

<?php
include_once 'includes/footer.php';//This will put the footer at the bottom of the page
?>