<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Dash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #fff;
        }

        .container {
            text-align: center;
            width: 90%;
            max-width: 400px;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
        }

        .logo img {
            max-width: 100px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 1.8em;
            margin-bottom: -10px;
            color: black.0.0;
        }

        p.subtitle {
            font-size: 1em;
            margin-bottom: 20px;
            color: #737373;
        }

        .features {
            background-color: #f7f7f7;
            padding: 20px;
            border-radius: 30px;
            margin-bottom: 20px;
            text-align: left;
        }

        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .feature:last-child {
            margin-bottom: 0;
        }

        .feature-icon {
            font-size: 24px;
            margin-right: 15px;
            color: #333;
        }

        .feature-text h3 {
            font-size: 1em;
            margin: 0;
            color: #333;
        }

        .feature-text p {
            margin: 5px 0 0;
            font-size: 0.9em;
            color: #737373;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #444;
        }

        .info {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9em;
            color: #737373;
        }

        .info-icon {
            font-size: 18px;
            margin-right: 10px;
            color: #737373;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .features {
                padding: 15px;
            }

            .feature-text h3 {
                font-size: 0.9em;
            }

            .feature-text p {
                font-size: 0.8em;
            }

            .btn {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .feature-icon {
                font-size: 20px;
            }

            .btn {
                font-size: 12px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="../images/official_logo.jpg" alt="Logo">
        </div>
        <h1>Welcome to Dash</h1>
        <p class="subtitle">We’re glad you’re here</p>
        <div class="features">
            <div class="feature">
                <div class="feature-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="feature-text">
                    <h3>3 steps to a clean vehicle</h3>
                    <p>Takes less than 2 minutes to book for a car wash</p>
                </div>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="feature-text">
                    <h3>Founder support</h3>
                    <p>1:1 help when you need it</p>
                </div>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="feature-text">
                    <h3>Save your time</h3>
                    <p>We do the washing so you can focus on more important tasks</p>
                </div>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="feature-text">
                    <h3>Cheap price</h3>
                    <p>Save money while washing your car</p>
                </div>
            </div>
        </div>
        <a href="welcome.php" class="btn">Let’s go!</a>
        <div class="info">
            <div class="info-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <p>We will send email promotions to you so you can stay updated with us</p>
        </div>
    </div>
</body>
</html>
