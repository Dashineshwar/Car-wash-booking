<?php
include 'includes/connection.php';
include 'includes/topbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Transactions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">Company Transactions</h2>

    <!-- Filter Inputs -->
    <div class="form-inline justify-content-center mb-4">
        <input type="text" id="transaction-id-input" class="form-control mr-3" placeholder="Transaction ID">
        <input type="text" id="booking-id-input" class="form-control mr-3" placeholder="Booking ID">
        <input type="text" id="rider-id-input" class="form-control mr-3" placeholder="Rider ID">
        <input type="text" id="user-id-input" class="form-control mr-3" placeholder="User ID">
        <select id="service-type-input" class="form-control mr-3">
            <option value="">All Services</option>
            <option value="Basic Car Wash">Basic Car Wash</option>
            <option value="Premium Car Wash">Premium Car Wash</option>
            <!-- Add more service types as needed -->
        </select>
        <select id="booking-type-input" class="form-control mr-3">
            <option value="">All Booking Types</option>
            <option value="Normal">Normal</option>
            <option value="Express">Express</option>
        </select>
        <input type="date" id="from-date-input" class="form-control mr-3">
        <input type="date" id="to-date-input" class="form-control mr-3">
        <select id="payment-status-input" class="form-control mr-3">
            <option value="">All Payment Status</option>
            <option value="Paid">Paid</option>
            <option value="Pending">Pending</option>
        </select>
        <button id="search-button" class="btn btn-primary">Search</button>
        <button id="reset-button" class="btn btn-secondary ml-3">Reset</button>
    </div>

    <!-- Export Button -->
    <button id="export-pdf" class="btn btn-success mb-4">Export to PDF</button>

    <!-- Transactions Table -->
    <div id="transactions-summary">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Booking ID</th>
                    <th>Rider ID</th>
                    <th>User ID</th>
                    <th>Service Type</th>
                    <th>Booking Type</th>
                    <th>Amount (RM)</th>
                    <th>Rider Previous Amount (RM)</th>
                    <th>Rider Current Amount (RM)</th>
                    <th>Rider share (RM)</th>
                    <th>Company share (RM)</th>
                    <th>Payment Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically loaded via AJAX -->
            </tbody>
        </table>
        <h4 class="text-right">Total Company Earnings: RM <span id="total-earnings">0.00</span></h4>
    </div>
</div>

<script>
$(document).ready(function () {
    // Function to fetch transactions
    function fetchTransactions(filters = {}) {
        $.ajax({
            url: 'php/transactions/fetch_company_transactions.php',
            type: 'GET',
            data: filters,
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    alert(response.message);
                    return;
                }

                const transactions = response.transactions;
                const totalEarnings = response.total;

                let tableRows = '';
                transactions.forEach(transaction => {
                    tableRows += `
                        <tr>
                            <td>${transaction.transaction_id}</td>
                            <td>${transaction.booking_id}</td>
                            <td>${transaction.rider_id}</td>
                            <td>${transaction.user_id}</td>
                            <td>${transaction.service_type}</td>
                            <td>${transaction.booking_type}</td>
                            <td>${parseFloat(transaction.amount).toFixed(2)}</td>
                            <td>${parseFloat(transaction.previous_amount).toFixed(2)}</td>
                            <td>${parseFloat(transaction.current_amount).toFixed(2)}</td>
                            <td>${parseFloat(transaction.agent_amount).toFixed(2)}</td>
                            <td>${parseFloat(transaction.company_amount).toFixed(2)}</td>
                            <td>${transaction.payment_status}</td>
                            <td>${transaction.transaction_time}</td>
                        </tr>
                    `;
                });
                $('table tbody').html(tableRows);
                $('#total-earnings').text(parseFloat(totalEarnings).toFixed(2));
            },
            error: function () {
                alert('Failed to fetch transactions.');
            }
        });
    }

    // Set default date inputs to today's date
    const today = new Date().toISOString().split('T')[0];
    $('#from-date-input').val(today);
    $('#to-date-input').val(today);

    // Fetch today's transactions on page load
    fetchTransactions({
        from_date: today,
        to_date: today
    });

    // Search button click event
    $('#search-button').click(function () {
        const filters = {
            transaction_id: $('#transaction-id-input').val(),
            booking_id: $('#booking-id-input').val(),
            rider_id: $('#rider-id-input').val(),
            user_id: $('#user-id-input').val(),
            service_type: $('#service-type-input').val(),
            booking_type: $('#booking-type-input').val(),
            from_date: $('#from-date-input').val(),
            to_date: $('#to-date-input').val(),
            payment_status: $('#payment-status-input').val()
        };
        fetchTransactions(filters);
    });

    // Reset button click event
    $('#reset-button').click(function () {
        $('#transaction-id-input').val('');
        $('#booking-id-input').val('');
        $('#rider-id-input').val('');
        $('#user-id-input').val('');
        $('#service-type-input').val('');
        $('#booking-type-input').val('');
        $('#payment-status-input').val('');
        $('#from-date-input').val(today);
        $('#to-date-input').val(today);
        fetchTransactions({
            from_date: today,
            to_date: today
        });
    });

    // Export to PDF
    $('#export-pdf').click(function () {
        const filters = {
            transaction_id: $('#transaction-id-input').val(),
            booking_id: $('#booking-id-input').val(),
            rider_id: $('#rider-id-input').val(),
            user_id: $('#user-id-input').val(),
            service_type: $('#service-type-input').val(),
            booking_type: $('#booking-type-input').val(),
            from_date: $('#from-date-input').val(),
            to_date: $('#to-date-input').val(),
            payment_status: $('#payment-status-input').val()
        };
        const queryParams = new URLSearchParams(filters).toString();
        const url = `php/transactions/export_company_transactions.php?${queryParams}`;
        window.open(url, '_blank');
    });
});
</script>
</body>
</html>
