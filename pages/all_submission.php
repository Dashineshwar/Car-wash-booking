<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All submissions</title>
    <style>
        /* Add your CSS styles for responsiveness here */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #f2f2f2;
        }
        /* Define colors for different status */
        .status-new {
            color: #ff6347; /* Red */
        }
        .status-pending {
            color: #ffa500; /* Orange */
        }
        .status-done {
            color: #008000; /* Green */
        }
        .status-symbol {
            font-weight: bold;
            font-size: 120%;
        }
        @media screen and (max-width: 600px) {
            .table td, .table th {
                padding: 5px;
                font-size: 12px;
            }
            .table img {
                width: 30px;
                height: 30px;
            }
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff; /* Blue */
            color: white;
            font-size: 13px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            text-decoration: none; /* Remove underline */
        }

        .button:hover {
            text-decoration: none; /* Remove underline */
            color: white;
        }
        .Page-title {
            font-size: 20px;
        }
        
        /* Scrollable table container */
        .table-container {
            max-height: 400px; /* Adjust this height as needed */
            overflow-y: auto;
        }
    </style>
</head>
<body>

<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

// Fetch data ordered by timestamp in descending order
$query = "SELECT * FROM userinput ORDER BY datetime DESC";
$result = $conn->query($query);
?>

<div class="main-content">
    <h2 id="Page-title" class="Page-title"><strong>All Submissions</strong></h2>
    <br>
    <a href="submission.php" class="button">View New Submissions</a>
    <br>
    <div class="table-container">
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Country</th>
                    <th>WhatsApp</th>
                    <th>Date & Time</th>
                    <th>Contact</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through each row in the result set
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><span id="status_<?php echo $row['id']; ?>" class="status-symbol <?php echo 'status-' . strtolower($row['status']); ?>">*</span> <?php echo $row['name']; ?></td>
                        <td><?php echo $row['country']; ?></td>
                        <td><?php echo $row['whatsapp']; ?></td>
                        <td><?php echo $row['datetime']; ?></td>
                        <td><a href="https://wa.me/<?php echo preg_replace('/[^\d]+/', '', $row['whatsapp']); ?>" target="_blank"><img src="../images/icons/whatsapp.png" alt="WhatsApp" style="width:40px; height:40px;"></a></td>
                        <td class="<?php echo 'status-' . strtolower($row['status']); ?>">
                            <select class="form-control" onchange="updateStatus(this, <?php echo $row['id']; ?>)">
                                <option value="new" <?php if ($row['status'] == 'new') echo 'selected'; ?>>New</option>
                                <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="done" <?php if ($row['status'] == 'done') echo 'selected'; ?>>Done</option>
                            </select>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function updateStatus(select, id) {
        var status = select.value;
        var statusSymbol = document.getElementById('status_' + id);
        statusSymbol.className = 'status-symbol status-' + status;
        
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../php/update_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log(xhr.responseText); // For debugging
            }
        };
        xhr.send("id=" + id + "&status=" + status);
    }
</script>

<?php
include '../includes/footer.php';
?>

</body>
</html>
