<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Your code to send emails will go here
    // You'll need to implement the email sending functionality
    // using PHP's mail() function or a library like PHPMailer
    // Ensure proper validation and sanitization of user input
    // Here's a placeholder message to indicate that the email has been sent
    $email_sent_message = "Email has been sent to all users.";
}
?>
<style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        margin: 0;
    }

    .main-content {
        flex: 1;
        padding-bottom: 60px; /* Height of the footer */
    }

    .email-form {
        max-width: 600px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .email-form label,
    .email-form textarea {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }

    .email-form textarea {
        height: 200px;
    }

    .email-form input[type="submit"] {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .email-form input[type="submit"]:hover {
        background-color: #0056b3;
    }
</style>

<div class="main-content">
    <div class="email-form">
        <h2>Draft Email Template</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>

            <!-- Optionally, you can add support for pasting images -->
            <!-- <label for="image">Image:</label> -->
            <!-- <input type="file" id="image" name="image" accept="image/*"> -->

            <input type="submit" value="Send Email">
        </form>
        <?php
        // Display a message if the email has been sent successfully
        if (isset($email_sent_message)) {
            echo '<p>' . $email_sent_message . '</p>';
        }
        ?>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
