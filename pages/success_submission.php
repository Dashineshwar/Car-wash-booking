<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f7f7f7;
        }

        .container {
            width: 90%;
            max-width: 500px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .container img {
            max-width: 100px;
            margin-bottom: 20px;
        }

        h1 {
            font-family: "Arial Black", sans-serif;
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
        }

        h2 {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.2em;
            font-weight: bold;
            color: #666;
            margin-bottom: 20px;
        }

        h3 {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.8em;
            font-weight: bold;
            color: blue;
            margin-bottom: 20px;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            display: inline-block;
            margin-right: 20px;
            color: #333;
        }

        .social-links a img {
            max-width: 40px;
            height: auto;
            border-radius: 50%;
            transition: transform 0.8s ease;
        }

        .social-links a img:hover {
            transform: scale(1.1);
        }

        /* Popup Styles */
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            display: none;
        }

        .popup-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        /* Media Queries for Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            h1 {
                font-size: 1.3em;
            }
            h2 {
                font-size: 1.1em;
            }
            h3 {
                font-size: 0.7em;
            }
        }

        @media (max-width: 480px) {
            .container img {
                max-width: 80px;
            }
            h1 {
                font-size: 1.2em;
            }
            h2 {
                font-size: 1em;
            }
            h3 {
                font-size: 0.6em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="../images/logo.jpg" alt="Logo">
        <h1>Thank you <?php echo htmlspecialchars($_GET['name']); ?>, we have received your information.</h1>
        <h2>Our top leader will reach you out as soon as possible.</h2>

        <h3>Follow us to get updates about Renata Health World.</h3>
        <div class="social-links">
            <a href="https://www.instagram.com" target="_blank"><img src="../images/icons/instagram.png" alt="Instagram"></a>
            <a href="https://www.facebook.com" target="_blank"><img src="../images/icons/facebook.png" alt="Facebook"></a>
            <a href="https://www.example.com" target="_blank"><img src="../images/icons/website.png" alt="Website"></a>
        </div>
    </div>

    <div class="popup" id="popup">
        <div class="popup-content">
            <h2>Redirecting in <span id="countdown">5</span> seconds...</h2>
            <p>You will be redirected to our website shortly.</p>
        </div>
    </div>

    <script>
        function showPopup() {
            var popup = document.getElementById('popup');
            var countdownElement = document.getElementById('countdown');
            var countdown = 5;

            popup.style.display = 'flex';

            var countdownInterval = setInterval(function() {
                countdown--;
                countdownElement.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = 'https://www.irenataworld.com/'; // Change to your website URL
                }
            }, 1000);
        }

        window.onload = function() {
            setTimeout(showPopup, 5000); // Show popup after a short delay
        };
    </script>
</body>
</html>
