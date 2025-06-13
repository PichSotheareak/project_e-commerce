<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Staff Management - Laravel Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <!-- Bootstrap & Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">


    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/staff.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
            <div class="logo-header" data-background-color="dark">
                <a href="index.html" class="logo">
                    <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
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
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <ul class="nav nav-secondary">
                    <li class="nav-item">
                        <a href="#dashboard" class="collapsed" aria-expanded="false">
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
                        <a href="/staff" class="nav-link text-white d-flex align-items-center gap-2">
                            <i class="fas fa-user-tie me-2"></i><span>Staff</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- End Sidebar -->

    <div class="main-panel">
        <div class="main-header">
            <!-- Navbar Header -->
            <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                <div class="container-fluid">
                    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                        <li class="nav-item topbar-user dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                <div class="avatar-sm">
                                    <img src="assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle" />
                                </div>
                                <span class="profile-username">
                                    <span class="op-7">Hi,</span>
                                    <span class="fw-bold">Admin</span>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="pt-5">
            <div class="row">
                <div class="col-6">Staff Management</div>
            </div>
        </div>

        <div id="app" class="mt-5 p-5">
            <!-- Header with Add Button -->
            <div class="d-flex justify-content-between mb-3">
                <h3>Staff List</h3>
                <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Staff">
                    <i class="fa-solid fa-user-plus fa-lg"></i>
                </button>
            </div>

            <!-- Enhanced Search Container -->
            <div class="">
                <div class="row justify-content-end align-items-center g-2">
                    <!-- Position Filter -->
                    <div class="col-auto">
                        <select class="form-select form-select-sm" v-model="selectedPositionFilter" @change="performSearch">
                            <option value="">All Positions</option>
                            <option v-for="position in availablePositions" :key="position" :value="position">
                                [[ position ]]
                            </option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="col-auto">
                            <input
                                type="text"
                                class="form-control "
                                placeholder="Search staff..."
                                v-model="searchQuery"
                                @input="performSearch"
                            >
                    </div>
                </div>


                <!-- Search Stats -->
                <div class="search-stats" v-if="searchActive">
                    Showing [[ displayedStaff.length ]] of [[ totalStaffCount ]] staff members
                    <span v-if="searchQuery">for "[[ searchQuery ]]"</span>
                </div>
            </div>

            <!-- Loading state -->
            <div v-if="loading" class="loading">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading staff data...</p>
            </div>

            <!-- Error message -->
            <div v-if="errorMessage" class="error-message">
                [[ errorMessage ]]
            </div>

            <!-- Staff Table -->
            <div v-if="!loading">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Position</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(staff, index) in displayedStaff" :key="staff.id">
                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;">[[ staff.id ]]</td>
                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;">
                                <img :src="staff.profile ? '/storage/' + staff.profile : 'https://via.placeholder.com/40'"
                                     alt="Profile" class="profile-img rounded-circle">
                            </td>
                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;" v-html="highlightText(staff.name)"></td>
                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;">[[ staff.gender ]]</td>
                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;" v-html="highlightText(staff.email)"></td>
                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;" v-html="highlightText(staff.phone)"></td>
                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;" v-html="highlightText(staff.position)"></td>
                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;" v-html="highlightText(staff.branches ? staff.branches.name : 'N/A')"></td>
                            <td>
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(staff)">
                                            <i class="fa-solid fa-pen me-2"></i>Edit
                                        </a>
                                    </div>
                                    <div>
                                        <a class="action-btn btn-delete" href="#" @click.prevent="deleteStaff(staff.id)">
                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <!-- No Results Message -->
                    <div v-if="displayedStaff.length === 0 && !loading" class="text-center py-4">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No staff found</h5>
                        <p class="text-muted">Try adjusting your search criteria</p>
                    </div>
                </div>

                <!-- Pagination Container -->
                <div class="pagination-container">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <!-- Page Size Selector -->
                        <div class="page-size-selector">
                            <span class="text-muted">Show</span>
                            <select class="form-select form-select-sm" style="width: auto;" v-model="pageSize" @change="changePageSize">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="text-muted">entries</span>
                        </div>

                        <!-- Pagination Info -->
                        <div class="pagination-info">
                            Showing [[ startRecord ]] to [[ endRecord ]] of [[ totalFilteredRecords ]] entries
                            <span v-if="totalFilteredRecords !== totalStaffCount">
                                (filtered from [[ totalStaffCount ]] total entries)
                            </span>
                        </div>

                        <!-- Pagination Controls -->
                        <nav aria-label="Staff pagination">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <button class="page-link" @click="goToPage(1)" :disabled="currentPage === 1">
                                        <i class="fas fa-angle-double-left"></i>
                                    </button>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <button class="page-link" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">
                                        <i class="fas fa-angle-left"></i>
                                    </button>
                                </li>

                                <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: page === currentPage }">
                                    <button class="page-link" @click="goToPage(page)">
                                        [[ page ]]
                                    </button>
                                </li>

                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <button class="page-link" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages">
                                        <i class="fas fa-angle-right"></i>
                                    </button>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <button class="page-link" @click="goToPage(totalPages)" :disabled="currentPage === totalPages">
                                        <i class="fas fa-angle-double-right"></i>
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Offcanvas -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="staffOffcanvas" aria-labelledby="staffOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="staffOffcanvasLabel">[[ isEditing ? 'Edit' : 'Add' ]] Staff</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="currentStaff">
                    <form @submit.prevent="saveStaff" enctype="multipart/form-data">

                        <div>
                            <div >
                                <!-- Upload State -->
                                <div v-if="!profileImageSrc" class="d-flex justify-content-lg-start align-content-start">
                                    <div class="w-100 mx-auto upload-area">
                                        <div
                                            class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:bg-gray-50 cursor-pointer transition-all duration-200"
                                            :class="{ 'bg-gray-100': isDragOver }"
                                            @click="triggerFileInput"
                                            @dragover.prevent="handleDragOver"
                                            @dragleave.prevent="handleDragLeave"
                                            @drop.prevent="handleDrop"
                                        >
                                            <div class="text-gray-500 text-2xl">
                                                <i class="fas fa-camera"></i>
                                            </div>
                                           <div class="fs-11px">
                                               <p class="text-sm  mb-1 fs-11px">
                                                   Simply drag and drop photos, or click to select and upload from your device.
                                                   Allowed file size: 10MB, dimensions: 930 × 492 pixels
                                                   Accepted file types: *.jpg, *.jpeg, *.png
                                               </p>
<!--                                               <p class="text-sm text-gray-500 mb-1">-->
<!--                                                   Allowed file size: <strong>10MB</strong>, dimensions: <strong>930 × 492 pixels</strong>-->
<!--                                               </p>-->
<!--                                               <p class="text-sm text-gray-500 mb-2">-->
<!--                                                   Accepted file types: <strong>*.jpg, *.jpeg, *.png</strong>-->
<!--                                               </p>-->
                                           </div>

                                            <input
                                                type="file"
                                                ref="fileInput"
                                                accept=".jpg,.jpeg,.png"
                                                @change="handleFileUpload"
                                                style="display: none;"
                                            >
                                        </div>
                                    </div>
                                </div>


                                <!-- Profile Photo State -->
                                <div v-if="profileImageSrc" class="">
                                    <div class=" text-center">
                                        <h6 class="text-muted mb-4 d-flex fw-bold">Profile Photo *</h6>
                                        <div class="profile-photo-container">
                                            <img :src="profileImageSrc" alt="Profile Photo" class="profile-photo">
                                            <button type="button" class="remove-btn" @click="removePhoto" title="Remove photo">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" v-model="currentStaff.name" required maxlength="50">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gender *</label>
                            <select class="form-select" v-model="currentStaff.gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" v-model="currentStaff.email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="text" class="form-control" v-model="currentStaff.phone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Address *</label>
                            <textarea class="form-control" v-model="currentStaff.current_address" required maxlength="100" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Position *</label>
                            <input type="text" class="form-control" v-model="currentStaff.position" required maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Salary *</label>
                            <input type="number" class="form-control" v-model="currentStaff.salary" required min="0" step="0.01">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Branch *</label>
                            <select class="form-select" v-model="currentStaff.branches_id" required>
                                <option value="">Select Branch</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    [[ branch.name ]]
                                </option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                [[ isEditing ? 'Update' : 'Add' ]]
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- View Details Offcanvas -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="viewOffcanvas" aria-labelledby="viewOffcanvasLabel">
                <div class="offcanvas-header">
                    <div class="w-100 d-flex justify-content-between align-items-start">
                        <div v-if="viewStaff">
                            <h5 class="mb-0">[[ viewStaff.name ]]</h5>
                            <small class="text-white badge bg-secondary">[[ viewStaff.position ]]</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body" v-if="viewStaff">
                    <div class="text-center mb-4">
                        <img :src="viewStaff.profile ? '/storage/' + viewStaff.profile : 'https://via.placeholder.com/100'"
                             alt="Profile Photo" class="rounded-circle shadow" width="100" height="100">
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Gender</div>
                            <div class="fw-semibold">[[ viewStaff.gender ]]</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold">[[ viewStaff.email ]]</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Phone Number</div>
                            <div class="fw-semibold">[[ viewStaff.phone ]]</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Current Address</div>
                            <div class="fw-semibold">[[ viewStaff.current_address ]]</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Position</div>
                            <div class="fw-semibold">[[ viewStaff.position ]]</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Salary</div>
                            <div class="fw-semibold">$[[ parseFloat(viewStaff.salary).toLocaleString() ]]</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Branch</div>
                            <div class="fw-semibold">[[ viewStaff.branches ? viewStaff.branches.name : 'N/A' ]]</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Created Date</div>
                            <div class="fw-semibold">[[ formatDate(viewStaff.created_at) ]]</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@3.4.21/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Ensure CSRF token is set
    if (document.querySelector('meta[name="csrf-token"]')) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    const { createApp } = Vue;

    createApp({
        delimiters: ['[[', ']]'],

        data() {
            return {
                staffList: [],
                filteredStaffList: [],
                branches: [],
                currentStaff: null,
                viewStaff: null,
                isEditing: false,
                loading: true,
                saving: false,
                errorMessage: null,
                selectedFile: null,

                // Search functionality
                searchQuery: '',
                searchFilter: 'name',
                selectedPositionFilter: '',
                searchDebounceTimer: null,

                // Pagination
                currentPage: 1,
                pageSize: 10,
                totalStaffCount: 0,
                profileImageSrc: null,
                isDragOver: false,

                // Form validation
                formErrors: {}
            };
        },

        async mounted() {
            try {
                await Promise.all([
                    this.loadStaff(),
                    this.loadBranches()
                ]);
            } catch (error) {
                console.error('Error during initialization:', error);
                this.showNotification('Failed to initialize application', 'error');
            }
        },

        computed: {
            availablePositions() {
                const positions = [...new Set(this.staffList.map(staff => staff.position))];
                return positions.filter(position => position && position.trim()).sort();
            },

            searchActive() {
                return this.searchQuery.trim() !== '' || this.selectedPositionFilter !== '';
            },

            totalFilteredRecords() {
                return this.filteredStaffList.length;
            },

            totalPages() {
                return Math.ceil(this.totalFilteredRecords / this.pageSize);
            },

            startRecord() {
                if (this.totalFilteredRecords === 0) return 0;
                return (this.currentPage - 1) * this.pageSize + 1;
            },

            endRecord() {
                const end = this.currentPage * this.pageSize;
                return Math.min(end, this.totalFilteredRecords);
            },

            displayedStaff() {
                const start = (this.currentPage - 1) * this.pageSize;
                const end = start + this.pageSize;
                return this.filteredStaffList.slice(start, end);
            },

            visiblePages() {
                const pages = [];
                const totalPages = this.totalPages;
                const currentPage = this.currentPage;

                if (totalPages <= 5) {
                    for (let i = 1; i <= totalPages; i++) {
                        pages.push(i);
                    }
                } else {
                    let startPage = Math.max(1, currentPage - 2);
                    let endPage = Math.min(totalPages, startPage + 4);

                    if (endPage - startPage < 4) {
                        startPage = Math.max(1, endPage - 4);
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        pages.push(i);
                    }
                }

                return pages;
            },

            filePreviewUrl() {
                if (this.selectedFile) {
                    return URL.createObjectURL(this.selectedFile);
                }
                return null;
            },

            isFormValid() {
                return this.currentStaff &&
                    this.currentStaff.name &&
                    this.currentStaff.gender &&
                    this.currentStaff.email &&
                    this.currentStaff.phone &&
                    this.currentStaff.current_address &&
                    this.currentStaff.position &&
                    this.currentStaff.salary &&
                    this.currentStaff.branches_id;
            }
        },

        watch: {
            filteredStaffList() {
                this.currentPage = 1;
            },

            searchQuery() {
                this.performSearch();
            },

            selectedPositionFilter() {
                this.performSearch();
            }
        },

        methods: {
            // Load staff data from API
            async loadStaff() {
                try {
                    this.loading = true;
                    this.errorMessage = null;

                    const response = await axios.get('/api/staff');
                    const data = response.data;

                    this.staffList = Array.isArray(data) ? data : (data.data || []);
                    this.totalStaffCount = this.staffList.length;
                    this.filteredStaffList = [...this.staffList];

                } catch (error) {
                    console.error('Error loading staff:', error);
                    this.errorMessage = this.getErrorMessage(error, 'Failed to load staff data');
                    this.staffList = [];
                    this.filteredStaffList = [];
                    this.showNotification(this.errorMessage, 'error');
                } finally {
                    this.loading = false;
                }
            },

            // Load branches data from API
            async loadBranches() {
                try {
                    const response = await axios.get('/api/branches');
                    const data = response.data;
                    this.branches = Array.isArray(data) ? data : (data.data || []);
                } catch (error) {
                    console.error('Error loading branches:', error);
                    this.branches = [];
                    this.showNotification('Failed to load branches', 'error');
                }
            },

            // Search functionality with debouncing
            performSearch() {
                if (this.searchDebounceTimer) {
                    clearTimeout(this.searchDebounceTimer);
                }

                this.searchDebounceTimer = setTimeout(() => {
                    this.executeSearch();
                }, 300);
            },

            executeSearch() {
                let filtered = [...this.staffList];

                // Apply text search
                if (this.searchQuery.trim()) {
                    const query = this.searchQuery.toLowerCase().trim();
                    filtered = filtered.filter(staff => {
                        if (!staff) return false;

                        switch (this.searchFilter) {
                            case 'name':
                                return staff.name && staff.name.toLowerCase().includes(query);
                            case 'email':
                                return staff.email && staff.email.toLowerCase().includes(query);
                            case 'position':
                                return staff.position && staff.position.toLowerCase().includes(query);
                            case 'phone':
                                return staff.phone && staff.phone.toString().includes(query);
                            case 'branch':
                                return staff.branches && staff.branches.name &&
                                    staff.branches.name.toLowerCase().includes(query);
                            default: // 'all'
                                return (staff.name && staff.name.toLowerCase().includes(query)) ||
                                    (staff.email && staff.email.toLowerCase().includes(query)) ||
                                    (staff.position && staff.position.toLowerCase().includes(query)) ||
                                    (staff.phone && staff.phone.toString().includes(query)) ||
                                    (staff.branches && staff.branches.name &&
                                        staff.branches.name.toLowerCase().includes(query));
                        }
                    });
                }

                // Apply position filter
                if (this.selectedPositionFilter) {
                    filtered = filtered.filter(staff =>
                        staff && staff.position === this.selectedPositionFilter
                    );
                }

                this.filteredStaffList = filtered;
            },

            clearSearch() {
                this.searchQuery = '';
                this.searchFilter = 'name';
                this.selectedPositionFilter = '';
                this.filteredStaffList = [...this.staffList];
            },

            highlightText(text) {
                if (!this.searchQuery.trim() || !text) return text;

                const query = this.searchQuery.trim();
                const regex = new RegExp(`(${this.escapeRegExp(query)})`, 'gi');
                return text.replace(regex, '<mark>$1</mark>');
            },

            escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            },

            // Pagination methods
            goToPage(page) {
                if (page >= 1 && page <= this.totalPages) {
                    this.currentPage = page;
                }
            },

            changePageSize() {
                this.currentPage = 1;
            },

            // Modal methods
            openAddModal() {
                this.isEditing = false;
                this.currentStaff = {
                    name: '',
                    gender: '',
                    email: '',
                    phone: '',
                    current_address: '',
                    position: '',
                    salary: '',
                    branches_id: ''
                };
                this.selectedFile = null;
                this.profileImageSrc = null;
                this.formErrors = {};

                this.showOffcanvas('staffOffcanvas');
            },

            openEditModal(staff) {
                if (!staff) return;

                this.isEditing = true;
                this.currentStaff = {
                    id: staff.id,
                    name: staff.name || '',
                    gender: staff.gender || '',
                    email: staff.email || '',
                    phone: staff.phone || '',
                    current_address: staff.current_address || '',
                    position: staff.position || '',
                    salary: staff.salary || '',
                    branches_id: staff.branches_id || '',
                    profile: staff.profile || null
                };

                this.selectedFile = null;
                this.profileImageSrc = staff.profile ? `/storage/${staff.profile}` : null;
                this.formErrors = {};

                this.showOffcanvas('staffOffcanvas');
            },

            viewStaffDetails(staff) {
                if (!staff) return;

                this.viewStaff = staff;
                this.showOffcanvas('viewOffcanvas');
            },

            // File handling methods
            handleFileUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.processFile(file);
                }
            },

            processFile(file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    this.showNotification('Please select a valid image file (JPG, JPEG, or PNG)', 'error');
                    return;
                }

                // Validate file size (2MB limit)
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    this.showNotification('File size must be less than 2MB', 'error');
                    return;
                }

                this.selectedFile = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.profileImageSrc = e.target.result;
                };
                reader.onerror = () => {
                    this.showNotification('Error reading file', 'error');
                };
                reader.readAsDataURL(file);
            },

            clearSelectedFile() {
                this.selectedFile = null;
                this.profileImageSrc = this.isEditing && this.currentStaff.profile ?
                    `/storage/${this.currentStaff.profile}` : null;

                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            },

            triggerFileInput() {
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.click();
                }
            },

            removePhoto() {
                this.profileImageSrc = null;
                this.selectedFile = null;
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            },

            // Drag and drop handlers
            handleDragOver(e) {
                e.preventDefault();
                this.isDragOver = true;
            },

            handleDragLeave(e) {
                e.preventDefault();
                this.isDragOver = false;
            },

            handleDrop(e) {
                e.preventDefault();
                this.isDragOver = false;

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    this.processFile(files[0]);
                }
            },

            // Form validation
            validateForm() {
                this.formErrors = {};

                if (!this.currentStaff.name || this.currentStaff.name.trim().length === 0) {
                    this.formErrors.name = 'Name is required';
                } else if (this.currentStaff.name.length > 50) {
                    this.formErrors.name = 'Name cannot exceed 50 characters';
                }

                if (!this.currentStaff.gender) {
                    this.formErrors.gender = 'Gender is required';
                }

                if (!this.currentStaff.email) {
                    this.formErrors.email = 'Email is required';
                } else if (!this.isValidEmail(this.currentStaff.email)) {
                    this.formErrors.email = 'Please enter a valid email address';
                }

                if (!this.currentStaff.phone) {
                    this.formErrors.phone = 'Phone is required';
                }

                if (!this.currentStaff.current_address || this.currentStaff.current_address.trim().length === 0) {
                    this.formErrors.current_address = 'Address is required';
                } else if (this.currentStaff.current_address.length > 100) {
                    this.formErrors.current_address = 'Address cannot exceed 100 characters';
                }

                if (!this.currentStaff.position || this.currentStaff.position.trim().length === 0) {
                    this.formErrors.position = 'Position is required';
                } else if (this.currentStaff.position.length > 100) {
                    this.formErrors.position = 'Position cannot exceed 100 characters';
                }

                if (!this.currentStaff.salary || this.currentStaff.salary <= 0) {
                    this.formErrors.salary = 'Valid salary is required';
                }

                if (!this.currentStaff.branches_id) {
                    this.formErrors.branches_id = 'Branch selection is required';
                }

                return Object.keys(this.formErrors).length === 0;
            },

            isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },

            // Save staff (Add/Edit)
            async saveStaff() {
                if (!this.validateForm()) {
                    this.showNotification('Please fix all form errors before submitting', 'error');
                    return;
                }

                try {
                    this.saving = true;

                    const formData = new FormData();

                    // Append all staff data
                    Object.keys(this.currentStaff).forEach(key => {
                        const value = this.currentStaff[key];
                        if (value !== null && value !== '' && value !== undefined) {
                            formData.append(key, value);
                        }
                    });

                    // Handle file upload
                    if (this.selectedFile) {
                        formData.append('profile', this.selectedFile);
                    }

                    let response;
                    const config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        }
                    };

                    if (this.isEditing) {
                        formData.append('_method', 'PUT');
                        response = await axios.post(`/api/staff/${this.currentStaff.id}`, formData, config);
                    } else {
                        response = await axios.post('/api/staff', formData, config);
                    }

                    // Close modal
                    this.hideOffcanvas('staffOffcanvas');

                    // Show success message
                    this.showNotification(
                        this.isEditing ? 'Staff updated successfully!' : 'Staff added successfully!',
                        'success'
                    );

                    // Reset form and reload data
                    this.resetForm();
                    await this.loadStaff();

                } catch (error) {
                    console.error('Save staff error:', error);
                    const errorMessage = this.getErrorMessage(error, 'Failed to save staff');
                    this.showNotification(errorMessage, 'error');
                } finally {
                    this.saving = false;
                }
            },

            // Reset form
            resetForm() {
                this.currentStaff = {
                    name: '',
                    gender: '',
                    email: '',
                    phone: '',
                    current_address: '',
                    position: '',
                    salary: '',
                    branches_id: ''
                };
                this.selectedFile = null;
                this.profileImageSrc = null;
                this.isEditing = false;
                this.formErrors = {};

                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            },

            // Delete staff
            async deleteStaff(staffId) {
                if (!staffId) return;

                try {
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    });

                    if (!result.isConfirmed) return;

                    await axios.delete(`/api/staff/${staffId}`);
                    await this.loadStaff();

                    this.showNotification('Staff member deleted successfully!', 'success');

                } catch (error) {
                    console.error('Error deleting staff:', error);
                    this.showNotification('Failed to delete staff member', 'error');
                }
            },

            // Utility methods
            formatDate(dateString) {
                if (!dateString) return 'N/A';

                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return 'N/A';

                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                } catch (error) {
                    console.error('Error formatting date:', error);
                    return 'N/A';
                }
            },

            formatCurrency(amount) {
                if (!amount) return '$0.00';

                try {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(amount);
                } catch (error) {
                    return `$${amount}`;
                }
            },

            getErrorMessage(error, defaultMessage = 'An error occurred') {
                if (error.response && error.response.data) {
                    if (error.response.data.errors) {
                        const errors = Object.values(error.response.data.errors).flat();
                        return errors.join(', ');
                    } else if (error.response.data.message) {
                        return error.response.data.message;
                    }
                }
                return defaultMessage;
            },

            showNotification(message, type = 'info') {
                const config = {
                    text: message,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                };

                switch (type) {
                    case 'success':
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            confirmButtonColor: '#198754',
                            ...config
                        });
                        break;
                    case 'error':
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            confirmButtonColor: '#dc3545',
                            timer: null,
                            showConfirmButton: true,
                            timerProgressBar: false,
                            text: message
                        });
                        break;
                    case 'warning':
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            confirmButtonColor: '#ffc107',
                            text: message
                        });
                        break;
                    default:
                        Swal.fire({
                            icon: 'info',
                            title: 'Info',
                            confirmButtonColor: '#0dcaf0',
                            text: message
                        });
                        break;
                }
            },

            // Bootstrap offcanvas helpers
            showOffcanvas(elementId) {
                try {
                    const element = document.getElementById(elementId);
                    if (element) {
                        const offcanvas = new bootstrap.Offcanvas(element);
                        offcanvas.show();
                    }
                } catch (error) {
                    console.error('Error showing offcanvas:', error);
                }
            },

            hideOffcanvas(elementId) {
                try {
                    const element = document.getElementById(elementId);
                    if (element) {
                        const offcanvas = bootstrap.Offcanvas.getInstance(element);
                        if (offcanvas) {
                            offcanvas.hide();
                        }
                    }
                } catch (error) {
                    console.error('Error hiding offcanvas:', error);
                }
            },

            // Refresh data
            async refreshData() {
                try {
                    await Promise.all([
                        this.loadStaff(),
                        this.loadBranches()
                    ]);
                    this.showNotification('Data refreshed successfully!', 'success');
                } catch (error) {
                    this.showNotification('Failed to refresh data', 'error');
                }
            }
        },

        // Cleanup
        beforeUnmount() {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }
        }
    }).mount('#app');
</script>
