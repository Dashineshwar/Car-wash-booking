<?php
// Start the session at the very beginning of the script
session_start();

// Include the database connection
include '../includes/connection.php';

// Define function to sanitize and validate user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize variables
$username = $password = "";
$username_err = $password_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $username_err = "Please enter username.";
    } else {
        $username = sanitize_input($_POST["username"]);
    }

    // Validate password
    if (empty($_POST["password"])) {
        $password_err = "Please enter your password.";
    } else {
        $password = sanitize_input($_POST["password"]);
    }

    // Check input errors before querying the database
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password, email, phone_no FROM users WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if username exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password, $email, $phone_no);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["email"] = $email;
                            $_SESSION["phone"] = $phone_no;

                            // Redirect user to welcome page
                            header("location: welcome.php");
                            exit;
                        } else {
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Your styles remain unchanged here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #fff;
        }

        .container {
            width: 90%;
            max-width: 400px;
            padding: 30px;
            background-color: #fff;

            border-radius: 50px;
            text-align: center;
            position: relative;
        }

        .logo img {
            max-width: 80px;

        }

        h1 {
            font-size: 1.8em;
            margin-bottom: -10px;
            color: black;
        }

        p.subtitle {
            font-size: 0.9em;
            margin-bottom: 30px;
            color: #737373;
        }

        .btn-google {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #f4f4f4;
            color: #555;
            border: 1px solid #ddd;
            border-radius: 50px;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            position: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .btn-google img {
            height: 20px;
            margin-right: 10px;
        }

        .btn-google:hover {
            background-color: #f7f7f7;
        }

        input[type="text"],
        input[type="password"] {
            width: 95%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #f4f4f4;

        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #444;
        }

        .terms {
            font-size: 0.8em;
            color: #737373;
            margin-top: 20px;
        }

        .terms a {
            color: #333;
            text-decoration: underline;
        }

        .terms a:hover {
            text-decoration: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="../images/official_logo.jpg" alt="Logo">
        </div>
        <h1 style="padding-bottom: -100px;">Welcome to Dash</h1>
        <p class="subtitle">Your only option to a dashing car wash</p>
        <button class="btn-google" onclick="location.href='social_login.php?provider=Google'">
            <img src="../images/icons/google.png" alt="Google Logo">
            Continue with Google
        </button>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" class="btn" value="Continue with email">
        </form>
        <p class="terms">
            By clicking "Continue with Google" or "Continue with email" you agree to our
            <a href="terms.php">Terms of Use</a> and <a href="privacy.php">Privacy Policy</a>.
        </p>
    </div>
</body>
</html>
