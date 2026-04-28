<?php
include_once 'includes/header.php'; //This will put the header at the top of the page
?>

<div class="product-page-header-container">

    <div class="product-page-header">
        <h1>Product Page<h1>
    </div>

</div>

<div class="wrapper">

    <div class="product-container">

    <?php
    $sql = "SELECT * FROM inventory ORDER BY producer_id DESC"; //Orders so all the products from the same producers are grouped together
    $stmt = $pdo->query($sql);
    $inventory = $stmt->fetchAll();

    if ($inventory) {

        //Formats the way the products are displayed
        foreach ($inventory as $row) {

            //Makes sure that only available products are shown
            if (!$row['stock'] == 0) {
                echo "<div class= 'row-content-container'>";
                echo "<div class= 'row-content'>";

                echo "<img src='/Greenfield_Local_Hub/Day_6/assets/img/placeholder.png' alt='Product Photo'>";
                echo "<h4>" . htmlspecialchars($row['product']) . "</h4>";
                echo "<p>" . "£" . htmlspecialchars(number_format($row['price'], 2)) . "</p>"; //Displays the price as pounds
                echo "<p>" . htmlspecialchars($row['stock']) . " Available" . "</p>"; //Allows the customer to know how many are left

                //This will add the product to the user's cart
                echo "<div class='add-to-cart'>";
                echo "<form method='POST' action='cart.php'>";
                echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
                echo "<button type='submit'>Add to Cart</button>";
                echo "</form>";
                echo "</div>";

                echo "</div>";
                echo "</div>";
            }
        }
    } else {
        echo "No Products Available";
    }

    ?>

    </div>

</div>

<?php
include_once 'includes/footer.php';//This will put the footer at the bottom of the page
?>