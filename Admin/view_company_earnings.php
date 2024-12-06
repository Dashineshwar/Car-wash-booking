<?php
include 'includes/admin_session.php';
include 'includes/connection.php';
include 'includes/topbar.php';

// Get the current month start and end dates
$currentMonthStart = date('Y-m-01');
$currentMonthEnd = date('Y-m-t');

// Fetch default company earnings for the current month
$query = "SELECT transaction_id, booking_id, amount, agent_amount, company_amount, transaction_time, service_type, booking_type 
          FROM company_transactions 
          WHERE transaction_time BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $currentMonthStart, $currentMonthEnd);
$stmt->execute();
$result = $stmt->get_result();

$earnings = [];
$totalEarnings = 0;
while ($row = $result->fetch_assoc()) {
    $earnings[] = $row;
    $totalEarnings += $row['company_amount'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Earnings Summary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">Company Earnings Summary</h2>
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
                    <th>Agent Amount (RM)</th>
                    <th>Company Amount (RM)</th>
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
                        <td><?= number_format($earning['agent_amount'], 2) ?></td>
                        <td><?= number_format($earning['company_amount'], 2) ?></td>
                        <td><?= $earning['transaction_time'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h4 class="text-right">Total Company Earnings: RM <?= number_format($totalEarnings, 2) ?></h4>
    </div>
</div>

<script>
    function exportToPDF() {
        const dateFrom = document.getElementById('from-date').value;
        const dateTo = document.getElementById('to-date').value;

        const url = `php/earnings/export_company_earnings.php?date_from=${dateFrom}&date_to=${dateTo}`;
        window.open(url, '_blank'); // Opens the URL in a new tab to trigger the PHP script
    }

    $(document).ready(function () {
        $('#filter-button').click(function () {
            const fromDate = $('#from-date').val();
            const toDate = $('#to-date').val();
            $.ajax({
                url: 'php/earnings/fetch_company_earnings.php',
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
                    $('#earnings-summary p').html(
                        `Showing earnings from <b>${response.date_from.split(' ')[0]}</b> to <b>${response.date_to.split(' ')[0]}</b>.`
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
                                <td>${parseFloat(earning.agent_amount).toFixed(2)}</td>
                                <td>${parseFloat(earning.company_amount).toFixed(2)}</td>
                                <td>${earning.transaction_time}</td>
                            </tr>
                        `;
                    });
                    $('table tbody').html(tableRows);

                    // Update the total earnings
                    $('h4.text-right').text(`Total Company Earnings: RM ${parseFloat(totalEarnings).toFixed(2)}`);
                },
                error: function () {
                    alert('Failed to fetch company earnings.');
                }
            });
        });
    });
</script>
</body>
</html>
