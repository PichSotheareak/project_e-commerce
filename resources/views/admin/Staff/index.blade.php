@php
$staff = $staff ?? null;
$profileImage = ($staff && $staff->profile && $staff->profile->image)
? asset('storage/' . $staff->profile->image)
: asset('assets/img/default-profile.jpg');
@endphp

@extends('admin.master')
@section('content')
    <div id="app">
        <div class="page-inner">
            <div  class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
                <div>
                    <h3 class="fw-bold mb-3">Staff List</h3>
                </div>
                <div>
                    <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Staff">
                        <i class="fa-solid fa-user-plus fa-lg"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-round">
                        <div class="card-header">
                            <div class="card-head-row">
<!--                                <div class="card-title">Staff Members</div>-->
                                <div class="card-tools">
                                    <div class="row justify-content-end align-items-center g-2">
                                        <div class="col-auto">
                                            <select class="form-select form-select-sm" v-model="selectedPositionFilter" @change="performSearch">
                                                <option value="">All Positions</option>
                                                <option v-for="position in availablePositions" :key="position" :value="position">
                                                    @{{ position }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <input type="text" class="form-control" placeholder="Search staff..."
                                                   v-model="searchQuery" @input="performSearch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-stats mt-2" v-if="searchActive">
                                Showing @{{ displayedStaff.length }} of @{{ totalStaffCount }} staff members
                                <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading staff data...</p>
                            </div>

                            <div v-if="errorMessage" class="alert alert-danger" role="alert">
                                @{{ errorMessage }}
                            </div>

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
                                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;">@{{ index + 1 }}</td>
                                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;">
                                                <img
                                                    src="{{ $profileImage }}"
                                                    alt="{{ $staff->name ?? 'Staff' }} Profile"
                                                    class="avatar-img rounded-circle"
                                                />
                                            </td>
                                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;" v-html="highlightText(staff.name)"></td>
                                            <td @click="viewStaffDetails(staff)" style="cursor: pointer;">@{{ staff.gender }}</td>
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

                                    <div v-if="displayedStaff.length === 0 && !loading" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No staff found</h5>
                                        <p class="text-muted">Try adjusting your search criteria</p>
                                    </div>
                                </div>

                                <div class="pagination-container card-footer py-3">
                                    <div class="d-flex justify-content-between align-items-center w-100">
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
                                        <div class="pagination-info">
                                            Showing @{{ startRecord }} to @{{ endRecord }} of @{{ totalFilteredRecords }} entries
                                            <span v-if="totalFilteredRecords !== totalStaffCount">
                                                (filtered from @{{ totalStaffCount }} total entries)
                                            </span>
                                        </div>
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
                                                        @{{ page }}
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Offcanvas -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="staffOffcanvas" aria-labelledby="staffOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="staffOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Staff</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="currentStaff">
                    <form @submit.prevent="saveStaff" enctype="multipart/form-data">
                        <div>
                            <div v-if="!profileImageSrc" class="d-flex justify-content-lg-start align-content-start">
                                <div class="w-100 mx-auto upload-area">
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:bg-gray-50 cursor-pointer transition-all duration-200"
                                         :class="{ 'bg-gray-100': isDragOver }"
                                         @click="triggerFileInput"
                                         @dragover.prevent="handleDragOver"
                                         @dragleave.prevent="handleDragLeave"
                                         @drop.prevent="handleDrop">
                                        <div class="text-gray-500 text-2xl">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-1">Drag and drop or click to upload</p>
                                        <p class="text-sm text-gray-500 mb-2">Max 2MB, JPG/JPEG/PNG</p>
                                        <input type="file" ref="fileInput" accept=".jpg,.jpeg,.png" @change="handleFileUpload" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            <div v-if="profileImageSrc" class="text-center">
                                <h6 class="text-muted mb-4 d-flex fw-bold">Profile Photo *</h6>
                                <div class="profile-photo-container">
                                    <img :src="profileImageSrc" alt="Profile Photo" class="profile-photo">
                                    <button type="button" class="remove-btn" @click="removePhoto" title="Remove photo">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" v-model="currentStaff.name" required maxlength="50">
                                <span v-if="formErrors.name" class="text-danger small">@{{ formErrors.name }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gender *</label>
                                <select class="form-select" v-model="currentStaff.gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <span v-if="formErrors.gender" class="text-danger small">@{{ formErrors.gender }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" v-model="currentStaff.email" required>
                                <span v-if="formErrors.email" class="text-danger small">@{{ formErrors.email }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="text" class="form-control" v-model="currentStaff.phone" required>
                                <span v-if="formErrors.phone" class="text-danger small">@{{ formErrors.phone }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Current Address *</label>
                                <textarea class="form-control" v-model="currentStaff.current_address" required maxlength="100" rows="3"></textarea>
                                <span v-if="formErrors.current_address" class="text-danger small">@{{ formErrors.current_address }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Position *</label>
                                <input type="text" class="form-control" v-model="currentStaff.position" required maxlength="100">
                                <span v-if="formErrors.position" class="text-danger small">@{{ formErrors.position }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Salary *</label>
                                <input type="number" class="form-control" v-model="currentStaff.salary" required min="0" step="0.01">
                                <span v-if="formErrors.salary" class="text-danger small">@{{ formErrors.salary }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Branch *</label>
                                <select class="form-select" v-model="currentStaff.branches_id" required>
                                    <option value="">Select Branch</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        @{{ branch.name }}
                                    </option>
                                </select>
                                <span v-if="formErrors.branches_id" class="text-danger small">@{{ formErrors.branches_id }}</span>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancel</button>
                                <button type="submit" class="btn btn-primary" :disabled="saving">
                                    <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    @{{ isEditing ? 'Update' : 'Add' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- View Details Offcanvas -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="viewOffcanvas" aria-labelledby="viewOffcanvasLabel">
                <div class="offcanvas-header">
                    <div class="w-100 d-flex justify-content-between align-items-start">
                        <div v-if="viewStaff">
                            <h5 class="mb-0">@{{ viewStaff.name }}</h5>
                            <small class="text-white badge bg-secondary">@{{ viewStaff.position }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="viewStaff">
                    <div class="text-center mb-4">
                        <img :src="viewStaff.profile ? '/storage/' + viewStaff.profile : 'https://via.placeholder.com/100'"
                             alt="Profile Photo" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover">
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Gender</div>
                            <div class="fw-semibold">@{{ viewStaff.gender }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold">@{{ viewStaff.email }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Phone Number</div>
                            <div class="fw-semibold">@{{ viewStaff.phone }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Current Address</div>
                            <div class="fw-semibold">@{{ viewStaff.current_address }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Position</div>
                            <div class="fw-semibold">@{{ viewStaff.position }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Salary</div>
                            <div class="fw-semibold">@{{ parseFloat(viewStaff.salary).toLocaleString() }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Branch</div>
                            <div class="fw-semibold">@{{ viewStaff.branches ? viewStaff.branches.name : 'N/A' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Created Date</div>
                            <div class="fw-semibold">@{{ formatDate(viewStaff.created_at) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Setup CSRF
        if (document.querySelector('meta[name="csrf-token"]')) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        // Setup Axios globally with Authorization Token
        axios.interceptors.request.use(config => {
            const token = localStorage.getItem('token') ?? '';
            if (token) {
                config.headers['Authorization'] = `Bearer ${token}`;
            }
            return config;
        }, error => {
            return Promise.reject(error);
        });

        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    staffList: [],
                    //api_url: 'https://su8.beynak.us',
                    api_url:'http://127.0.0.1:8000',
                    filteredStaffList: [],
                    branches: [],
                    currentStaff: null,
                    viewStaff: null,
                    isEditing: false,
                    loading: true,
                    saving: false,
                    errorMessage: null,
                    selectedFile: null,
                    profileImageSrc: null,
                    isDragOver: false,
                    searchQuery: '',
                    selectedPositionFilter: '',
                    searchDebounceTimer: null,
                    currentPage: 1,
                    pageSize: 5,
                    totalStaffCount: 0,
                    formErrors: {},
                    removeProfile: false // New flag to track photo removal
                };
            },
            async mounted() {
                console.log('Mounting Vue app...');
                try {
                    await Promise.all([this.loadStaff(), this.loadBranches()]);
                    console.log('Staff List:', this.staffList);
                } catch (error) {
                    console.error('Error during initialization:', error);
                    this.errorMessage = 'Failed to initialize application. Details: ' + (error.message || 'Unknown error');
                }
            },
            computed: {
                availablePositions() {
                    return [...new Set(this.staffList.map(staff => staff.position))].filter(p => p && p.trim()).sort();
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
                    return this.totalFilteredRecords === 0 ? 0 : (this.currentPage - 1) * this.pageSize + 1;
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
                        for (let i = 1; i <= totalPages; i++) pages.push(i);
                    } else {
                        let startPage = Math.max(1, currentPage - 2);
                        let endPage = Math.min(totalPages, startPage + 4);
                        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);
                        for (let i = startPage; i <= endPage; i++) pages.push(i);
                    }
                    return pages;
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
                async loadStaff() {
                    try {
                        this.loading = true;
                        this.errorMessage = null;
                        console.log('Fetching staff data...');
                        const response = await axios.get(`${this.api_url}/api/staff`);
                        this.staffList = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        this.totalStaffCount = this.staffList.length;
                        this.filteredStaffList = [...this.staffList];
                    } catch (error) {
                        console.error('Error loading staff:', error.response ? error.response.data : error.message);
                        this.errorMessage = this.getErrorMessage(error, 'Failed to load staff data');
                        this.staffList = [];
                        this.filteredStaffList = [];
                    } finally {
                        this.loading = false;
                    }
                },
                async loadBranches() {
                    try {
                        const response = await axios.get(`${this.api_url}/api/branches`);
                        this.branches = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        console.log('Branches:', this.branches);
                    } catch (error) {
                        console.error('Error loading branches:', error);
                        this.branches = [];
                    }
                },
                performSearch() {
                    if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                    this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
                },
                executeSearch() {
                    let filtered = [...this.staffList];
                    if (this.searchQuery.trim()) {
                        const query = this.searchQuery.toLowerCase().trim();
                        filtered = filtered.filter(staff =>
                            (staff.name && staff.name.toLowerCase().includes(query)) ||
                            (staff.email && staff.email.toLowerCase().includes(query)) ||
                            (staff.position && staff.position.toLowerCase().includes(query)) ||
                            (staff.phone && staff.phone.toString().includes(query)) ||
                            (staff.branches && staff.branches.name && staff.branches.name.toLowerCase().includes(query))
                        );
                    }
                    if (this.selectedPositionFilter) {
                        filtered = filtered.filter(staff => staff.position?.toLowerCase() === this.selectedPositionFilter.toLowerCase());
                    }

                    this.filteredStaffList = filtered;
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
                goToPage(page) {
                    if (page >= 1 && page <= this.totalPages) this.currentPage = page;
                },
                changePageSize() {
                    this.currentPage = 1;
                },
                openAddModal() {
                    this.isEditing = false;
                    this.currentStaff = { name: '', gender: '', email: '', phone: '', current_address: '', position: '', salary: '', branches_id: '' };
                    this.selectedFile = null;
                    this.profileImageSrc = null;
                    this.removeProfile = false;
                    this.formErrors = {};
                    this.showOffcanvas('staffOffcanvas');
                },
                openEditModal(staff) {
                    this.isEditing = true;
                    this.currentStaff = { ...staff, branches_id: staff.branches_id || '' };
                    this.selectedFile = null;
                    this.profileImageSrc = staff.profile ? `${this.api_url}/storage/${staff.profile}` : null;
                    this.removeProfile = false;
                    this.formErrors = {};
                    console.log('Editing Staff:', this.currentStaff);
                    this.showOffcanvas('staffOffcanvas');
                },
                viewStaffDetails(staff) {
                    this.viewStaff = { ...staff };
                    this.showOffcanvas('viewOffcanvas');
                },
                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const maxSize = 2 * 1024 * 1024;
                        console.log('Selected File:', {
                            name: file.name,
                            size: file.size,
                            type: file.type
                        });
                        if (file.size > maxSize) {
                            this.errorMessage = 'File size must be less than 2MB';
                            return;
                        }
                        if (!['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml'].includes(file.type)) {
                            this.errorMessage = 'Please select a valid image file (JPG, JPEG, PNG, GIF, or SVG)';
                            return;
                        }
                        this.selectedFile = file;
                        this.profileImageSrc = URL.createObjectURL(file);
                        this.removeProfile = false; // Reset remove flag when new file is uploaded
                    }
                },
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
                    const file = e.dataTransfer.files[0];
                    if (file) this.handleFileUpload({ target: { files: [file] } });
                },
                removePhoto() {
                    this.profileImageSrc = null;
                    this.selectedFile = null;
                    this.removeProfile = true; // Set flag to indicate removal
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                triggerFileInput() {
                    this.$refs.fileInput.click();
                },
                validateForm() {
                    this.formErrors = {};
                    if (!this.currentStaff.name || this.currentStaff.name.trim().length === 0) this.formErrors.name = 'Name is required';
                    if (!this.currentStaff.gender) this.formErrors.gender = 'Gender is required';
                    if (!this.currentStaff.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.currentStaff.email)) this.formErrors.email = 'Valid email is required';
                    if (!this.currentStaff.phone) this.formErrors.phone = 'Phone is required';
                    if (!this.currentStaff.current_address || this.currentStaff.current_address.trim().length === 0) this.formErrors.current_address = 'Address is required';
                    if (!this.currentStaff.position || this.currentStaff.position.trim().length === 0) this.formErrors.position = 'Position is required';
                    if (!this.currentStaff.salary || this.currentStaff.salary <= 0) this.formErrors.salary = 'Valid salary is required';
                    if (!this.currentStaff.branches_id) this.formErrors.branches_id = 'Branch is required';
                    console.log('Validation Errors:', this.formErrors);
                    return Object.keys(this.formErrors).length === 0;
                },
                async saveStaff() {
                    if (!this.validateForm()) {
                        console.log('Validation failed:', this.formErrors);
                        return;
                    }
                    try {
                        this.saving = true;
                        const formData = new FormData();

                        Object.keys(this.currentStaff).forEach(key => {
                            if (key !== 'profile' && key !== 'branches') {
                                formData.append(key, this.currentStaff[key]);
                            }
                        });

                        if (this.selectedFile) {
                            formData.append('profile', this.selectedFile);
                        }
                        if (this.removeProfile) {
                            formData.append('remove_profile', '1');
                        }

                        let response;
                        if (this.isEditing) {
                            formData.append('_method', 'PUT');
                            response = await axios.post(`${this.api_url}/api/staff/${this.currentStaff.id}`, formData, {
                                headers: { 'Content-Type': 'multipart/form-data' }
                            });
                        } else {
                            response = await axios.post(`${this.api_url}/api/staff`, formData, {
                                headers: { 'Content-Type': 'multipart/form-data' }
                            });
                        }

                        this.hideOffcanvas('staffOffcanvas');
                        Swal.fire({
                            icon: 'success',
                            title: this.isEditing ? 'Staff updated successfully!' : 'Staff added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        this.resetForm();
                        await this.loadStaff();
                    } catch (error) {
                        console.error('Save staff error:', error);
                        const errors = error.response?.data?.errors;
                        if (errors) {
                            this.formErrors = errors;
                            this.errorMessage = 'Please fix the errors in the form';
                        } else {
                            this.errorMessage = error.response?.data?.message || 'Failed to save staff';
                        }
                    } finally {
                        this.saving = false;
                    }
                }
                ,
                resetForm() {
                    this.currentStaff = { name: '', gender: '', email: '', phone: '', current_address: '', position: '', salary: '', branches_id: '' };
                    this.selectedFile = null;
                    this.profileImageSrc = null;
                    this.removeProfile = false;
                    this.isEditing = false;
                    this.formErrors = {};
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                async deleteStaff(staffId) {
                    if (!staffId) return;

                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    });

                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/staff/${staffId}`);
                            await this.loadStaff();

                            await Swal.fire(
                                'Deleted!',
                                'Staff member has been deleted.',
                                'success'
                            );

                        } catch (error) {
                            console.error('Error deleting staff:', error);
                            this.errorMessage = 'Failed to delete staff member';

                            await Swal.fire(
                                'Error!',
                                'Failed to delete staff member.',
                                'error'
                            );
                        }
                    }
                }
                ,
                formatDate(dateString) {
                    return dateString ? new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
                },
                showOffcanvas(elementId) {
                    const element = document.getElementById(elementId);
                    if (element) new bootstrap.Offcanvas(element).show();
                },
                hideOffcanvas(elementId) {
                    const element = document.getElementById(elementId);
                    if (element) bootstrap.Offcanvas.getInstance(element)?.hide();
                },
                getErrorMessage(error, defaultMessage = 'An error occurred') {
                    if (error.response && error.response.data) {
                        if (error.response.data.errors) {
                            return Object.values(error.response.data.errors).flat().join(', ');
                        } else if (error.response.data.message) {
                            return error.response.data.message;
                        }
                    }
                    return defaultMessage;
                }
            },
            beforeUnmount() {
                if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
            }
        }).mount('#app');
    </script>
@endsection
