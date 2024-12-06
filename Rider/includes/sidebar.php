<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Alphasphinx</title>
  <link rel="icon" href="https://www.freeiconspng.com/uploads/sales-icon-7.png">

  <!-- Custom fonts for this template-->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles -->
  <style>
    body {
      font-family: 'Nunito', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f8f9fc;
    }

    #wrapper {
      display: flex;
    }

    .sidebar {
      background-color: #4e73df;
      color: white;
      width: 80px;
      height: 100vh;
      transition: width 0.3s ease;
      position: fixed;
      top: 0;
      left: 0;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .sidebar.expanded {
      width: 250px;
    }

    .sidebar .sidebar-brand {
      padding: 1rem;
      text-align: center;
      width: 100%;
    }

    .sidebar .sidebar-brand img {
      width: 40px;
      transition: width 0.3s ease;
    }

    .sidebar.expanded .sidebar-brand img {
      width: 80px;
    }

    .sidebar .nav-item {
      width: 100%;
    }

    .sidebar .nav-item .nav-link {
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-decoration: none;
      font-size: 1rem;
      padding: 1rem;
      width: 100%;
      transition: color 0.3s ease, padding-left 0.3s ease;
    }

    .sidebar.expanded .nav-item .nav-link {
      justify-content: flex-start;
      padding-left: 2rem;
    }

    .sidebar .nav-item .nav-link i {
      margin-right: 0;
      transition: margin-right 0.3s ease;
    }

    .sidebar.expanded .nav-item .nav-link i {
      margin-right: 1rem;
    }

    .sidebar .nav-item .nav-link:hover {
      color: #d1d3e2;
      background-color: #2e59d9;
    }

    .sidebar .sidebar-divider {
      height: 1px;
      background-color: #e3e6f0;
      width: 80%;
      margin: 1rem 0;
    }

    .sidebar .sidebar-heading {
      text-transform: uppercase;
      font-weight: bold;
      font-size: 0.75rem;
      color: #d1d3e2;
      width: 100%;
      text-align: left;
      padding-left: 1rem;
      display: none;
    }

    .sidebar.expanded .sidebar-heading {
      display: block;
    }

    .content-wrapper {
      flex: 1;
      margin-left: 80px;
      padding: 1rem;
      transition: margin-left 0.3s ease;
    }

    .content-wrapper.expanded {
      margin-left: 250px;
    }

    .sidebar-toggle {
      position: absolute;
      top: 10px;
      left: 80px;
      background-color: #4e73df;
      color: white;
      border: none;
      padding: 0.5rem;
      cursor: pointer;
      transition: left 0.3s ease;
      z-index: 1000;
    }

    .sidebar-toggle.expanded {
      left: 250px;
    }

    .content {
      padding: 2rem;
      background-color: white;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 0.5rem;
      max-width: 1100px;
      margin: 0 auto;
    }

  </style>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <!-- Sidebar - Brand -->
      <div class="sidebar-brand">
        <img src="../images/official_logo.jpg" alt="Logo">
      </div>

      <!-- Divider -->
      <div class="sidebar-divider"></div>

      <!-- Nav Item - Dashboard -->
      <div class="nav-item">
        <a class="nav-link" href="../User/welcome.php">
          <i class="fas fa-fw fa-home"></i>
          <span>Home</span>
        </a>
      </div>

      <!-- Divider -->
      <div class="sidebar-divider"></div>

      <!-- Heading -->
      <div class="sidebar-heading">General</div>

      <!-- Nav Item - Settings -->
      <div class="nav-item">
        <a class="nav-link" href="../pages/settings.php">
          <i class="fas fa-cogs"></i>
          <span>Settings</span>
        </a>
      </div>

      <!-- Nav Item - Logout -->
      <div class="nav-item">
        <a class="nav-link" href="../User/logout.php">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </div>

      <!-- Divider -->
      <div class="sidebar-divider"></div>
    </div>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="content-wrapper">
      <?php include_once 'topbar.php'; ?>
      <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
      <div class="content">
        <!-- Main content goes here -->
      </div>
    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    // Toggle sidebar and content wrapper
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const contentWrapper = document.getElementById('content-wrapper');

    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('expanded');
      contentWrapper.classList.toggle('expanded');
      sidebarToggle.classList.toggle('expanded');
    });
  </script>

</body>

</html>
