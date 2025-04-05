<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

// Fetch services from the database
$services_query = "SELECT id, name, normal_price, express_price, duration FROM services";
$services_result = mysqli_query($conn, $services_query);

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
<title>Choose Vehicle - Modern UI</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f5f7fa;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        margin: auto;
        padding: 20px;
    }

    .title {
        text-align: center;
        margin-bottom: 30px;
    }

    .vehicle-btn {
        background: #1f2937;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 30px;
        margin: 5px;
        font-size: 14px;
        transition: 0.3s;
    }
    .vehicle-btn:hover {
        background: #374151;
    }

    .card-ui {
        background: #ffffff;
        border-radius: 20px;
        padding: 25px;
        margin-top: 25px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    .booking-btn, .continue-btn {
        background-color: #1f2937;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 30px;
        margin: 10px 5px;
        transition: background 0.3s ease;
    }
    .booking-btn:hover, .continue-btn:hover {
        background-color: #111827;
    }

    #receipt {
        display: none;
    }

    @media (max-width: 576px) {
        .vehicle-btn {
            width: 100%;
            margin: 5px 0;
        }
    }
</style>
</head>
<body>
<div class="container">
    <h2 class="title">Welcome to <strong>QuikWash</strong></h2>

    <div class="text-center">
        <button class="vehicle-btn" data-type="All My Vehicle">All My Vehicle</button>
        <button class="vehicle-btn" data-type="Sedan">Sedan</button>
        <button class="vehicle-btn" data-type="SUV">SUV</button>
        <button class="vehicle-btn" data-type="MPV">MPV</button>
        <button class="vehicle-btn" data-type="Van">Van</button>
        <button class="vehicle-btn" data-type="Lorry">Lorry</button>
    </div>

    <div id="car-info" class="card-ui mt-4 text-center" style="display: none;"></div>
    <div id="service-select-container" class="card-ui mt-4" style="display: none;"></div>
    <div id="receipt" class="card-ui mt-4">
        <h4 class="text-center">Booking Receipt</h4>
        <p><strong>Car Number Plate:</strong> <span id="receipt-plate"></span></p>
        <p><strong>Service Chosen:</strong> <span id="receipt-service"></span></p>
        <p><strong>Type of Service:</strong> <span id="receipt-type"></span></p>
        <p><strong>Price:</strong> RM<span id="receipt-price"></span></p>
        <div class="text-center">
            <button id="continue-btn" class="continue-btn">Continue</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    var services = <?php echo json_encode($services); ?>;

    $('.vehicle-btn').click(function(){
        var vehicleType = $(this).data('type');
        $('#car-info, #service-select-container').hide().empty();
        $('#receipt').hide();

        $.ajax({
            url: '../php/fetch_vehicle.php',
            type: 'POST',
            data: { vehicle_type: vehicleType },
            success: function(response){
                $('#car-info').html(response).fadeIn();
                bindVehicleSelect();
            },
            error: function(){
                $('#car-info').html('<p>Error retrieving vehicle data.</p>').fadeIn();
            }
        });
    });

    function bindVehicleSelect() {
        $('#vehicle-select').change(function(){
            var selectedVehicle = $(this).val();
            $('#service-select-container').hide().empty();
            $('#receipt').hide();

            if (selectedVehicle) {
                let serviceOptions = `
                    <h5>Select a Service</h5>
                    <select id="service-select" class="form-control mb-3">
                        <option value="">Select a service</option>`;

                services.forEach(service => {
                    serviceOptions += `<option value="${service.name}">${service.name}</option>`;
                });

                serviceOptions += `</select>`;
                $('#service-select-container').html(serviceOptions).fadeIn();

                $('#service-select').change(function(){
                    $('#service-select-container .text-center.mt-3').remove();
                    $('#receipt').hide();

                    const selectedService = $(this).val();
                    if (selectedService) {
                        const detail = services.find(s => s.name === selectedService);
                        const normal = detail.normal_price;
                        const express = detail.express_price;
                        const duration = detail.duration;
                        const plate = $('#vehicle-select option:selected').text();

                        const buttonHTML = `
                            <div class="text-center mt-3">
                                <button class="booking-btn" data-type="Express" data-price="${express}" data-service="${selectedService}" data-plate="${plate}" data-duration="${duration}">Express Booking</button>
                                <button class="booking-btn" data-type="Normal" data-price="${normal}" data-service="${selectedService}" data-plate="${plate}" data-duration="${duration}">Normal Booking</button>
                            </div>
                        `;
                        $('#service-select-container').append(buttonHTML);
                        bindBookingButtons();
                    }
                });
            }
        });
    }

    function bindBookingButtons() {
        $('.booking-btn').off('click').on('click', function(){
            const type = $(this).data('type');
            const price = $(this).data('price');
            const plate = $(this).data('plate');
            const service = $(this).data('service');
            const duration = $(this).data('duration');

            $('#receipt-plate').text(plate);
            $('#receipt-service').text(service);
            $('#receipt-type').text(type);
            $('#receipt-price').text(price);

            $('#receipt').fadeIn();

            $('#continue-btn').off('click').on('click', function(){
                window.location.href = `booking.php?plate=${encodeURIComponent(plate)}&service=${encodeURIComponent(service)}&type=${encodeURIComponent(type)}&price=${encodeURIComponent(price)}&duration=${encodeURIComponent(duration)}`;
            });
        });
    }
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).on('click', '[data-toggle="modal"]', function (e) {
        e.preventDefault();
    });
</script>
</body>
</html>
