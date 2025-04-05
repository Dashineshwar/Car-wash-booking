<?php
session_start();
include '../includes/connection.php';

// Billplz API credentials (Replace with your actual API Key and Collection ID)
$api_key = "21a4d082-e3f7-450d-af0c-f9c409918873"; // Get from Billplz Dashboard
$collection_id = "v1dg6obm"; // Get from Billplz Dashboard

// Retrieve booking details
$plate = $_POST['plate'] ?? 'Unknown';
$service = $_POST['service'] ?? 'Unknown';
$type = $_POST['type'] ?? 'Unknown';
$price = $_POST['price'] ?? 0;
$address_line_1 = $_POST['address_line_1'] ?? '';
$address_line_2 = $_POST['address_line_2'] ?? '';
$postcode = $_POST['postcode'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$country = $_POST['country'] ?? '';
$user_email = $_SESSION['email'] ?? 'noemail@example.com'; // Assuming email is stored in session

// Validate price before converting
$amount = is_numeric($price) ? $price * 100 : 0;

// Payment Callback URL (Where Billplz will redirect after payment)
$return_url = "success.php"; // Change this
$callback_url = "payment_callback.php"; // Optional

// Create a Billplz payment request
$billplz_url = "https://www.billplz.com/api/v3/bills"; // Use Sandbox if testing
$data = [
    "collection_id" => $collection_id,
    "email" => $user_email,
    "mobile" => "", // Optional: Add mobile number if available
    "name" => "Car Wash Payment",
    "amount" => $amount, // Convert to cents
    "callback_url" => $callback_url,
    "redirect_url" => $return_url,
    "description" => "Payment for Service: $service, Car Plate: $plate",
];

// Convert data to JSON format
$json_data = json_encode($data);

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $billplz_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . base64_encode("$api_key:"), // Fix encoding
    "Content-Type: application/json"
]);

$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL error: " . curl_error($ch);
    curl_close($ch);
    exit();
}

curl_close($ch);

// Decode JSON response
$response_data = json_decode($response, true);

if (isset($response_data['url'])) {
    // Redirect user to Billplz Payment Page
    header("Location: " . $response_data['url']);
    exit();
} else {
    echo "Error creating payment: " . json_encode($response_data);
}
?>
