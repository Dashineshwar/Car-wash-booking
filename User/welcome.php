<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

// Fetch services from the database
$services_query = "SELECT id, name, normal_price, express_price FROM services";
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
    display: flex;
    flex-direction: column;
    min-height: 100vh; /* Ensure the body takes the full height of the viewport */
}

#content {
    flex: 1 0 auto; /* Allow content to grow and fill available space */
}

.container {
    flex-grow: 1;
}

.vehicle-btn {
    background-color:rgb(44, 62, 80);
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 10px;
    border-radius: 5px;
    cursor: pointer;
}
.vehicle-btn:hover {
    background-color:rgb(44, 62, 80);
}
.car-info {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9;
}
.booking-btn {
    background-color: rgb(44, 62, 80);
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 10px;
    border-radius: 5px;
    cursor: pointer;
}
.booking-btn:hover {
    background-color: rgb(44, 62, 80);
}
#receipt {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9;
    display: none;
}
.continue-btn {
    background-color: rgb(44, 62, 80);
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 10px;
    border-radius: 5px;
    cursor: pointer;
}
.continue-btn:hover {
    background-color: rgbrgb(44, 62, 80);
}

/* Sidebar toggle button */
.toggle-btn {
    position: absolute;
    top: 15px;
    left: 15px;
    background-color: rgb(44, 62, 80);
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    z-index: 1100;
}

#content {
    margin-left: 0px; /* Adjusted for the collapsed state */
    transition: margin-left 0.3s ease;
}

#content.expanded {
    margin-left: 250px; /* Adjusted for the expanded state */
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div id="content">
    <div class="container">
        <br>
        <h1 class="text-center mb-4">Welcome to Dashing</h1>

        <div class="text-center">
            <button class="vehicle-btn" data-type="All My Vehicle">All My Vehicle</button> <!-- New Button -->
            <button class="vehicle-btn" data-type="Sedan">Sedan</button>
            <button class="vehicle-btn" data-type="SUV">SUV</button>
            <button class="vehicle-btn" data-type="MPV">MPV</button>
            <button class="vehicle-btn" data-type="Van">Van</button>
            <button class="vehicle-btn" data-type="Lorry">Lorry</button>
        </div>

        <div id="car-info" class="car-info text-center" style="display: none;">
            <!-- Car information will be displayed here -->
        </div>

        <div id="receipt">
            <h3>Booking Receipt</h3>
            <p><strong>Car Number Plate:</strong> <span id="receipt-plate"></span></p>
            <p><strong>Service Chosen:</strong> <span id="receipt-service"></span></p>
            <p><strong>Type of Service:</strong> <span id="receipt-type"></span></p>
            <p><strong>Price:</strong> RM<span id="receipt-price"></span></p>
            <button id="continue-btn" class="continue-btn">Continue</button>
        </div>

    </div>
</div>

<script>
$(document).ready(function(){
    var services = <?php echo json_encode($services); ?>;

    $('.vehicle-btn').click(function(){
        var vehicleType = $(this).attr('data-type');

        // Reset everything when a new vehicle type is selected
        $('#car-info').empty().hide();
        $('#service-select-container').empty().hide();
        $('#receipt').hide();

        $.ajax({
            url: '../php/fetch_vehicle.php',
            type: 'POST',
            data: {
                vehicle_type: vehicleType
            },
            success: function(response){
                $('#car-info').html(response).fadeIn();

                $('#vehicle-select').change(function(){
                    var selectedVehicle = $(this).val();

                    // Reset service selection and receipt when a new vehicle is selected
                    $('#service-select-container').empty().hide();
                    $('#receipt').hide();

                    if (selectedVehicle) {
                        var serviceOptions = '<h3>Select a Service</h3>';
                        serviceOptions += '<select id="service-select" class="form-control"><option value="">Select a service</option>';

                        // Dynamically populate the service options from the PHP array
                        services.forEach(function(service) {
                            serviceOptions += '<option value="' + service.name + '">' + service.name + '</option>';
                        });

                        serviceOptions += '</select>';

                        $('#service-select-container').html(serviceOptions).fadeIn();

                        $('#service-select').change(function(){
                            var selectedService = $(this).val();

                            // Reset buttons and receipt when a new service is selected
                            $('#service-select-container .text-center').remove();
                            $('#receipt').hide();

                            if (selectedService) {
                                var serviceDetails = services.find(service => service.name === selectedService);
                                var normalPrice = serviceDetails.normal_price;
                                var expressPrice = serviceDetails.express_price;

                                $('#service-select-container').append(`
                                    <div class="text-center mt-3">
                                        <button id="express-booking" class="booking-btn">Express Booking</button>
                                        <button id="normal-booking" class="booking-btn">Normal Booking</button>
                                    </div>
                                `);

                                $('#express-booking, #normal-booking').click(function(){
                                    var typeOfService = $(this).attr('id').includes('express') ? 'Express' : 'Normal';
                                    var price = (typeOfService === 'Express') ? expressPrice : normalPrice;
                                    var plateNumber = $('#vehicle-select option:selected').text();
                                    var duration = serviceDetails.duration;

                                    $('#receipt-plate').text(plateNumber);
                                    $('#receipt-service').text(selectedService.charAt(0).toUpperCase() + selectedService.slice(1));
                                    $('#receipt-type').text(typeOfService);
                                    $('#receipt-price').text(price);

                                    $('#receipt').fadeIn();

                                    $('#continue-btn').click(function(){
                                        // Redirect to booking.php with query parameters, including the duration
                                        window.location.href = 'booking.php?plate=' + encodeURIComponent(plateNumber) +
                                                            '&service=' + encodeURIComponent(selectedService) +
                                                            '&type=' + encodeURIComponent(typeOfService) +
                                                            '&price=' + encodeURIComponent(price) +
                                                            '&duration=' + encodeURIComponent(duration);
                                    });
                                });

                            }
                        });
                    } else {
                        $('#service-select-container').fadeOut();
                    }
                });
            },
            error: function(){
                $('#car-info').html('<p>Error retrieving vehicle data.</p>').fadeIn();
            }
        });
    });
});
</script>

<?php
include '../includes/footer.php';
?>

</body>
</html>
