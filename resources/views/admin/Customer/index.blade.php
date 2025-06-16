@extends('admin.master')
@section('content')
    <div id="app">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
                <div>
                    <h3 class="fw-bold mb-3">Customer List</h3>
                </div>
                <div>
                    <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Customer">
                        <i class="fa-solid fa-user-plus fa-lg"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-round">
                        <div class="card-header">
                            <div class="card-head-row">
                                <div class="card-title">All Customers</div>
                                <div class="card-tools">
                                    <div class="row justify-content-end align-items-center g-2">
                                        <div class="col-auto">
                                            <input type="text" class="form-control" placeholder="Search customers..."
                                                   v-model="searchQuery" @input="performSearch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-stats mt-2" v-if="searchActive">
                                Showing @{{ displayedCustomers.length }} of @{{ totalCustomerCount }} customers
                                <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading customer data...</p>
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
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(customer, index) in displayedCustomers" :key="customer.id">
                                            <td @click="viewCustomerDetails(customer)" style="cursor: pointer;">@{{ index + 1 }}</td>
                                            <td @click="viewCustomerDetails(customer)" style="cursor: pointer;">
                                                <img v-if="customer.image" :src="api_url + '/storage/' + customer.image"
                                                     alt="Customer Image" class="profile-img rounded-circle" style="width: 40px; height: 40px;">
                                                <span v-else>-</span>
                                            </td>
                                            <td @click="viewCustomerDetails(customer)" style="cursor: pointer;" v-html="highlightText(customer.name)"></td>
                                            <td @click="viewCustomerDetails(customer)" style="cursor: pointer;">@{{ customer.gender || '-' }}</td>
                                            <td @click="viewCustomerDetails(customer)" style="cursor: pointer;" v-html="highlightText(customer.email || '-')"></td>
                                            <td @click="viewCustomerDetails(customer)" style="cursor: pointer;" v-html="highlightText(customer.phone || '-')"></td>
                                            <td @click="viewCustomerDetails(customer)" style="cursor: pointer;" v-html="highlightText(customer.address || '-')"></td>
                                            <td @click="viewCustomerDetails(customer)" style="cursor: pointer;">@{{ formatDate(customer.created_at) }}</td>
                                            <td @click="viewCustomerDetails(customer)" style="cursor: pointer;">
                                                <span :class="{'text-danger': customer.deleted_at, 'text-success': !customer.deleted_at}">
                                                    @{{ customer.deleted_at ? 'Deleted' : 'Active' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="me-3" v-if="!customer.deleted_at">
                                                        <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(customer)">
                                                            <i class="fa-solid fa-pen me-2"></i>Edit
                                                        </a>
                                                    </div>
                                                    <div v-if="!customer.deleted_at">
                                                        <a class="action-btn btn-delete" href="#" @click.prevent="deleteCustomer(customer.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                                        </a>
                                                    </div>
                                                    <div v-if="customer.deleted_at">
                                                        <a class="action-btn btn-restore" href="#" @click.prevent="restoreCustomer(customer.id)">
                                                            <i class="fa-solid fa-trash-restore me-2"></i>Restore
                                                        </a>
                                                    </div>
                                                    <div v-if="customer.deleted_at" class="ms-3">
                                                        <a class="action-btn btn-danger" href="#" @click.prevent="forceDeleteCustomer(customer.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Permanent Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div v-if="displayedCustomers.length === 0 && !loading" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No customers found</h5>
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
                                            <span v-if="totalFilteredRecords !== totalCustomerCount">
                                                (filtered from @{{ totalCustomerCount }} total entries)
                                            </span>
                                        </div>
                                        <nav aria-label="Customer pagination">
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="customerOffcanvas" aria-labelledby="customerOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="customerOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Customer</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="currentCustomer">
                    <form @submit.prevent="saveCustomer" enctype="multipart/form-data">
                        <div>
                            <div v-if="!imageSrc" class="d-flex justify-content-lg-start align-content-start">
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
                            <div v-if="imageSrc" class="text-center">
                                <h6 class="text-muted mb-4 d-flex fw-bold">Customer Image</h6>
                                <div class="image-container">
                                    <img :src="imageSrc" alt="Customer Image" class="profile-photo">
                                    <button type="button" class="remove-btn" @click="removeImage" title="Remove image">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" v-model="currentCustomer.name" required maxlength="255">
                                <span v-if="formErrors.name" class="text-danger small">@{{ formErrors.name }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gender *</label>
                                <select class="form-select" v-model="currentCustomer.gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <span v-if="formErrors.gender" class="text-danger small">@{{ formErrors.gender }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" v-model="currentCustomer.email" required maxlength="255">
                                <span v-if="formErrors.email" class="text-danger small">@{{ formErrors.email }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="text" class="form-control" v-model="currentCustomer.phone" required maxlength="255" pattern="\+?[0-9\s\-\(\)]*">
                                <span v-if="formErrors.phone" class="text-danger small">@{{ formErrors.phone }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address *</label>
                                <textarea class="form-control" v-model="currentCustomer.address" required maxlength="255" rows="3"></textarea>
                                <span v-if="formErrors.address" class="text-danger small">@{{ formErrors.address }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password @{{ isEditing ? '(Leave blank to keep unchanged)' : '*' }}</label>
                                <input type="password" class="form-control" v-model="currentCustomer.password" :required="!isEditing" maxlength="255">
                                <span v-if="formErrors.password" class="text-danger small">@{{ formErrors.password }}</span>
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="viewCustomerOffcanvas" aria-labelledby="viewCustomerOffcanvasLabel">
                <div class="offcanvas-header">
                    <div class="w-100 d-flex justify-content-between align-items-start">
                        <div v-if="viewCustomer">
                            <h5 class="mb-0">@{{ viewCustomer.name }}</h5>
                            <small class="text-white badge bg-secondary" v-if="viewCustomer.deleted_at">Deleted</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="viewCustomer">
                    <div class="text-center mb-4">
                        <img :src="viewCustomer.image ? api_url + '/storage/' + viewCustomer.image : 'https://via.placeholder.com/100'"
                             alt="Customer Image" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover">
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">ID</div>
                            <div class="fw-semibold">@{{ viewCustomer.id }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Name</div>
                            <div class="fw-semibold">@{{ viewCustomer.name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Gender</div>
                            <div class="fw-semibold">@{{ viewCustomer.gender || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold">@{{ viewCustomer.email || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Phone</div>
                            <div class="fw-semibold">@{{ viewCustomer.phone || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Address</div>
                            <div class="fw-semibold">@{{ viewCustomer.address || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Created At</div>
                            <div class="fw-semibold">@{{ formatDate(viewCustomer.created_at) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Updated At</div>
                            <div class="fw-semibold">@{{ formatDate(viewCustomer.updated_at) }}</div>
                        </div>
                        <div class="mb-3" v-if="viewCustomer.deleted_at">
                            <div class="text-muted small">Deleted At</div>
                            <div class="fw-semibold">@{{ formatDate(viewCustomer.deleted_at) }}</div>
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
        // Ensure CSRF token is set
        if (document.querySelector('meta[name="csrf-token"]')) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        // Setup Authorization token globally for Axios
        const token = localStorage.getItem('token') ?? '';
        console.log('Token:', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    customerList: [],
                    api_url: 'http://127.0.0.1:8000',
                    filteredCustomerList: [],
                    currentCustomer: null,
                    viewCustomer: null,
                    isEditing: false,
                    loading: true,
                    saving: false,
                    errorMessage: null,
                    selectedFile: null,
                    imageSrc: null,
                    isDragOver: false,
                    searchQuery: '',
                    searchDebounceTimer: null,
                    currentPage: 1,
                    pageSize: 5,
                    totalCustomerCount: 0,
                    formErrors: {},
                    showDeleted: false,
                    removeImage: false
                };
            },
            async mounted() {
                console.log('Mounting Vue.js app...');
                try {
                    await this.loadCustomers();
                    console.log('Customer List:', this.customerList);
                } catch (error) {
                    console.error('Error during initialization:', error);
                    this.errorMessage = 'Failed to initialize application. Details: ' + (error.message || 'Unknown error');
                }
            },
            computed: {
                searchActive() {
                    return this.searchQuery.trim() !== '';
                },
                totalFilteredRecords() {
                    return this.filteredCustomerList.length;
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
                displayedCustomers() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    const end = start + this.pageSize;
                    return this.filteredCustomerList.slice(start, end);
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
                filteredCustomerList() {
                    this.currentPage = 1;
                },
                searchQuery() {
                    this.performSearch();
                },
                showDeleted() {
                    this.loadCustomers();
                }
            },
            methods: {
                async loadCustomers() {
                    try {
                        this.loading = true;
                        this.errorMessage = null;
                        console.log('Fetching customer data with showDeleted:', this.showDeleted);
                        const response = await axios.get(`${this.api_url}/api/customers`, {
                            params: { with_deleted: this.showDeleted ? 1 : 0 }
                        });
                        console.log('API Response:', response.data);
                        this.customerList = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        this.totalCustomerCount = this.customerList.length;
                        this.filteredCustomerList = [...this.customerList];
                        this.executeSearch();
                        console.log('After loadCustomers - customerList:', this.customerList, 'totalCustomerCount:', this.totalCustomerCount);
                    } catch (error) {
                        console.error('Error loading customers:', error.response ? error.response.data : error.message);
                        this.errorMessage = this.getErrorMessage(error, 'Failed to load customer data');
                        this.customerList = [];
                        this.filteredCustomerList = [];
                    } finally {
                        this.loading = false;
                    }
                },
                performSearch() {
                    if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                    this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
                },
                executeSearch() {
                    let filtered = [...this.customerList];
                    if (this.searchQuery.trim()) {
                        const query = this.searchQuery.toLowerCase().trim();
                        filtered = filtered.filter(customer =>
                            (customer.name && customer.name.toLowerCase().includes(query)) ||
                            (customer.email && customer.email.toLowerCase().includes(query)) ||
                            (customer.phone && customer.phone.toLowerCase().includes(query)) ||
                            (customer.address && customer.address.toLowerCase().includes(query))
                        );
                    }
                    this.filteredCustomerList = filtered;
                    console.log('After executeSearch - filteredCustomerList:', this.filteredCustomerList);
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
                toggleShowDeleted() {
                    this.showDeleted = !this.showDeleted;
                },
                openAddModal() {
                    this.isEditing = false;
                    this.currentCustomer = { name: '', gender: '', email: '', phone: '', address: '', password: '', image: null };
                    this.selectedFile = null;
                    this.imageSrc = null;
                    this.removeImage = false;
                    this.formErrors = {};
                    this.showOffcanvas('customerOffcanvas');
                },
                openEditModal(customer) {
                    this.isEditing = true;
                    this.currentCustomer = { ...customer, password: '' }; // Clear password for edit
                    this.selectedFile = null;
                    this.imageSrc = customer.image ? `${this.api_url}/storage/${customer.image}` : null;
                    this.removeImage = false;
                    this.formErrors = {};
                    console.log('Editing Customer:', this.currentCustomer);
                    this.showOffcanvas('customerOffcanvas');
                },
                viewCustomerDetails(customer) {
                    this.viewCustomer = { ...customer };
                    this.showOffcanvas('viewCustomerOffcanvas');
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
                        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
                            this.errorMessage = 'Please select a valid image file (JPG, JPEG, PNG)';
                            return;
                        }
                        this.selectedFile = file;
                        this.imageSrc = URL.createObjectURL(file);
                        this.removeImage = false;
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
                removeImage() {
                    this.imageSrc = null;
                    this.selectedFile = null;
                    this.removeImage = true;
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                triggerFileInput() {
                    this.$refs.fileInput.click();
                },
                validateForm() {
                    this.formErrors = {};
                    if (!this.currentCustomer.name || this.currentCustomer.name.trim().length === 0) {
                        this.formErrors.name = 'Name is required';
                    }
                    if (!this.currentCustomer.gender) {
                        this.formErrors.gender = 'Gender is required';
                    }
                    if (!this.currentCustomer.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.currentCustomer.email)) {
                        this.formErrors.email = 'Valid email is required';
                    }
                    if (!this.currentCustomer.phone || !/^\+?[0-9\s\-\(\)]{0,255}$/.test(this.currentCustomer.phone)) {
                        this.formErrors.phone = 'Valid phone number is required';
                    }
                    if (!this.currentCustomer.address || this.currentCustomer.address.trim().length === 0) {
                        this.formErrors.address = 'Address is required';
                    }
                    if (!this.isEditing && (!this.currentCustomer.password || this.currentCustomer.password.length < 8)) {
                        this.formErrors.password = 'Password must be at least 8 characters';
                    }
                    if (this.selectedFile && !['image/jpeg', 'image/jpg', 'image/png'].includes(this.selectedFile.type)) {
                        this.formErrors.image = 'Image must be JPG, JPEG, or PNG';
                    }
                    console.log('Validation Errors:', this.formErrors);
                    return Object.keys(this.formErrors).length === 0;
                },
                async saveCustomer() {
                    if (!this.validateForm()) {
                        console.log('Validation failed:', this.formErrors);
                        return;
                    }
                    try {
                        this.saving = true;
                        const formData = new FormData();
                        formData.append('name', this.currentCustomer.name || '');
                        formData.append('gender', this.currentCustomer.gender || '');
                        formData.append('email', this.currentCustomer.email || '');
                        formData.append('phone', this.currentCustomer.phone || '');
                        formData.append('address', this.currentCustomer.address || '');
                        if (this.currentCustomer.password) {
                            formData.append('password', this.currentCustomer.password);
                        }
                        if (this.selectedFile) {
                            formData.append('image', this.selectedFile);
                        }
                        if (this.removeImage) {
                            formData.append('remove_image', '1');
                        }
                        formData.append('_method', this.isEditing ? 'PUT' : 'POST');

                        console.log('Form Data:', [...formData.entries()]);
                        const url = this.isEditing ? `${this.api_url}/api/customers/${this.currentCustomer.id}` : `${this.api_url}/api/customers`;
                        const response = await axios.post(url, formData, {
                            headers: { 'Content-Type': 'multipart/form-data' }
                        });
                        console.log('Response:', response.data);
                        this.hideOffcanvas('customerOffcanvas');
                        Swal.fire({
                            icon: 'success',
                            title: this.isEditing ? 'Customer updated successfully!' : 'Customer added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        this.resetForm();
                        await this.loadCustomers();
                    } catch (error) {
                        console.error('Save customer error:', {
                            message: error.message,
                            response: error.response ? error.response.data : null,
                            status: error.response ? error.response.status : null
                        });
                        const errors = error.response?.data?.errors;
                        if (errors) {
                            this.formErrors = errors;
                            this.errorMessage = 'Please fix the errors in the form';
                        } else {
                            this.errorMessage = error.response?.data?.message || 'Failed to save customer';
                        }
                    } finally {
                        this.saving = false;
                    }
                },
                resetForm() {
                    this.currentCustomer = { name: '', gender: '', email: '', phone: '', address: '', password: '', image: null };
                    this.selectedFile = null;
                    this.imageSrc = null;
                    this.removeImage = false;
                    this.isEditing = false;
                    this.formErrors = {};
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                async deleteCustomer(customerId) {
                    if (!customerId) return;
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This will soft delete the customer. You can restore it later.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/customers/${customerId}`);
                            await this.loadCustomers();
                            await Swal.fire(
                                'Deleted!',
                                'Customer has been soft deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error deleting customer:', error);
                            this.errorMessage = 'Failed to delete customer';
                            await Swal.fire(
                                'Error!',
                                'Failed to delete customer.',
                                'error'
                            );
                        }
                    }
                },
                async restoreCustomer(customerId) {
                    if (!customerId) return;
                    const result = await Swal.fire({
                        title: 'Restore Customer?',
                        text: "This will restore the customer to active status.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, restore it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.post(`${this.api_url}/api/customers/${customerId}/restore`, {});
                            await this.loadCustomers();
                            await Swal.fire(
                                'Restored!',
                                'Customer has been restored.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error restoring customer:', error);
                            this.errorMessage = 'Failed to restore customer';
                            await Swal.fire(
                                'Error!',
                                'Failed to restore customer.',
                                'error'
                            );
                        }
                    }
                },
                async forceDeleteCustomer(customerId) {
                    if (!customerId) return;
                    const result = await Swal.fire({
                        title: 'Permanently Delete Customer?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, permanently delete!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/customers/${customerId}/force`);
                            await this.loadCustomers();
                            await Swal.fire(
                                'Permanently Deleted!',
                                'Customer has been permanently deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error force deleting customer:', error);
                            this.errorMessage = 'Failed to permanently delete customer';
                            await Swal.fire(
                                'Error!',
                                'Failed to permanently delete customer.',
                                'error'
                            );
                        }
                    }
                },
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
