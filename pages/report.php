<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

// Query to get the counts for each status
$new_count_query = "SELECT COUNT(*) AS count FROM userinput WHERE status = 'new'";
$pending_count_query = "SELECT COUNT(*) AS count FROM userinput WHERE status = 'pending'";
$completed_count_query = "SELECT COUNT(*) AS count FROM userinput WHERE status = 'done'";
$new_submissions_today_query = "SELECT COUNT(*) AS count FROM userinput WHERE DATE(datetime) = CURDATE()";

// Execute the queries
$new_count_result = $conn->query($new_count_query);
$pending_count_result = $conn->query($pending_count_query);
$completed_count_result = $conn->query($completed_count_query);
$new_submissions_today_result = $conn->query($new_submissions_today_query);

// Fetch the counts
$new_count = $new_count_result->fetch_assoc()['count'];
$pending_count = $pending_count_result->fetch_assoc()['count'];
$completed_count = $completed_count_result->fetch_assoc()['count'];
$new_submissions_today_count = $new_submissions_today_result->fetch_assoc()['count'];

// Close the database connection
$conn->close();
?>

<style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        margin: 0;
    }

    .main-content {
        flex: 1;
        padding-bottom: 60px; /* Height of the footer */
    }
</style>
<div class="main-content">
    <canvas id="myChart" class="myChart"></canvas>
</div>

<script>
    // Get the counts from PHP and pass them to JavaScript
    var newCount = <?php echo $new_count; ?>;
    var pendingCount = <?php echo $pending_count; ?>;
    var completedCount = <?php echo $completed_count; ?>;
    var newSubmissionsTodayCount = <?php echo $new_submissions_today_count; ?>;

    // Create the chart
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['New', 'Pending', 'Completed', 'New Today'],
            datasets: [{
                label: 'Submission Status',
                data: [newCount, pendingCount, completedCount, newSubmissionsTodayCount],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false, // Make the chart responsive
            responsive: true, // Make the chart responsive
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>


<?php
include '../includes/footer.php';
?>
