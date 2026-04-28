<?php
session_start();

include_once 'includes/header.php'; //This will put the header at the top of the page
?>

<!-- Checks if the user on the page is logged in as an producer -->
<?php if ($_SESSION['user_type'] != 'producer'):
    header("Location: index.php");
    exit;
?>

<?php endif; ?>

<div class="dashboard-container">

    <div class="dashboard">

        <h1>Producer Dashboard</h1>

        <!-- Makes the products and the orders appear side by side -->
        <div class="collumn-container">

            <div class="collumn">

                <!-- This outputs all the products from the logged in producer -->
                <div class="product-sec">
                    <h3>Product:</h3>

                        <div class="scroll-box">
                        <?php
                            $producer_id = $_SESSION['user_id'];

                            $sql = "SELECT * FROM inventory WHERE producer_id= $producer_id"; //Takes only the products from the producer that is logged in
                            $stmt = $pdo->query($sql);
                            $inventory = $stmt->fetchAll();

                            foreach ($inventory as $row) {
                                echo "<h4>" . htmlspecialchars($row['product']);
                            }
                        
                        ?>
                        </div>
                </div>
                
                <!-- Redirects the producer to the page to add a new product -->
                <div class="dash-button">
                    <a href="create-product.php">New Product</a>
                </div>
            
            </div>

            <div class="collumn">
                <!-- This will output all the orders the producer has, only shows orders including his products -->
                <div class="product-sec">
                    <h3>Orders:</h3>

                    <div class="scroll-box">
                    <?php
                        $producer_id = $_SESSION['user_id'];

                        $sql = "SELECT order_items.order_id, inventory.product, order_items.quantity, order_items.cost FROM order_items JOIN inventory ON order_items.product_id = inventory.id WHERE inventory.producer_id = :producer_id ORDER BY order_items.order_id DESC";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['producer_id' => $producer_id]);
                        $orders = $stmt->fetchAll();

                        foreach ($orders as $row) {
                            echo "<h4>Order #" . htmlspecialchars($row['order_id']) . "</h4>";
                            echo "<h4>Product: " . htmlspecialchars($row['product']) . "</h4>";
                            echo "<h4>Quantity: " . htmlspecialchars($row['quantity']) . "</h4>";
                            echo "<h4>Cost: £" . htmlspecialchars(number_format($row['cost'] ,2 )) . "</h4>";
                            echo "<hr>";
                        }
                    ?>
                    </div>
                </div>
            
            </div>
        
        </div>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>

    </div>
</div>

<?php
include_once 'includes/footer.php';//This will put the footer at the bottom of the page
?>