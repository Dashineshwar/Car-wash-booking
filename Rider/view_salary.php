<?php
include 'includes/rider_session.php';
include 'includes/connection.php';
include 'includes/topbar.php';

// Get the current month start and end dates
$currentMonthStart = date('Y-m-01');
$currentMonthEnd = date('Y-m-t');

// Fetch default earnings for the current month
$rider_id = $_SESSION['riderId'];
$query = "SELECT transaction_id, booking_id, amount, transaction_time, service_type, booking_type 
          FROM transactions 
          WHERE rider_id = ? AND transaction_time BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $rider_id, $currentMonthStart, $currentMonthEnd);
$stmt->execute();
$result = $stmt->get_result();
$earnings = [];
$totalEarnings = 0;
while ($row = $result->fetch_assoc()) {
    $earnings[] = $row;
    $totalEarnings += $row['amount'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earnings Summary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">Earnings Summary</h2>
    <form id="filter-form" class="form-inline justify-content-center mb-4">
        <label for="from-date" class="mr-2">From:</label>
        <input type="date" id="from-date" name="from_date" class="form-control mr-3" value="<?= $currentMonthStart ?>">
        <label for="to-date" class="mr-2">To:</label>
        <input type="date" id="to-date" name="to_date" class="form-control mr-3" value="<?= $currentMonthEnd ?>">
        <button type="button" id="filter-button" class="btn btn-primary mr-3">Filter</button>
    </form>
    <button id="export-pdf" class="btn btn-success mt-3" onclick="exportToPDF()">Export to PDF</button>
    <div id="earnings-summary">
        <p>Showing earnings from <b><?= $currentMonthStart ?></b> to <b><?= $currentMonthEnd ?></b>.</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Booking ID</th>
                    <th>Service Type</th>
                    <th>Booking Type</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($earnings as $earning): ?>
                    <tr>
                        <td><?= $earning['transaction_id'] ?></td>
                        <td><?= $earning['booking_id'] ?></td>
                        <td><?= $earning['service_type'] ?></td>
                        <td><?= $earning['booking_type'] ?></td>
                        <td><?= number_format($earning['amount'], 2) ?></td>
                        <td><?= $earning['transaction_time'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h4 class="text-right">Total Earnings: RM <?= number_format($totalEarnings, 2) ?></h4>
    </div>
</div>

<script>
    function exportToPDF() {
    const riderId = '<?= $_SESSION['riderId'] ?>';
    const dateFrom = document.getElementById('from-date').value;
    const dateTo = document.getElementById('to-date').value;
    console.log('Export to PDF button clicked');


    const url = `php/earnings/export_rider_earnings.php?riderId=${riderId}&date_from=${dateFrom}&date_to=${dateTo}`;
    window.open(url, '_blank'); // Opens the URL in a new tab to trigger the PHP script

    }

$(document).ready(function () {
    // Handle Filter Button
    $('#filter-button').click(function () {
        const fromDate = $('#from-date').val();
        const toDate = $('#to-date').val();
        $.ajax({
            url: 'php/earnings/fetch_rider_earnings.php',
            type: 'GET',
            data: { date_from: fromDate, date_to: toDate },
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    alert(response.message);
                    return;
                }

                const earnings = response.bookings;
                const totalEarnings = response.total;

                // Update the earnings summary text
                const dateFrom = response.date_from.split(' ')[0];
                const dateTo = response.date_to.split(' ')[0];
                $('#earnings-summary p').html(
                    `Showing earnings from <b>${dateFrom}</b> to <b>${dateTo}</b>.`
                );

                // Update the earnings table
                let tableRows = '';
                earnings.forEach(earning => {
                    tableRows += `
                        <tr>
                            <td>${earning.transaction_id}</td>
                            <td>${earning.booking_id}</td>
                            <td>${earning.service_type}</td>
                            <td>${earning.booking_type}</td>
                            <td>${parseFloat(earning.amount).toFixed(2)}</td>
                            <td>${earning.transaction_time}</td>
                        </tr>
                    `;
                });
                $('table tbody').html(tableRows);

                // Update the total earnings
                $('h4.text-right').text(`Total Earnings: RM ${parseFloat(totalEarnings).toFixed(2)}`);
            },
            error: function () {
                alert('Failed to fetch earnings.');
            }
        });
    });

});
</script>
</body>
</html>
