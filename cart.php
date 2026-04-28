<?php
session_start();

require_once 'includes/dbconn.php'; //Connects to the database

//Sends a user who isn't logged in at all to the customer login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

//Sends a user who is logged in as an admin or producer to the home page as there IDs can be the same as customers if they make an order it could accidently make an order for the customer with the same ID
elseif (
    isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'producer')){
    header("Location: index.php");
    exit;
}

$customer_id = $_SESSION['user_id'];

if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    // Add item to the cart
    $sql = "INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1"; // If the item is already in the customers cart this will add another one to the quantity instead of making another record

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id, $product_id]);

    header("Location: products.php");
    exit;
}

include_once 'includes/header.php'; //This will put the header at the top of the page

?>

<div class="cart-container">
    
    <div class="cart">

        <h1>Your Cart:</h1>

        <div class="scroll-box">
            <!-- Displays the products and quantity in the user's basket -->
            <?php

                $customer_id = $_SESSION['user_id'];

                $sql = "SELECT cart.quantity, inventory.product, inventory.price FROM cart INNER JOIN inventory ON cart.product_id = inventory.id WHERE cart.customer_id = :customer_id"; //Makes sure to only get the cart content of the user currently logged in
                //Joins the inventory and cart tables so we can retreive the products name
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['customer_id' => $customer_id]);
                $cart = $stmt->fetchAll();

                $total = 0;

                foreach ($cart as $row) {
                    
                    echo "<h4>" . htmlspecialchars($row['quantity']) . " " . htmlspecialchars($row['product']) . " £" . htmlspecialchars(number_format($row['price'] * $row['quantity'] , 2)) . "</h4>"; //Outputs the item, quantity and price

                    $total += $row['price']*$row['quantity'];
                }
                
                echo "<h4>" . "Total: £" . htmlspecialchars(number_format($total , 2));
            ?>
        </div>

        <!-- Form that begins the ordering process -->
        <form action="order.php" method="post">

            <!-- If ticked the user will be taken to delivery.php after -->
            <div class="delivery-checkbox">     
                <label>
                    <input type="checkbox" name="delivery" value=1>
                    Request Delivery
                </label>
            </div>

            <div class="cart-order-button">
                <button type="submit">Place Order</button>
            </div>


    </div>

</div>

<?php
include_once 'includes/footer.php';//This will put the footer at the bottom of the page
?>