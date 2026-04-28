<?php
include_once 'includes/header.php'; //This will put the header at the top of the page
?>

<!-- Checks if the user on the page is logged in as an admin -->
<?php if ($_SESSION['user_type'] != 'admin'):
    header("Location: index.php");
    exit;
?>

<?php endif; ?>

<div class="dashboard-container">
    
    <div class="dashboard">

        <h1>Admin Dashboard</h1>

        <!--This is to make the orders appear on one side and the user details appear on the other -->
        <div class="collumn-container">

            <div class="collumn">
                <!-- This will display all the products -->
                <div class="product-sec">
                    <h3>Product:</h3>
                        <div class="scroll-box">
                            <?php
                                $producer_id = $_SESSION['user_id'];

                                $sql = "SELECT * FROM inventory"; //Takes all the products from the inventory
                                $stmt = $pdo->query($sql);
                                $inventory = $stmt->fetchAll();

                                foreach ($inventory as $row) {
                                    echo "<h4>" . htmlspecialchars($row['product']);
                                }
                            
                            ?>
                        </div>

                </div>

            </div>

            <div class="collumn">

                <!-- This section will display all the details about the producers and will allow the admin to create / delete producer accounts -->
                <div class="admin-dash-user-sec">
                    
                    <h3>Producers:</h3>

                    <!-- This will get all the producers details from the database and display it here -->
                    <div class="scroll-box">
                        <?php
                            $sql = "SELECT * FROM producer ORDER BY created_at ASC";
                            $stmt = $pdo->query($sql);
                            $producer = $stmt->fetchAll();

                            foreach ($producer as $row) {
                                echo "<h4>" . htmlspecialchars($row['username']) . " " . htmlspecialchars($row['email']) . "</h4>";
                            }

                        ?>
                    </div>

                    <!-- Redirects the user to the page to add a new producer -->
                    <div class="dash-button">
                        <a href="prod-acc-creation.php">New Producer</a>
                    </div>

                </div>

                <div class="admin-dash-user-sec">
                    <h3>Customers:</h3>


                    <div class="scroll-box">
                        <!-- This will get all the producers details from the database and display it here -->
                        <?php
                            $sql = "SELECT * FROM customer ORDER BY created_at ASC";
                            $stmt = $pdo->query($sql);
                            $customer = $stmt->fetchAll();

                            foreach ($customer as $row) {
                                echo "<h4>" . htmlspecialchars($row['username']) . " " . htmlspecialchars($row['email']) . "</h4>";
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