<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        input[type="password"],
        input[type="email"],
        input[type="tel"] {
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

        .login-btn {
            background-color: #007bff;
            margin-top: 10px;
        }

        .login-btn:hover {
            background-color: #0056b3;
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
            input {
                font-size: 14px;
            }
            .btn {
                font-size: 14px;
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
            input {
                font-size: 12px;
            }
            .btn {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="images/official_logo.jpg" alt="Logo">
        <h2>User Registration</h2>
        <?php
        // Include the database connection
        include 'includes/connection.php';

        // Define function to sanitize and validate user input
        function sanitize_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        // Initialize variables
        $username = $email = $password = $phone_number = "";
        $username_err = $email_err = $password_err = $phone_err = "";

        // Process form data when form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Validate username
            if (empty($_POST["username"])) {
                $username_err = "Please enter a username.";
                echo "<div id='error-message-username' class='error-message show'>$username_err</div>";
            } else {
                $username = sanitize_input($_POST["username"]);
            }

            // Validate email
            if (empty($_POST["email"])) {
                $email_err = "Please enter an email.";
                echo "<div id='error-message-email' class='error-message show'>$email_err</div>";
            } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $email_err = "Invalid email format.";
                echo "<div id='error-message-email' class='error-message show'>$email_err</div>";
            } else {
                $email = sanitize_input($_POST["email"]);
            }

            // Validate password
            if (empty($_POST["password"])) {
                $password_err = "Please enter a password.";
                echo "<div id='error-message-password' class='error-message show'>$password_err</div>";
            } else {
                $password = sanitize_input($_POST["password"]);
            }

            // Validate phone number
            if (empty($_POST["phone_number"])) {
                $phone_err = "Please enter a phone number.";
                echo "<div id='error-message-phone' class='error-message show'>$phone_err</div>";
            } else {
                $phone_number = sanitize_input($_POST["phone_number"]);
            }

            // Check input errors before inserting into database
            if (empty($username_err) && empty($email_err) && empty($password_err) && empty($phone_err)) {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare an insert statement
                $sql = "INSERT INTO users (username, email, password, phone_no, acc_status) VALUES (?, ?, ?, ?, ?)";

                if ($stmt = $conn->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters
                    $acc_status = 0;
                    $stmt->bind_param("sssss", $username, $email, $hashed_password, $phone_number, $acc_status);

                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        // Registration successful
                        echo "<div class='success-message'>Registration successful! You can now <a href='login.php'>login here</a>.</div>";
                    } else {
                        echo "Something went wrong. Please try again later.";
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
            <div class="<?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
            </div>
            <div class="<?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="password" placeholder="Password">
            </div>
            <div class="<?php echo (!empty($phone_err)) ? 'has-error' : ''; ?>">
                <input type="tel" name="phone_number" placeholder="Phone Number" value="<?php echo $phone_number; ?>">
            </div>
            <input type="submit" class="btn" value="Register">
        </form>
        <br>
        <!-- Login button -->
        <a href="User/login.php" class="btn login-btn">Login</a>
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
