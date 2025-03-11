<?php
echo "Current URL: " . $_SERVER['REQUEST_URI'];

require '../hybridauth-master/src/autoload.php';
require '../includes/connection.php'; // Include your database connection

$config = include 'hybridauth_config.php';

$provider = 'Google'; // e.g., Google

if ($provider) {
    try {
        $hybridauth = new Hybridauth\Hybridauth($config);
        $adapter = $hybridauth->authenticate($provider);
        $userProfile = $adapter->getUserProfile();

        // Extract Google user data
        $googleEmail = $userProfile->email;
        $googleName = $userProfile->displayName;
        $googlePicture = $userProfile->photoURL;

        // Check if user already exists
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $googleEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists, log them in
            $user = $result->fetch_assoc();

            session_start();
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $user['id'];
            $_SESSION["username"] = $user['username'];
            $_SESSION["email"] = $user['email'];
            $_SESSION["phone"] = $user['phone_no'];

            // Redirect to the main dashboard
            header("Location: ../User/info.php");
            exit;
        } else {
            // New user, redirect to the registration page
            session_start();
            $_SESSION["google_name"] = $googleName;
            $_SESSION["google_email"] = $googleEmail;
            $_SESSION["google_picture"] = $googlePicture;

            header("Location: complete_registration.php");
            exit;
        }

        // Disconnect after use
        $adapter->disconnect();
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo 'No provider specified!';
}
