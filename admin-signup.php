<?php
require_once 'includes/dbconn.php'; // Creates the connectiont to the database

$error_message = '';
$success_message = '';

//Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Get the form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    //Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "Username, Email and Password are required."; //Checks all fields are filled in
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid Email Format."; //Checks if the email is in correct format
    } else {

        try {
            //Check if the username already exists
            $stmt = $pdo->prepare("SELECT id FROM administrator WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error_message ="This username is already registered.";
            } else {
                //Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM administrator WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error_message = "This email address is already registered";
                } else {

                    //Hash the password for security
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    //Prepare the SQL INSERT statement using '?' as placeholders to prevent SQL injection
                    $sql = "INSERT INTO administrator (username, email, password) VALUES (?, ?, ?)";
                    $stmt = $pdo->prepare($sql);

                    //Execute the statement
                    if ($stmt->execute([$username, $email, $hashed_password])) {
                        //Successful, redirect to login
                        header("Location: login.php?signup=success");
                        exit;
                    } else {
                        $error_message ="Error: Couldn't register. Please try again.";
                    }
                }
            }
        }catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        } 
    }
}


include_once 'includes/header.php'; //This will put the header at the top of the page
?>

<!-- This HTML shows once the PHP above has run -->

<!-- SIGN UP FORM -->

<div class="form-container">
    <div class="form">
        <h2>Create an Account</h2>

        <!-- Show error message if an error exists -->
        <?php if ($error_message): ?>
            <div>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="admin-signup.php" method="POST">

            <div class="input-area">
                <label for="username" class="form-label">Username:</label>
                <input type="username" id="username" name="username" autocomplete="off" required> 
            </div>

            <div class="input-area">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" autocomplete="off" required> 
            </div>

            <div class="input-area">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" autocomplete="off" required> 
            </div>

            <div class="login-signup-button">
                <button type="submit">Sign Up</button>
            </div>

        </form>
        
        <p class="switch-login-signup">
            Already have an account? <a href="login.php">Login Here</a>
        </p>
    </div>
</div>

<?php
include_once 'includes/footer.php';//This will put the footer at the bottom of the page
?>