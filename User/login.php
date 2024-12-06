<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f7f7f7;
        }

        .container {
            width: 90%;
            max-width: 400px;
            padding: 40px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
            position: relative;
        }

        .container img {
            max-width: 80px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .reset-password {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .reset-password:hover {
            color: #333;
        }

        .error-message {
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            background: linear-gradient(45deg, #ff0047, #6a00ff);
            color: white;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .error-message.show {
            opacity: 1;
            transform: translate(-50%, 0);
        }

        /* Media Queries for Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            h2 {
                font-size: 1.5em;
            }
            input[type="text"],
            input[type="password"] {
                font-size: 14px;
            }
            .btn {
                font-size: 14px;
            }
            .reset-password {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .container {
                max-width: 90%;
            }
            .container img {
                max-width: 60px;
            }
            h2 {
                font-size: 1.3em;
            }
            input[type="text"],
            input[type="password"] {
                font-size: 12px;
            }
            .btn {
                font-size: 12px;
            }
            .reset-password {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="../images/official_logo.jpg" alt="Logo">
        <h2>User Login</h2>
        <?php
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
                echo "<div id='error-message-username' class='error-message show'>$username_err</div>";
            } else {
                $username = sanitize_input($_POST["username"]);
            }

            // Validate password
            if (empty($_POST["password"])) {
                $password_err = "Please enter your password.";
                echo "<div id='error-message-password' class='error-message show'>$password_err</div>";
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
                                    session_start();

                                    // Store data in session variables
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $id;
                                    $_SESSION["username"] = $username;
                                    $_SESSION["email"] = $email;
                                    $_SESSION["phone"] = $phone_no;

                                    // Redirect user to welcome page
                                    header("location: welcome.php");
                                } else {
                                    // Display an error message if password is not valid
                                    $password_err = "The password you entered was not valid.";
                                    echo "<div id='error-message-password' class='error-message show'>$password_err</div>";
                                }
                            }
                        } else {
                            // Display an error message if username doesn't exist
                            $username_err = "No account found with that username.";
                            echo "<div id='error-message-username' class='error-message show'>$username_err</div>";
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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="<?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>">
            </div>
            <div class="<?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="password" placeholder="Password">
            </div>
            <input type="submit" class="btn" value="Login">
        </form>
        <br>
        <a href="reset_password.php" class="reset-password">Forgot password?</a>

    </div>
    <script>
        // Script to remove the error message after a certain time
        setTimeout(function() {
            var errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(function(errorMessage) {
                errorMessage.classList.remove('show');
            });
        }, 5000);
    </script>
</body>
</html>
