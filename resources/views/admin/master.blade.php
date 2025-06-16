<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kaiadmin - Bootstrap 5 Admin Dashboard</title>
    <meta
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
        name="viewport"
    />
    <link
        rel="icon"
        href="{{ asset('assets/img/kaiadmin/favicon.ico') }}"
        type="image/x-icon"
    />

    <!-- Fonts and icons -->
    <!-- Font Awesome 6 Free -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- WebFont Loader -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webfont/1.6.28/webfontloader.js"></script>

    <script>
        // Only run WebFont.load if WebFont is defined
        if (typeof WebFont !== 'undefined') {
            WebFont.load({
                google: { families: ["Public Sans:300,400,500,600,700"] },
                custom: {
                    families: [
                        "Font Awesome 5 Solid",
                        "Font Awesome 5 Regular",
                        "Font Awesome 5 Brands",
                        "simple-line-icons",
                    ],
                    urls: ["{{ asset('assets/css/fonts.min.css') }}"],
                },
                active: function () {
                    sessionStorage.fonts = true;
                },
            });
        }
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vue.js 3 -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Axios -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.0/axios.min.js"></script>

    <style>
        .profile-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        .upload-area {
            max-width: 300px;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #f8f9fa;
        }

        .profile-photo-container {
            position: relative;
            display: inline-block;
        }

        .remove-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
        }

        .remove-btn:hover {
            background: #c82333;
        }

        .action-btn {
            text-decoration: none;
            font-size: 0.875rem;
            padding: 0.25rem 0;
            transition: color 0.15s ease-in-out;
        }

        .btn-edit {
            color: #6c757d;
        }

        .btn-edit:hover {
            color: #007bff;
        }

        .btn-delete {
            color: #6c757d;
        }

        .btn-delete:hover {
            color: #dc3545;
        }

        .pagination-container {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        .page-size-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-info {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .search-stats {
            font-size: 0.875rem;
            color: #6c757d;
        }

        mark {
            background-color: #fff3cd;
            padding: 0;
        }

        .border-dashed {
            border-style: dashed !important;
        }

        .border-gray-300 {
            border-color: #d1d5db !important;
        }

        .text-gray-500 {
            color: #6b7280 !important;
        }

        .bg-gray-50:hover {
            background-color: #f9fafb !important;
        }

        .bg-gray-100 {
            background-color: #f3f4f6 !important;
        }
    </style>
</head>
<body>
@php
$user = auth()->user()->load('profile');
$profileImage = ($user->profile && $user->profile->image)
? asset('storage/' . $user->profile->image)
: asset('assets/img/default-profile.jpg');
@endphp

<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
                <a href="{{ route('dashboard') }}" class="logo">
                    <img
                        src="assets/img/kaiadmin/logo_light.svg"
                        alt="navbar brand"
                        class="navbar-brand"
                        height="20"
                    />
                </a>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="gg-menu-right"></i>
                    </button>
                    <button class="btn btn-toggle sidenav-toggler">
                        <i class="gg-menu-left"></i>
                    </button>
                </div>
                <button class="topbar-toggler more">
                    <i class="gg-more-vertical-alt"></i>
                </button>
            </div>
            <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <ul class="nav nav-secondary ">
                    <li class="nav-item">
                        <a  href="{{ route('dashboard') }}" class="collapsed" >
                            <i class="fas fa-home me-2"></i><span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                        <h4 class="text-section">Components</h4>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('branches') }}" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-code-branch me-2"></i><span>Branches</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('brands') }}" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-tags me-2"></i><span>Brands</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('categories') }}" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-list-alt me-2"></i><span>Categories</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('products') }}" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-box-open me-2"></i><span>Products</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('productDetails') }}" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-info-circle me-2"></i><span>Product Details</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/orders" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-shopping-cart me-2"></i><span>Orders</span>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="/invoices" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-file-invoice-dollar me-2"></i><span>Invoices</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/payments" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-credit-card me-2"></i><span>Payments</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('customers') }}" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-user-friends me-2"></i><span>Customers</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('staff') }}" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-user-tie me-2"></i><span>Staff</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('users') }}" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-users me-2"></i><span>Users</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/contact-us" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-envelope me-2"></i><span>Contact Us</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
    <!-- End Sidebar -->

    <div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img
                            src="assets/img/kaiadmin/logo_light.svg"
                            alt="navbar brand"
                            class="navbar-brand"
                            height="20"
                        />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
                <!-- End Logo Header -->
            </div>
            <!-- Navbar Header -->
            <nav
                class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
            >
                <div class="container-fluid">

                    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                        <li class="nav-item topbar-icon dropdown hidden-caret">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                id="messageDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <i class="fa fa-envelope"></i>
                            </a>
                            <ul
                                class="dropdown-menu messages-notif-box animated fadeIn"
                                aria-labelledby="messageDropdown"
                            >
                                <li>
                                    <div
                                        class="dropdown-title d-flex justify-content-between align-items-center"
                                    >
                                        Messages
                                        <a href="#" class="small">Mark all as read</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="message-notif-scroll scrollbar-outer">
                                        <div class="notif-center">
                                            <a href="#">
                                                <div class="notif-img">
                                                    <img
                                                        src="assets/img/jm_denis.jpg"
                                                        alt="Img Profile"
                                                    />
                                                </div>
                                                <div class="notif-content">
                                                    <span class="subject">Jimmy Denis</span>
                                                    <span class="block"> How are you ? </span>
                                                    <span class="time">5 minutes ago</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a class="see-all" href="javascript:void(0);"
                                    >See all messages<i class="fa fa-angle-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item topbar-icon dropdown hidden-caret">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                id="notifDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <i class="fa fa-bell"></i>
                                <span class="notification">1</span>
                            </a>
                            <ul
                                class="dropdown-menu notif-box animated fadeIn"
                                aria-labelledby="notifDropdown"
                            >
                                <li>
                                    <div class="dropdown-title">
                                        You have 1 new notification
                                    </div>
                                </li>
                                <li>
                                    <div class="notif-scroll scrollbar-outer">
                                        <div class="notif-center">
                                            <a href="#">
                                                <div class="notif-icon notif-primary">
                                                    <i class="fa fa-user-plus"></i>
                                                </div>
                                                <div class="notif-content">
                                                    <span class="block"> New user registered </span>
                                                    <span class="time">5 minutes ago</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a class="see-all" href="javascript:void(0);"
                                    >See all notifications<i class="fa fa-angle-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item topbar-user dropdown hidden-caret">
                            <a
                                class="dropdown-toggle profile-pic"
                                data-bs-toggle="dropdown"
                                href="#"
                                aria-expanded="false"
                            >
                                <div class="avatar-sm">
                                    <img
                                        src="{{ $profileImage }}"
                                        alt="..."
                                        class="avatar-img rounded-circle"
                                    />
                                </div>
                                <span class="profile-username">
                                    <span class="op-7">Hi,</span>
                                    <span class="fw-bold">{{ auth()->user()->name }}</span>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <div class="dropdown-user-scroll scrollbar-outer">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-lg">
                                                <img
                                                    src="{{ $profileImage }}"
                                                    alt="image profile"
                                                    class="avatar-img rounded"
                                                />
                                            </div>
                                            <div class="u-text">
                                                <h4>{{ auth()->user()->name }}</h4>
                                                <p class="text-muted">{{ auth()->user()->email }}</p>
                                                <a
                                                    href="profile.html"
                                                    class="btn btn-xs btn-secondary btn-sm"
                                                >View Profile</a
                                                >
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ route('profile') }}">My Profile</a>
                                        <a class="dropdown-item" href="#">My Balance</a>
                                        <a class="dropdown-item" href="#">Inbox</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">Account Setting</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>

                                    </li>
                                </div>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>

        <div class="container">
            @yield('content')
        </div>

        <footer class="footer">
        </footer>
    </div>

</div>

<!--   Core JS Files   -->
<script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

<!-- jQuery Scrollbar -->
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

<!-- Chart JS -->
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

<!-- jQuery Sparkline -->
<script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Chart Circle -->
<script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- jQuery Vector Maps -->
<script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>

<!-- Sweet Alert -->
<script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Kaiadmin JS -->
<script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

<!-- Kaiadmin DEMO methods, don't include it in your project! -->
<script src="{{ asset('assets/js/setting-demo.js') }}"></script>
<script src="{{ asset('assets/js/demo.js') }}"></script>

<!-- Font Awesome JS (defer loading) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" defer></script>

<script>
    // Initialize charts only if elements exist
    $(document).ready(function() {
        if ($("#lineChart").length) {
            $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
                type: "line",
                height: "70",
                width: "100%",
                lineWidth: "2",
                lineColor: "#177dff",
                fillColor: "rgba(23, 125, 255, 0.14)",
            });
        }

        if ($("#lineChart2").length) {
            $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
                type: "line",
                height: "70",
                width: "100%",
                lineWidth: "2",
                lineColor: "#f3545d",
                fillColor: "rgba(243, 84, 93, .14)",
            });
        }

        if ($("#lineChart3").length) {
            $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
                type: "line",
                height: "70",
                width: "100%",
                lineWidth: "2",
                lineColor: "#ffa534",
                fillColor: "rgba(255, 165, 52, .14)",
            });
        }
    });
</script>

@yield('scripts')

</body>
</html>
