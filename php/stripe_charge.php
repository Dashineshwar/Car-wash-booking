<?php
include '../includes/session.php';
require '../vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51PoGYVP0jYcAiYG4mQDz0U0hTRyrSpJk6lwlr9gEKSoAWV9fSBCpJSSKcdryu6G1XgKjwa99GfzfMWWs5YsdH6cu005nZLTegM');

// Get the form data
$plate = $_POST['plate'];
$service = $_POST['service'];
$type = $_POST['type'];
$price = $_POST['price'];
$description = $_POST['description'];
$date = $_POST['date'];
$time = $_POST['time'];

// Separate address components from form
$address_line_1 = $_POST['address_line_1'];
$address_line_2 = $_POST['address_line_2'];
$postcode = $_POST['postcode'];
$city = $_POST['city'];
$state = $_POST['state'];
$country = $_POST['country'];

// Combine the address components
$full_address = trim($address_line_1 . ', ' . $address_line_2 . ', ' . $postcode . ', ' . $city . ', ' . $state . ', ' . $country, ', ');

// Convert price to cents (Stripe expects amounts in cents)
$priceCents = $price * 100;

try {
    // Update description to include full address
    $description = "$type - $service for $plate on $date at $time at $full_address";

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'myr',
                'product_data' => [
                    'name' => "$service - $type",
                    'description' => $description,
                ],
                'unit_amount' => $priceCents,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost/alphasphinx/php/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/alphasphinx/User/welcome.php',
        'metadata' => [
            'plate' => $plate,
            'service' => $service,
            'type' => $type,
            'description' => $description,
            'date' => $date,
            'time' => $time,
            // Store the separate address fields in metadata
            'address_line_1' => $address_line_1,
            'address_line_2' => $address_line_2,
            'postcode' => $postcode,
            'city' => $city,
            'state' => $state,
            'country' => $country
        ]
    ]);

    // Redirect to the Stripe checkout page
    header("Location: " . $session->url);
    exit();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
