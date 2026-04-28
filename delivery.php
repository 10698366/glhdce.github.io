<?php
session_start();

require_once 'includes/dbconn.php';

try {

    $message = "";
    $error_message = "";

    //Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $order_id = $_POST['order_id'];
        $date = $_POST['delivery_date'];
        $time = $_POST['delivery_time'];

        $sql = "INSERT INTO deliveries (order_id, delivery_date, delivery_time) VALUES (:order_id, :delivery_date, :delivery_time)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':delivery_date', $date);
        $stmt->bindParam(':delivery_time', $time);

        $stmt->execute();

        $message = "Delivery scheduled successfully!";
    }

    $order_id = $_GET['order_id'] ?? '';

} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}

include_once 'includes/header.php'; //This will put the header at the top of the page
?>

<div class="delivery-container">
    
    <div class="delivery">

        <h1>Schedule Delivery</h1>

            <?php if ($message): ?>
                <div>
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php elseif ($error_message): ?>
                <div>
                    <?php echo "<h3>" . htmlspecialchars($error_message) . "</h3>"; ?>
                </div>

            <?php else: ?>

                <!-- Takes the date and the time that the user wants the delivery to be scheduled for -->
                <form method="post">

                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">

                    <div class="delivery-inputs">
                        
                        <div>
                            <label>Delivery Date:</label>
                            <input type="date" name="delivery_date" min="2026-04-17" max="2026-12-31" required>
                        </div>

                        <div>
                            <label>Delivery Time:</label>
                            <input type="time" name="delivery_time" min="07:00" max="21:00" required>
                        </div>

                        <button type="submit">Confirm Delivery</button>
                    </div>
                
                </form>

            <?php endif; ?>
        </div>

    </div>

</div>

<?php
include_once 'includes/footer.php';//This will put the footer at the bottom of the page
?>