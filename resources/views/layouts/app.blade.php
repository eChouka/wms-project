<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>WMS Project</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Styles -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<style>
    body {
        font-family: 'Nunito', sans-serif;
        background-color: #f5f7fa;
        color: #34495e;
        margin: 0;
        padding: 0;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 250px;
        background: #2c3e50;
        padding: 15px 20px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        overflow-y: auto;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
    }

    .sidebar-collapsed {
        width: 0;
        padding: 0;
        overflow: hidden;
    }

    .sidebar-link {
        color: #ecf0f1;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 30px;
        text-align: center;
    }

    .sidebar-link:hover {
        color: #1abc9c;
    }

    .sidebar ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .sidebar ul li {
        margin-bottom: 15px;
    }

    .sidebar ul li a {
        text-decoration: none;
        color: #bdc3c7;
        display: block;
        padding: 10px 15px;
        border-radius: 4px;
        font-size: 15px;
        font-weight: 400;
        transition: all 0.3s ease;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        background-color: #3498db;
        color: #ffffff;
    }

    .sidebar a.customizer-link {
        display: block;
        text-align: center;
        background-color: #e67e22;
        color: white;
        font-weight: 600;
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 30px;
        text-decoration: none;
        font-size: 16px;
        margin-top: 20px; /* Adjusted for better spacing */
    }

    .sidebar a.customizer-link:hover {
        background-color: #d35400;
    }

    /* Divider Line */
    .sidebar-divider {
        border-top: 1.5px solid #bdc3c7;
        margin: 11px 0px 30px 0px;
    }

    /* Main Content Area */
    .main-content {
        margin-left: 270px; /* Adjust based on sidebar width */
        padding: 20px 30px;
        transition: margin-left 0.3s ease;
    }

    .sidebar-collapsed + .main-content {
        margin-left: 0;
    }

    /* Navbar Styles */
    .navbar {
        background-color: #ecf0f1;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 7px 20px; /* Restored to the initial height */
        z-index: 1001;
        position: fixed;
        top: 0;
        left: 250px; /* Aligning with the sidebar width */
        right: 0;
        transition: left 0.3s ease;
    }

    .sidebar-collapsed + .navbar {
        left: 0;
    }

    /* Extending the sidebar color to the left part of the header */
    .navbar .container-fluid {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar .navbar-brand {
        font-size: 22px;
        font-weight: 600;
        color: #1abc9c; /* New color for better contrast */
        text-decoration: none;
    }

    .navbar .navbar-brand:hover {
        color: #16a085;
    }

    .navbar .navbar-nav .nav-link {
        color: #34495e;
        font-weight: 400; /* Regular font weight */
        font-size: 14px;
    }

    .navbar .navbar-nav .nav-link:hover {
        color: #2980b9;
    }

    .navbar .dropdown-menu {
        right: 0;
        left: auto;
    }

    /* Content Area Styles */
    .content-area {
        margin-top: 70px; /* Adjusted based on navbar height */
    }

    /* Sidebar and Content Container */
    .wrapper {
        display: flex;
        align-items: stretch;
    }

    /* Additional Custom Styles */
    .content-area h1 {
        font-size: 26px; /* Slightly smaller header */
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .content-area p {
        font-size: 16px;
        line-height: 1.6;
        color: #7f8c8d;
    }

    /* Enhance buttons */
    .btn-primary {
        background-color: #3498db;
        border-color: #2980b9;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        border-color: #1c7cba;
    }

    .btn-danger {
        background-color: #e74c3c;
        border-color: #c0392b;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .btn-danger:hover {
        background-color: #c0392b;
        border-color: #a93226;
    }

    .pagination .page-item.active .page-link {
        background-color: #3498db;
        border-color: #2980b9;
    }

    .pagination .page-link:hover {
        background-color: #2980b9;
        border-color: #1c7cba;
    }

    /* Ensure the layout remains responsive */
    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            width: 250px;
            left: -250px;
            top: 0;
            height: 100vh;
            z-index: 1000;
            transition: left 0.3s ease-in-out;
        }

        .sidebar.open {
            left: 0;
        }

        .main-content {
            margin-left: 0;
            padding: 20px 15px;
        }

        .navbar {
            left: 0;
            position: fixed;
            width: 100%;
            z-index: 1001;
        }

        .sidebar.open + .main-content {
            margin-left: 0;
        }

        .sidebar.open + .navbar {
            left: 250px;
        }

        .sidebar-divider {
            margin: 15px 0;
        }

        .navbar-toggler {
            margin-right: 10px;
        }

        .navbar .sidebar-toggle {
            margin-right: 10px;
        }
    }
</style>

<body>
    <div id="app" class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <a class="navbar-brand sidebar-link" href="{{ url('/home') }}">
                WMS Project
            </a>
            <div class="sidebar-divider"></div>
            <a href="/customizer" class="customizer-link">Customizer</a>
            <ul>
                <li><a href="{{ url('/model/Supplier') }}" class="{{ request()->is('model/Supplier') ? 'active' : '' }}">Suppliers</a></li>
                <li><a href="{{ url('/model/Customer') }}" class="{{ request()->is('model/Customer') ? 'active' : '' }}">Customers</a></li>
                <li><a href="{{ url('/model/User') }}" class="{{ request()->is('model/User') ? 'active' : '' }}">Users</a></li>
                <li><a href="{{ url('/model/Product') }}" class="{{ request()->is('model/Product') ? 'active' : '' }}">Products</a></li>
                <li><a href="{{ url('/model/StockMovement') }}" class="{{ request()->is('model/StockMovement') ? 'active' : '' }}">Stock Movements</a></li>
                <li><a href="{{ url('/model/PickingJob') }}" class="{{ request()->is('model/PickingJob') ? 'active' : '' }}">Picking Jobs</a></li>
                <li><a href="{{ url('/model/Area') }}" class="{{ request()->is('model/Area') ? 'active' : '' }}">Areas</a></li>
                <li><a href="{{ url('/model/Location') }}" class="{{ request()->is('model/Location') ? 'active' : '' }}">Locations</a></li>
                <li><a href="{{ url('/model/PurchaseOrder') }}" class="{{ request()->is('model/PurchaseOrder') ? 'active' : '' }}">Purchase Orders</a></li>
                <li><a href="{{ url('/model/SalesOrder') }}" class="{{ request()->is('model/SalesOrder') ? 'active' : '' }}">Sales Orders</a></li>
            </ul>
        </nav>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-md navbar-light">
                <div class="container-fluid">
                    <button class="navbar-toggler sidebar-toggle" type="button" aria-label="Toggle sidebar" onclick="toggleSidebar()">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif

                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ url('/account') }}">Account</a>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content Area -->
            <div class="content-area">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
    </script>
</body>
</html>
