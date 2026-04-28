<?php
session_start();
require_once 'includes/dbconn.php';

//Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$error_message = null;

$delivery_requested = isset($_POST['delivery']) ? 1 : 0;

try {
    $pdo->beginTransaction();

    //Get cart items and their prices
    $stmt = $pdo->prepare("SELECT cart.product_id, cart.quantity, inventory.price,
 inventory.stock FROM cart JOIN inventory ON cart.product_id = inventory.id WHERE cart.customer_id = ? FOR UPDATE
    ");
    $stmt->execute([$customer_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$cartItems) {
        throw new Exception("Your cart is empty.");
    }

    //Create order
    $orderStmt = $pdo->prepare("INSERT INTO orders (customer_id, delivery, completed) VALUES (?, ?, 0)");
    $orderStmt->execute([$customer_id, $delivery_requested]);

    $order_id = $pdo->lastInsertId();

    //Insert order items and update stock
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, cost) VALUES (?, ?, ?, ?)");

    $stockStmt = $pdo->prepare("UPDATE inventory SET stock = stock - ? WHERE id = ?");

    foreach ($cartItems as $item) {

        //Make sure that there is enough stock
        if ($item['quantity'] > $item['stock']) {
            throw new Exception("Not enough stock for product ID: " . $item['product_id']);
        }

        $cost = $item['price'] * $item['quantity'];

        $itemStmt->execute([$order_id, $item['product_id'], $item['quantity'], $cost]);

        //Reduce stock by the quantity bought
        $stockStmt->execute([$item['quantity'], $item['product_id']]);
    }

    //Remove items from the cart
    $clear = $pdo->prepare("DELETE FROM cart WHERE customer_id = ?");
    $clear->execute([$customer_id]);

    $pdo->commit();

    //Redirect to next relevant page
    if ($delivery_requested) {
        header("Location: delivery.php?order_id=" . $order_id);
    } else {
        header("Location: user.php?");
    }
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $error_message = $e->getMessage();
}

//Page only shows if there is an error, otherwise it is unseen
include_once 'includes/header.php';//This will put the header at the top of the page
?>

<!-- Checks if the user on the page is logged in as an customer -->
<?php if ($_SESSION['user_type'] != 'customer'):
    header("Location: index.php");
    exit;
?>

<?php endif; ?>


<!-- In the case that the order doesn't go through correctly display this to the user -->
<div>
    <h2>Order Error</h2>

    <?php if ($error_message): ?>
        <div>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php else: ?>
        <div>
            Something went wrong placing your order.
        </div>
    <?php endif; ?>

    <a href="cart.php">Go back to cart</a>
</div>

<?php include_once 'includes/footer.php'; //This will put the footer at the bottom of the page?>