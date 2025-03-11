<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

// Fetch services from the database
$services_query = "SELECT id, name, normal_price, express_price, duration FROM services";
$services_result = mysqli_query($conn, $services_query);

// Create an array to store services
$services = [];
if ($services_result && mysqli_num_rows($services_result) > 0) {
    while ($row = mysqli_fetch_assoc($services_result)) {
        $services[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Choose Vehicle</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- For icons -->
<style>
html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

body {
    min-height: 100vh; /* Ensure the body takes the full height of the viewport */
}

#content {
    flex: 1 0 auto; /* Allow content to grow and fill available space */
}

.container {
    max-width: 800px;
    margin: auto;
    padding: 20px;
}

.vehicle-btn {
    background-color: #000000;
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 5px;
    border-radius: 20px;
    cursor: pointer;
}

.car-info, #receipt {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9;
}

.booking-btn {
    background-color: #28a745;
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 10px;
    border-radius: 20px;
    cursor: pointer;
}


.continue-btn {
    background-color: #000000;
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 10px;
    border-radius: 20px;
    cursor: pointer;
}

#express-booking {
    background-color: #000000;
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 5px;
    border-radius: 20px;
    cursor: pointer;
}
#normal-booking {
    background-color: #000000;
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 5px;
    border-radius: 20px;
    cursor: pointer;
}
#receipt {
    display: none;
    background-color: #ffffff;
    border: 1px solid #ccc;
    border-radius: 12px;
    padding: 30px;
    margin-top: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.receipt-title {
    font-weight: bold;
    font-size: 24px;
    margin-bottom: 25px;
    color: #333;
}

.receipt-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* Two columns */
    gap: 20px; /* Space between grid items */
    text-align: center;
}

.receipt-grid div {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.receipt-grid i {
    font-size: 30px;
    color: #000;
    margin-bottom: 10px;
}

.receipt-grid p {
    margin: 0;
    font-size: 16px;
    color: #555;
}

.receipt-grid p strong {
    font-size: 16px;
    color: #333;
}

.continue-btn {
    background-color: #000000;
    color: #ffffff;
    border: none;
    padding: 12px 25px;
    margin-top: 25px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
}

.continue-btn:hover {
    background-color: #444444;
}

</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div id="content">
    <div class="container">
        <h1 class="text-center" style="font-size: 30px;">Welcome to Alphasphinx Car Wash</h1> <br>

        <!-- Vehicle Type Buttons -->
        <div class="text-center">
            <button class="vehicle-btn" data-type="All My Vehicle">All My Vehicle</button>
            <button class="vehicle-btn" data-type="Sedan">Sedan</button>
            <button class="vehicle-btn" data-type="SUV">SUV</button>
            <button class="vehicle-btn" data-type="MPV">MPV</button>
            <button class="vehicle-btn" data-type="Van">Van</button>
            <button class="vehicle-btn" data-type="Lorry">Lorry</button>
        </div>

        <!-- Car Info -->
        <div id="car-info" class="car-info text-center" style="display: none;"></div>

        <!-- Receipt Section -->
        <div id="receipt" class="text-center">
            <h3 class="receipt-title">Receipt</h3>
            <div class="receipt-grid">
                <div>
                    <i class="fas fa-car"></i><strong>Car Number Plate:</strong> 
                    <p><span id="receipt-plate"></span></p>
                </div>
                <div>
                    <i class="fas fa-file-alt"></i><strong>Type of Service:</strong>
                    <p><span id="receipt-type"></span></p>
                </div>
                <div>
                    <i class="fas fa-hand-sparkles"></i><strong>Service chosen:</strong><p>
                    <span id="receipt-service"></span></p>
                </div>
                <div>
                    <i class="fas fa-money-bill-wave"></i><strong>Price:</strong><p> 
                        RM<span id="receipt-price"></span></p>
                </div>
            </div>
            <button id="continue-btn" class="continue-btn">Continue</button>
        </div>


    </div>
</div>

<script>
$(document).ready(function () {
    const services = <?php echo json_encode($services); ?>;

    $('.vehicle-btn').click(function () {
        const vehicleType = $(this).data('type');

        // Reset all dynamic content when a new vehicle type is selected
        $('#car-info').empty().hide(); // Clear car info section
        $('#receipt').hide(); // Hide the receipt

        $.ajax({
            url: '../php/fetch_vehicle.php',
            type: 'POST',
            data: { vehicle_type: vehicleType },
            success: function (response) {
                $('#car-info').html(response).fadeIn();

                $('#vehicle-select').change(function () {
                    const selectedVehicle = $('#vehicle-select option:selected').val();

                    // Clear any existing service dropdown and booking buttons
                    $('#car-info').find('#service-select').parent().remove(); // Remove previous "Select a Service" container
                    $('#car-info').find('.text-center.mt-3').remove(); // Remove previous booking buttons
                    $('#receipt').hide(); // Hide the receipt

                    if (selectedVehicle) {
                        // Append a new service selection dropdown
                        let serviceOptions = `
                            <div id="service-container">
                                <h3>Select a Service</h3>
                                <select id="service-select" class="form-control">
                                    <option value="">Choose your shine!</option>
                        `;

                        services.forEach(service => {
                            serviceOptions += `<option value="${service.name}">${service.name}</option>`;
                        });

                        serviceOptions += `
                                </select>
                            </div>
                        `;
                        $('#car-info').append(serviceOptions);

                        $('#service-select').change(function () {
                            const selectedService = $(this).val();
                            const serviceDetails = services.find(service => service.name === selectedService);

                            // Clear previous booking buttons and receipt
                            $('#car-info').find('.text-center.mt-3').remove();
                            $('#receipt').hide();

                            if (serviceDetails) {
                                // Append booking buttons
                                $('#car-info').append(`
                                    <div class="text-center mt-3">
                                        <button id="express-booking" class="booking-btn">Express Booking</button>
                                        <button id="normal-booking" class="booking-btn">Normal Booking</button>
                                    </div>
                                `);

                                // Handle booking button clicks
                                $('.booking-btn').off('click').on('click', function () {
                                    const typeOfService = $(this).attr('id') === 'express-booking' ? 'Express' : 'Normal';
                                    const price = typeOfService === 'Express' ? serviceDetails.express_price : serviceDetails.normal_price;

                                    // Populate and display the receipt
                                    $('#receipt-plate').text(selectedVehicle);
                                    $('#receipt-service').text(selectedService);
                                    $('#receipt-type').text(typeOfService);
                                    $('#receipt-price').text(price);

                                    $('#receipt').fadeIn();

                                    // Handle the continue button click
                                    $('#continue-btn').off('click').on('click', function () {
                                        window.location.href = `booking.php?plate=${encodeURIComponent(selectedVehicle)}&service=${encodeURIComponent(selectedService)}&type=${encodeURIComponent(typeOfService)}&price=${encodeURIComponent(price)}&duration=${encodeURIComponent(serviceDetails.duration)}`;
                                    });
                                });
                            }
                        });
                    }
                });
            },
            error: function () {
                $('#car-info').html('<p>Error fetching vehicle data.</p>').fadeIn();
            }
        });
    });
});


</script>


</body>
</html>
