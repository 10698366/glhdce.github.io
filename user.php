<?php
session_start();

require_once 'includes/dbconn.php';

$customer_id = $_SESSION['user_id'];

//Grab the order items of the customer from the database
$stmt = $pdo->prepare("SELECT orders.id AS order_id, inventory.product AS product_name, order_items.quantity, order_items.cost FROM orders JOIN order_items ON orders.id = order_items.order_id JOIN inventory ON order_items.product_id = inventory.id WHERE orders.customer_id = :customer_id ORDER BY orders.id DESC");

$stmt->execute(['customer_id' => $customer_id]);
$orderRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Structure orders array
$orders = [];

foreach ($orderRows as $row) {
    $orderId = $row['order_id'];

    $orders[$orderId]['items'][] = [
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'cost' => $row['cost']
    ];
}

//Grab deliveries of the customer from the database
$stmt2 = $pdo->prepare("SELECT deliveries.order_id, deliveries.delivery_date, deliveries.delivery_time FROM deliveries JOIN orders ON deliveries.order_id = orders.id WHERE orders.customer_id = :customer_id ORDER BY deliveries.delivery_date DESC");

$stmt2->execute(['customer_id' => $customer_id]);
$deliveries = $stmt2->fetchAll(PDO::FETCH_ASSOC);

//Attach deliveries to orders
foreach ($deliveries as $delivery) {
    $orderId = $delivery['order_id'];

    $orders[$orderId]['delivery'] = [
        'date' => $delivery['delivery_date'],
        'time' => $delivery['delivery_time']
    ];
}

include_once 'includes/header.php';//This will put the header at the top of the page
?>

<!-- Checks if the user on the page is logged in as an customer -->
<?php if ($_SESSION['user_type'] != 'customer'):
    header("Location: index.php");
    exit;
?>

<?php endif; ?>


<div class="dashboard-container">
    <div class="dashboard">
        <h1>Account Overview</h1>
            
        <div class="collumn-container">

            <div class="collumn">
                
                <!-- Displays the customer's orders as well as deliveries if there are any -->
                <div class="customer-sub-head">
                    <h3>Your Orders:</h3>
                </div>

                <div class="customer-order-sec">
                
                    <div class="scroll-box">

                        <?php foreach ($orders as $orderId => $order): ?>

                            <div class='row-content-container'>
                                <div class='row-content'>
                                    <h4>Order #<?= htmlspecialchars($orderId) ?></h4>

                                    <?php foreach ($order['items'] as $item): ?>
                                        <p>Product: <?= htmlspecialchars($item['product_name']) ?></p>
                                        <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                                        <p>Price: £<?= htmlspecialchars(number_format($item['cost'], 2)) ?></p>
                                        <hr>
                                    <?php endforeach; ?>

                                    <?php if (!empty($order['delivery'])): ?>
                                        <p>Delivery:</p>
                                        <p>Date: <?= date("d M Y", strtotime($order['delivery']['date'])) ?></p>
                                        <p>Time: <?= date("H:i", strtotime($order['delivery']['time'])) ?></p>
                                    <?php else: ?>
                                        <p>Order For Collection</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                        <?php endforeach; ?>

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