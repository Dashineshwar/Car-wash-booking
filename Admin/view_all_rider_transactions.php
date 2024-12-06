<?php
include 'includes/connection.php';
include 'includes/topbar.php';

// Fetch riders for dropdown
$ridersQuery = "SELECT rider_id, username FROM rider";
$ridersResult = mysqli_query($conn, $ridersQuery);
$riders = [];
while ($row = mysqli_fetch_assoc($ridersResult)) {
    $riders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Rider Transactions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">All Rider Transactions</h2>

    <!-- Filter Inputs -->
    <div class="form-inline justify-content-center mb-4">
        <select id="rider-id-select" class="form-control mr-3">
            <option value="">All Riders</option>
            <?php foreach ($riders as $rider): ?>
                <option value="<?= $rider['rider_id'] ?>"><?= $rider['username'] ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" id="transaction-id-input" class="form-control mr-3" placeholder="Transaction ID">
        <input type="text" id="booking-id-input" class="form-control mr-3" placeholder="Booking ID">
        <input type="date" id="from-date-input" class="form-control mr-3">
        <input type="date" id="to-date-input" class="form-control mr-3">
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
                    <th>Rider</th>
                    <th>Booking ID</th>
                    <th>Service Type</th>
                    <th>Booking Type</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically loaded via AJAX -->
            </tbody>
        </table>
        <h4 class="text-right">Total Earnings: RM <span id="total-earnings">0.00</span></h4>
    </div>
</div>

<script>
$(document).ready(function () {
    // Function to fetch transactions
    function fetchTransactions(riderId = '', transactionId = '', bookingId = '', fromDate = '', toDate = '') {
        $.ajax({
            url: 'php/transactions/fetch_all_rider_transactions.php',
            type: 'GET',
            data: {
                rider_id: riderId,
                transaction_id: transactionId,
                booking_id: bookingId,
                from_date: fromDate,
                to_date: toDate
            },
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
                            <td>${transaction.username}</td>
                            <td>${transaction.booking_id}</td>
                            <td>${transaction.service_type}</td>
                            <td>${transaction.booking_type}</td>
                            <td>${parseFloat(transaction.amount).toFixed(2)}</td>
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
    fetchTransactions('', '', '', today, today);

    // Search button click event
    $('#search-button').click(function () {
        const riderId = $('#rider-id-select').val();
        const transactionId = $('#transaction-id-input').val();
        const bookingId = $('#booking-id-input').val();
        const fromDate = $('#from-date-input').val();
        const toDate = $('#to-date-input').val();
        fetchTransactions(riderId, transactionId, bookingId, fromDate, toDate);
    });

    // Reset button click event
    $('#reset-button').click(function () {
        $('#rider-id-select').val('');
        $('#transaction-id-input').val('');
        $('#booking-id-input').val('');
        const today = new Date().toISOString().split('T')[0];
        $('#from-date-input').val(today);
        $('#to-date-input').val(today);
        fetchTransactions('', '', '', today, today);
    });

    // Export to PDF
    $('#export-pdf').click(function () {
        const riderId = $('#rider-id-select').val();
        const transactionId = $('#transaction-id-input').val();
        const bookingId = $('#booking-id-input').val();
        const fromDate = $('#from-date-input').val();
        const toDate = $('#to-date-input').val();
        const url = `php/transactions/export_all_rider_transactions.php?rider_id=${riderId}&transaction_id=${transactionId}&booking_id=${bookingId}&from_date=${fromDate}&to_date=${toDate}`;
        window.open(url, '_blank');
    });
});

</script>
</body>
</html>
