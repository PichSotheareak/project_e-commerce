<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kaiadmin - Bootstrap 5 Admin Dashboard</title>
    <meta
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
        name="viewport"
    />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700&display=swap" rel="stylesheet">

    <link
        rel="icon"
        href="{{ asset('assets/img/kaiadmin/favicon.ico') }}"
        type="image/x-icon"
    />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Fonts and icons -->
    <!-- Font Awesome 6 Free -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" defer></script>
    <style>
        body {
            font-family: "Roboto", sans-serif !important;
            font-optical-sizing: auto;
            font-weight: 400; /* You can set 100, 300, 400, 500, 700 depending on your needs */
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }
        .bt-end{
            bottom: 14px !important;
            position: absolute;
            align-items: end;
            display: flex
        ;
            justify-content: end;
            /* align-content: end; */
            right: 16px;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            min-width: 70px;
            justify-content: center;
        }

        .action-btn i {
            font-size: 0.8rem;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-decoration: none;
        }

        .btn-edit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white !important;
            border-color: #ff6b6b;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #ff5252 0%, #e04848 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }
    </style>

    <script>
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
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
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
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <ul class="nav nav-secondary ">
                    <li class="nav-item active">
                        <a
                            data-bs-toggle="collapse"
                            href="#dashboard"
                            class="collapsed"
                            aria-expanded="false"
                        >
                            <i class="fas fa-home me-2"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                        <h4 class="text-section">Components</h4>
                    </li>

                    <li class="nav-item">
                        <a href="/branches" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-code-branch me-2"></i><span>Branches</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/brands" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-tags me-2"></i><span>Brands</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/categories" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-list-alt me-2"></i><span>Categories</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/products" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-box-open me-2"></i><span>Products</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/product-details" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-info-circle me-2"></i><span>Product Details</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/orders" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-shopping-cart me-2"></i><span>Orders</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/order-details" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-receipt me-2"></i><span>Order Details</span>
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
                        <a href="/payment-methods" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-money-check-alt me-2"></i><span>Payment Methods</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/customers" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-user-friends me-2"></i><span>Customers</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/staff" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-user-tie me-2"></i><span>Staff</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/users" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-users me-2"></i><span>Users</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/profile" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-id-badge me-2"></i><span>Profile</span>
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
                    <nav
                        class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
                    >
<!--                        <div class="input-group">-->
<!--                            <div class="input-group-prepend">-->
<!--                                <button type="submit" class="btn btn-search pe-1">-->
<!--                                    <i class="fa fa-search search-icon"></i>-->
<!--                                </button>-->
<!--                            </div>-->
<!--                            <input-->
<!--                                type="text"-->
<!--                                placeholder="Search ..."-->
<!--                                class="form-control"-->
<!--                            />-->
<!--                        </div>-->
                    </nav>

                    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                        <li
                            class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
                        >
                            <a
                                class="nav-link dropdown-toggle"
                                data-bs-toggle="dropdown"
                                href="#"
                                role="button"
                                aria-expanded="false"
                                aria-haspopup="true"
                            >
                                <i class="fa fa-search"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-search animated fadeIn">
                                <form class="navbar-left navbar-form nav-search">
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            placeholder="Search ..."
                                            class="form-control"
                                        />
                                    </div>
                                </form>
                            </ul>
                        </li>
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
                                            <a href="#">
                                                <div class="notif-img">
                                                    <img
                                                        src="assets/img/chadengle.jpg"
                                                        alt="Img Profile"
                                                    />
                                                </div>
                                                <div class="notif-content">
                                                    <span class="subject">Chad</span>
                                                    <span class="block"> Ok, Thanks ! </span>
                                                    <span class="time">12 minutes ago</span>
                                                </div>
                                            </a>
                                            <a href="#">
                                                <div class="notif-img">
                                                    <img
                                                        src="assets/img/mlane.jpg"
                                                        alt="Img Profile"
                                                    />
                                                </div>
                                                <div class="notif-content">
                                                    <span class="subject">Jhon Doe</span>
                                                    <span class="block">
                                Ready for the meeting today...
                              </span>
                                                    <span class="time">12 minutes ago</span>
                                                </div>
                                            </a>
                                            <a href="#">
                                                <div class="notif-img">
                                                    <img
                                                        src="assets/img/talha.jpg"
                                                        alt="Img Profile"
                                                    />
                                                </div>
                                                <div class="notif-content">
                                                    <span class="subject">Talha</span>
                                                    <span class="block"> Hi, Apa Kabar ? </span>
                                                    <span class="time">17 minutes ago</span>
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
                                <span class="notification">4</span>
                            </a>
                            <ul
                                class="dropdown-menu notif-box animated fadeIn"
                                aria-labelledby="notifDropdown"
                            >
                                <li>
                                    <div class="dropdown-title">
                                        You have 4 new notification
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
                                            <a href="#">
                                                <div class="notif-icon notif-success">
                                                    <i class="fa fa-comment"></i>
                                                </div>
                                                <div class="notif-content">
                              <span class="block">
                                Rahmad commented on Admin
                              </span>
                                                    <span class="time">12 minutes ago</span>
                                                </div>
                                            </a>
                                            <a href="#">
                                                <div class="notif-img">
                                                    <img
                                                        src="assets/img/profile2.jpg"
                                                        alt="Img Profile"
                                                    />
                                                </div>
                                                <div class="notif-content">
                              <span class="block">
                                Reza send messages to you
                              </span>
                                                    <span class="time">12 minutes ago</span>
                                                </div>
                                            </a>
                                            <a href="#">
                                                <div class="notif-icon notif-danger">
                                                    <i class="fa fa-heart"></i>
                                                </div>
                                                <div class="notif-content">
                                                    <span class="block"> Farrah liked Admin </span>
                                                    <span class="time">17 minutes ago</span>
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
                        <li class="nav-item topbar-icon dropdown hidden-caret">
                            <a
                                class="nav-link"
                                data-bs-toggle="dropdown"
                                href="#"
                                aria-expanded="false"
                            >
                                <i class="fas fa-layer-group"></i>
                            </a>
                            <div class="dropdown-menu quick-actions animated fadeIn">
                                <div class="quick-actions-header">
                                    <span class="title mb-1">Quick Actions</span>
                                    <span class="subtitle op-7">Shortcuts</span>
                                </div>
                                <div class="quick-actions-scroll scrollbar-outer">
                                    <div class="quick-actions-items">
                                        <div class="row m-0">
                                            <a class="col-6 col-md-4 p-0" href="#">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item bg-danger rounded-circle">
                                                        <i class="far fa-calendar-alt"></i>
                                                    </div>
                                                    <span class="text">Calendar</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="#">
                                                <div class="quick-actions-item">
                                                    <div
                                                        class="avatar-item bg-warning rounded-circle"
                                                    >
                                                        <i class="fas fa-map"></i>
                                                    </div>
                                                    <span class="text">Maps</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="#">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item bg-info rounded-circle">
                                                        <i class="fas fa-file-excel"></i>
                                                    </div>
                                                    <span class="text">Reports</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="#">
                                                <div class="quick-actions-item">
                                                    <div
                                                        class="avatar-item bg-success rounded-circle"
                                                    >
                                                        <i class="fas fa-envelope"></i>
                                                    </div>
                                                    <span class="text">Emails</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="#">
                                                <div class="quick-actions-item">
                                                    <div
                                                        class="avatar-item bg-primary rounded-circle"
                                                    >
                                                        <i class="fas fa-file-invoice-dollar"></i>
                                                    </div>
                                                    <span class="text">Invoice</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="#">
                                                <div class="quick-actions-item">
                                                    <div
                                                        class="avatar-item bg-secondary rounded-circle"
                                                    >
                                                        <i class="fas fa-credit-card"></i>
                                                    </div>
                                                    <span class="text">Payments</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                        src="assets/img/profile.jpg"
                                        alt="..."
                                        class="avatar-img rounded-circle"
                                    />
                                </div>
                                <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold">Hizrian</span>
                    </span>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <div class="dropdown-user-scroll scrollbar-outer">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-lg">
                                                <img
                                                    src="assets/img/profile.jpg"
                                                    alt="image profile"
                                                    class="avatar-img rounded"
                                                />
                                            </div>
                                            <div class="u-text">
                                                <h4>Hizrian</h4>
                                                <p class="text-muted">hello@example.com</p>
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
                                        <a class="dropdown-item" href="#">My Profile</a>
                                        <a class="dropdown-item" href="#">My Balance</a>
                                        <a class="dropdown-item" href="#">Inbox</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">Account Setting</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="/login">Logout</a>
                                    </li>
                                </div>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>
        <div class="pt-5">
            <div class="row ">
                <div class="col-6">User Management</div>
                <div class="col-6"></div>
            </div>
        </div>
           <div id="app" class=" mt-5 p-5">
               <div class="d-flex justify-content-between mb-3">
                   <h3>Member List</h3>
                   <button class="btn btn-primary rounded" type="button" @click="openAddModal" title="Add Member">
                       <i class="fa-solid fa-person-circle-plus fa-lg"></i>
                   </button>
               </div>

               <table class="table table-hover">
                   <thead>
                   <tr>
                       <th>ID</th>
                       <th>Name</th>
                       <th>Gender</th>
                       <th>Email</th>
                       <th>Status</th>
                       <th>Actions</th>
                   </tr>
                   </thead>
                   <tbody>
                   <tr v-for="(member, index) in members" :key="index">
                       <td @click="viewMemberDetails(member)" style="cursor: pointer;">[[ member.id ]]</td>
                       <td @click="viewMemberDetails(member)" style="cursor: pointer;">[[ member.name ]]</td>
                       <td @click="viewMemberDetails(member)" style="cursor: pointer;">[[ member.gender ]]</td>
                       <td @click="viewMemberDetails(member)" style="cursor: pointer;">[[ member.email ]]</td>
                       <td @click="viewMemberDetails(member)" style="cursor: pointer;">
                           <span class="badge" :class="statusClass(member.status)">[[ member.status ]]</span>
                       </td>
                       <td>

                               <div class="d-flex">
                                   <div class="me-3">
                                       <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(index)">
                                           <i class="fa-solid fa-pen me-2"></i>Edit
                                       </a>
                                   </div>
                                   <div class="text-white">
                                       <a class="action-btn btn-delete" href="#" @click.prevent="deleteMember(index)">
                                           <i class="fa-solid fa-trash me-2"></i>Delete
                                       </a>
                                   </div>
                               </div>
                       </td>

                   </tr>
                   </tbody>
               </table>

               <!-- Add/Edit Offcanvas -->
               <div class="offcanvas offcanvas-end" tabindex="-1" id="memberOffcanvas" aria-labelledby="memberOffcanvasLabel">
                   <div class="offcanvas-header">
                       <h5 id="memberOffcanvasLabel">[[ isEditing ? 'Edit' : 'Add' ]] Member</h5>
                       <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                   </div>
                   <div class="offcanvas-body" v-if="currentMember">
                       <form @submit.prevent="saveMember">
                           <div class="mb-2">
                               <label>Name</label>
                               <input type="text" class="form-control" v-model="currentMember.name" required>
                           </div>
                           <div class="mb-2">
                               <label>Email</label>
                               <input type="email" class="form-control" v-model="currentMember.email" required>
                           </div>
                           <div class="mb-2">
                               <label>Gender</label>
                               <select class="form-select" v-model="currentMember.gender">
                                   <option>Male</option>
                                   <option>Female</option>
                               </select>
                           </div>
                           <div class="mb-2">
                               <label>Status</label>
                               <select class="form-select" v-model="currentMember.status">
                                   <option>Confirmed</option>
                                   <option>Pending</option>
                                   <option>Cancelled</option>
                                   <option>Not Connected</option>
                               </select>
                           </div>
                           <div class="bt-end">
                               <button class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancel</button>
                               <button class="btn btn-primary" type="submit">[[ isEditing ? 'Update' : 'Add' ]]</button>
                           </div>
                       </form>
                   </div>
               </div>

               <!-- View Details Offcanvas -->
               <div class="offcanvas offcanvas-end" tabindex="-1" id="viewOffcanvas" aria-labelledby="viewOffcanvasLabel">
                   <div class="offcanvas-header">
                       <div class="w-100 d-flex justify-content-between align-items-start">
                           <div v-if="viewMember">
                               <h5 class="mb-0">[[ viewMember.name ]]</h5>
                               <div class="mt-1">
                                   <span class="badge bg-primary me-1" v-if="viewMember.status === 'Active'">Active</span>
                                   <span class="badge bg-secondary" v-else>Inactive</span>
                               </div>
                           </div>
                           <div class="dropdown">
                               <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                   <i class="bi bi-three-dots-vertical fs-5"></i>
                               </button>
                               <ul class="dropdown-menu dropdown-menu-end">
                                   <li><a class="dropdown-item" href="#" @click="editFromView"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                   <li><a class="dropdown-item" href="#"><i class="bi bi-slash-circle me-2"></i>Disable</a></li>
                                   <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i>Delete</a></li>
                               </ul>
                           </div>
                       </div>
                       <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                   </div>

                   <div class="offcanvas-body" v-if="viewMember">
                       <div class="text-center mb-4">
                           <img :src="viewMember.photo || 'https://via.placeholder.com/100'" alt="Profile Photo"
                                class="rounded-circle shadow" width="100" height="100">
                       </div>

                       <div class="card-body">
                           <div class="mb-3">
                               <div class="text-muted small">Date of Birth</div>
                               <div class="fw-semibold">[[ viewMember.dob || 'N/A' ]]</div>
                           </div>
                           <div class="mb-3">
                               <div class="text-muted small">Gender</div>
                               <div class="fw-semibold">[[ viewMember.gender ]]</div>
                           </div>
                           <div class="mb-3">
                               <div class="text-muted small">Phone Number</div>
                               <div class="fw-semibold">[[ viewMember.phone || 'N/A' ]]</div>
                           </div>
                           <div class="mb-3">
                               <div class="text-muted small">Role</div>
                               <div class="fw-semibold">[[ viewMember.role || 'N/A' ]]</div>
                           </div>
                           <div class="mb-3">
                               <div class="text-muted small">Created By</div>
                               <div class="fw-semibold">[[ viewMember.createdBy || 'N/A' ]]</div>
                           </div>
                           <div class="mb-3">
                               <div class="text-muted small">Created Date</div>
                               <div class="fw-semibold">[[ viewMember.createdDate || 'N/A' ]]</div>
                           </div>
                       </div>

                   </div>
               </div>

           </div>

</div>

<!--   Core JS Files   -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <script src="https://cdn.jsdelivr.net/npm/vue@3.4.21/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const { createApp } = Vue;

        createApp({
            delimiters: ['[[', ']]'],
            data() {
                return {
                    members: [
                        { id: '1', name: 'John Doe', gender: 'Male', email: 'john.doe@email.com', status: 'Confirmed', joinedDate: '2023-05-01', phone: '123456789', address: '123 Street' },
                        { id: '2', name: 'Jane Smith', gender: 'Female', email: 'jane.smith@email.com', status: 'Pending', joinedDate: '2023-06-10', phone: '987654321', address: '456 Avenue' }
                    ],
                    currentMember: null,
                    viewMember: null,
                    isEditing: false,
                    editIndex: null
                };
            },
            methods: {
                openAddModal() {
                    this.currentMember = { id: '', name: '', email: '', gender: 'Male', status: 'Pending' };
                    this.isEditing = false;
                    // Show offcanvas
                    const offcanvas = new bootstrap.Offcanvas(document.getElementById('memberOffcanvas'));
                    offcanvas.show();
                },
                openEditModal(index) {
                    this.currentMember = { ...this.members[index] };
                    this.isEditing = true;
                    this.editIndex = index;
                    const offcanvas = new bootstrap.Offcanvas(document.getElementById('memberOffcanvas'));
                    offcanvas.show();
                },
                validateFields() {
                    // Check if name is empty
                    if (!this.currentMember.name || this.currentMember.name.trim() === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Name field is required!',
                            confirmButtonColor: '#dc3545'
                        });
                        return false;
                    }

                    // Check if email is empty
                    if (!this.currentMember.email || this.currentMember.email.trim() === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Email field is required!',
                            confirmButtonColor: '#dc3545'
                        });
                        return false;
                    }

                    // Validate email format
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(this.currentMember.email)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please enter a valid email address!',
                            confirmButtonColor: '#dc3545'
                        });
                        return false;
                    }

                    // Check for duplicate email (only when adding new member or changing email)
                    const isDuplicateEmail = this.members.some((member, index) => {
                        if (this.isEditing && index === this.editIndex) {
                            return false; // Skip current member when editing
                        }
                        return member.email.toLowerCase() === this.currentMember.email.toLowerCase();
                    });

                    if (isDuplicateEmail) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'This email address is already registered!',
                            confirmButtonColor: '#dc3545'
                        });
                        return false;
                    }

                    return true;
                },
                saveMember() {
                    // Validate fields before saving
                    if (!this.validateFields()) {
                        return;
                    }

                    if (this.isEditing) {
                        this.members[this.editIndex] = { ...this.currentMember };
                        // Show success message for update
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'Member has been updated successfully!',
                            confirmButtonColor: '#198754',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        const newId = '#00' + (this.members.length + 1);
                        this.members.push({ ...this.currentMember, id: newId });
                        // Show success message for add
                        Swal.fire({
                            icon: 'success',
                            title: 'Added!',
                            text: 'New member has been added successfully!',
                            confirmButtonColor: '#198754',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }

                    // Hide the offcanvas
                    const el = document.getElementById('memberOffcanvas');
                    bootstrap.Offcanvas.getInstance(el)?.hide();
                },
                deleteMember(index) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.members.splice(index, 1);
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Member has been deleted successfully!',
                                confirmButtonColor: '#198754',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                },
                statusClass(status) {
                    switch (status) {
                        case 'Confirmed': return 'bg-success';
                        case 'Pending': return 'bg-secondary';
                        case 'Cancelled': return 'bg-danger';
                        case 'Not Connected': return 'bg-warning text-dark';
                        default: return 'bg-light';
                    }
                },
                viewMemberDetails(member) {
                    this.viewMember = member;
                    const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewOffcanvas'));
                    offcanvas.show();
                },
                editFromView() {
                    this.openEditModal(this.members.findIndex(m => m.id === this.viewMember.id));
                }
            }
        }).mount('#app');
    </script>

</body>
</html>
