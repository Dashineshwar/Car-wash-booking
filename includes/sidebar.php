<!-- sidebar.php -->
<div id="sidebar-wrapper" class="bg-light border-right d-flex flex-column">
    <div class="sidebar-heading text-center">
    </div>
    <div class="sidebar-heading text-center">
        <img src="/path/to/your/logo.png" alt="Logo" id="logo" class="logo-collapsed"> <!-- Replace with the correct logo path -->
    </div>
    <ul class="list-group list-group-flush flex-grow-1">
        <li class="list-group-item text-center sidebar-item">
            <i class="fas fa-home"></i>
            <span class="sidebar-text">Home</span>
        </li>
        <li class="list-group-item text-center sidebar-item">
            <i class="fas fa-history"></i>
            <span class="sidebar-text">Booking History</span>
        </li>
        <li class="list-group-item text-center sidebar-item">
            <i class="fas fa-user"></i>
            <span class="sidebar-text">Manage Account</span>
        </li>
        <li class="list-group-item text-center sidebar-item">
            <i class="fas fa-envelope"></i>
            <span class="sidebar-text">Contact Us</span>
        </li>
    </ul>

    <!-- Move toggle button to the bottom -->
    <div class="sidebar-toggle text-center">
        <button id="sidebarToggle" class="btn toggle-btn">
            ☰
        </button>
    </div>
</div>

<style>
#sidebar-wrapper {
    width: 70px; /* Collapsed width */
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #f8f9fa;
    z-index: 1000;
    transition: all 0.3s ease;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Ensure items are evenly distributed */
}

#sidebar-wrapper.expanded {
    width: 250px; /* Expanded width */
}

.sidebar-heading {
    padding: 10px;
    margin-bottom: 10px;
}

.logo-collapsed {
    width: 40px;
    height: 40px;
}

.logo-expanded {
    width: 150px;
    height: auto;
}

.sidebar-item i {
    font-size: 24px;
}

.sidebar-item .sidebar-text {
    display: none;
    font-size: 14px;
    padding-left: 10px;
}

#sidebar-wrapper.expanded .sidebar-item .sidebar-text {
    display: inline-block;
}

#sidebar-wrapper.expanded .sidebar-item {
    text-align: left;
    padding-left: 20px;
}

.sidebar-toggle {
    padding: 10px;
    margin-bottom: 20px;
}

.toggle-btn {
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
}
</style>
