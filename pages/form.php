<?php
// Start the session
session_start();

// Include the database connection
include '../includes/connection.php';

// Define the country codes array in PHP
$countryCodes = [
    "United States" => "+1",
    "Canada" => "+1",
    "United Kingdom" => "+44",
    "Australia" => "+61",
    "India" => "+91",
    "Malaysia" => "+60",
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $country = htmlspecialchars($_POST['country']);
    $area_code = htmlspecialchars($_POST['area_code']);
    $whatsapp = htmlspecialchars($_POST['whatsapp']);

    // Validate and sanitize input
    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        $_SESSION['error'] = "Invalid name format";
    } elseif (!array_key_exists($country, $countryCodes)) {
        $_SESSION['error'] = "Invalid country selection";
    } elseif (!preg_match("/^\+\d{1,4}$/", $area_code)) {
        $_SESSION['error'] = "Invalid area code format";
    } elseif (!preg_match("/^\d+$/", $whatsapp)) {
        $_SESSION['error'] = "Invalid WhatsApp number format";
    } else {
        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO userinput (name, country, whatsapp, status) VALUES (?, ?, ?, ?)");
        $full_whatsapp_number = "($area_code)$whatsapp";
        $status = "new";
        $stmt->bind_param("ssss", $name, $country, $full_whatsapp_number, $status);

        if ($stmt->execute()) {
            // Redirect to success_submission.php and pass the name as a parameter
            header("Location: success_submission.php?name=" . urlencode($name));
            exit();
        } else {
            echo "<div class='message'>Error: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
    }

    // Redirect back to form if there's an error
    header("Location: form.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collect User Data</title>
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
            max-width: 500px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }
        .container img {
            max-width: 100px;
            margin-bottom: 20px;
        }
        .container h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .container form {
            display: flex;
            flex-direction: column;
        }
        .container label {
            text-align: left;
            margin-bottom: 5px;
        }
        .container input,
        .container select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }
        .container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 20px;
            color: red;
        }
        .flex-container {
            display: flex;
            justify-content: space-between;
        }
        .flex-container input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        #area_code {
            flex: 1;
            margin-right: 10px;
            max-width: 30%;
        }
        #whatsapp {
            flex: 2;
            max-width: 70%;
        }
        .flex-container input:last-child {
            margin-right: 0;
        }

        /* Media Queries for Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 80%;
            }
            .flex-container {
                flex-direction: column;
            }
            .flex-container input {
                margin-right: 0;
                margin-bottom: 10px;
                width: 100%;
            }
            .flex-container input:last-child {
                margin-bottom: 0;
            }
        }

        @media (max-width: 480px) {
            .container h1 {
                font-size: 1.2em;
            }
            .container img {
                max-width: 80px;
            }
        }
    </style>
    <script>
        const countryCodes = {
            "United States": "+1",
            "Canada": "+1",
            "United Kingdom": "+44",
            "Australia": "+61",
            "India": "+91",
            "Malaysia": "+60",
        };

        function updateAreaCode() {
            const countrySelect = document.getElementById("country");
            const areaCodeInput = document.getElementById("area_code");
            const selectedCountry = countrySelect.value;

            areaCodeInput.value = countryCodes[selectedCountry] || "";
        }
    </script>
</head>
<body>
    <div class="container">
        <img src="../images/logo.jpg" alt="Logo">
        <h1>Please give us your information and one of our top leaders will reach you out.<br> Together we rise!</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='message'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']); // Clear the error message
        }
        ?>
        <form action="form.php" method="POST">
            <label for="name" >Name:</label>
            <input type="text" id="name" name="name" placeholder="Please input your name" required>

            <label for="country">Country:</label>
            <select id="country" name="country" onchange="updateAreaCode()" required>
                <option value="">Select a country</option>
                <?php
                foreach ($countryCodes as $country => $code) {
                    echo "<option value=\"$country\">$country</option>";
                }
                ?>
            </select>
            <br>
            <label for="whatsapp">WhatsApp Number:</label>
            <label style="font-size:10px; color:red;">**Please input your area code.</label>
            <div class="flex-container">
                <input type="text" id="area_code" name="area_code" placeholder="Area Code" required>
                <input type="text" id="whatsapp" name="whatsapp" placeholder="Please input your whatsapp number" required>
            </div>
            <br>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
