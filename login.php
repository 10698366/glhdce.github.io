<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//CUSTOMER LOGIN PAGE

require_once 'includes/dbconn.php'; //Connects to the database

$error_message = '';

//Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Get the form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = "Username and Password are required";

    } else {
        try {
            //Find the user in the database by their username
            $sql = "SELECT * FROM customer WHERE username = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username]);

            //Fetch the user
            $user = $stmt->fetch(); // Returns user array or false if not found

            //Check if user exists
            if (!$user) {
                $error_message = "No user found with username: " . htmlspecialchars($username);
            } else {
                //Verify the user and their password
                if (password_verify($password, $user['password'])) {

                    //Password is correct
                    //Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_type'] = 'customer'; //Determines access rights

                    //Redirect to the home page
                    header("Location: index.php");
                    exit;
                } else {
                    $error_message = "Password is incorrect for user: " . htmlspecialchars($username);
                }
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

include_once 'includes/header.php'; //This will put the header at the top of the page
?>

<!-- LOGIN FORM -->
<div class="form-container">
    <div class="form">
        <h2>Customer Login</h2>

        <!-- Show Error message if there is one -->
         <?php if ($error_message): ?>
            <div>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Show success message from sign up -->
         <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
            <div>
                Sign up successful. Please login.
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" autocomplete="off">

            <div class="input-area">
                <label for="username" class="form-label">Username:</label>
                <input type="username" id="username" name="username" value="" autocomplete="off" required>
            </div>

            <div class="input-area">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" value="" autocomplete="off" required>
            </div>

            <div class="login-signup-button">
                <button type="submit">Login</button>
            </div>

        </form>
        
        <p class="switch-login-signup">
            Don't have an account? <a href="signup.php">Sign-Up Here</a>
        </p>

        <p class="switch-login-signup">
            Trying to login as a producer? <a href="prod-login.php">Login Here</a>
        </p>

        <p class="switch-login-signup">
            Trying to login as an admin? <a href="admin-login.php">Login Here</a>
        </p>

    </div>
</div>

<?php
include_once 'includes/footer.php';//This will put the footer at the bottom of the page
?>