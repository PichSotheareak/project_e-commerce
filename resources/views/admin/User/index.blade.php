@extends('admin.master')
<style>
    [v-cloak] {
        display: none;
    }
</style>
@section('content')
    <div id="userApp" v-cloak>
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
                <div>
                    <h3 class="fw-bold mb-3">User List</h3>
                </div>
                <div>
                    <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add User">
                        <i class="fa-solid fa-user-plus fa-lg"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-round">
                        <div class="card-header">
                            <div class="card-head-row">
                                <div class="card-title">All Users</div>
                                <div class="card-tools">
                                    <div class="row justify-content-end align-items-center g-2">
                                        <div class="col-auto">
                                            <select class="form-select form-select-sm" v-model="selectedStatusFilter" @change="performSearch">
                                                <option value="">All Statuses</option>
                                                <option v-for="status in availableStatuses" :key="status" :value="status">
                                                    @{{ status }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <input type="text" class="form-control" placeholder="Search users..."
                                                   v-model="searchQuery" @input="performSearch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-stats mt-2" v-if="searchActive">
                                Showing @{{ displayedUsers.length }} of @{{ totalUserCount }} users
                                <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading user data...</p>
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
                                            <th>Status</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Type</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(user, index) in displayedUsers" :key="user.id">
                                            <td @click="viewUserDetails(user)" style="cursor: pointer;">@{{ index + 1 }}</td>
                                            <td @click="viewUserDetails(user)" style="cursor: pointer;">
                                                <img v-if="user.profile && user.profile.image" :src="api_url + '/storage/' + user.profile.image"
                                                     alt="User Image" class="profile-img rounded-circle" style="width: 40px; height: 40px;">
                                                <span v-else>-</span>
                                            </td>
                                            <td @click="viewUserDetails(user)" style="cursor: pointer;" v-html="highlightText(user.name)"></td>
                                            <td @click="viewUserDetails(user)" style="cursor: pointer;">@{{ user.gender || '-' }}</td>
                                            <td @click="viewUserDetails(user)" style="cursor: pointer;" v-html="highlightText(user.email)"></td>
                                            <td @click="viewUserDetails(user)" style="cursor: pointer;">@{{ user.status || '-' }}</td>
                                            <td @click="viewUserDetails(user)" style="cursor: pointer;" v-html="highlightText(user.profile && user.profile.phone ? user.profile.phone : '-')"></td>
                                            <td @click="viewUserDetails(user)" style="cursor: pointer;" v-html="highlightText(user.profile && user.profile.address ? user.profile.address : '-')"></td>
                                            <td @click="viewUserDetails(user)" style="cursor: pointer;">@{{ user.profile && user.profile.type ? user.profile.type : '-' }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="me-3" v-if="!user.deleted_at">
                                                        <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(user)">
                                                            <i class="fa-solid fa-pen me-2"></i>Edit
                                                        </a>
                                                    </div>
                                                    <div v-if="!user.deleted_at">
                                                        <a class="action-btn btn-delete" href="#" @click.prevent="deleteUser(user.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                                        </a>
                                                    </div>
                                                    <div v-if="user.deleted_at">
                                                        <a class="action-btn btn-restore" href="#" @click.prevent="restoreUser(user.id)">
                                                            <i class="fa-solid fa-trash-restore me-2"></i>Restore
                                                        </a>
                                                    </div>
                                                    <div v-if="user.deleted_at" class="ms-3">
                                                        <a class="action-btn btn-danger" href="#" @click.prevent="forceDeleteUser(user.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Permanent Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div v-if="displayedUsers.length === 0 && !loading" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No users found</h5>
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
                                            <span v-if="totalFilteredRecords !== totalUserCount">
                                            (filtered from @{{ totalUserCount }} total entries)
                                        </span>
                                        </div>
                                        <nav aria-label="User pagination navigation">
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="userOffcanvas" aria-labelledby="userOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="userOffcanvasLabel" v-if="currentUser">@{{ isEditing ? 'Edit User' : 'Add User' }}</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="currentUser">
                    <form @submit.prevent="saveUser" enctype="multipart/form-data">
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
                                        <p class="text-sm text-gray-500 mb-1">Drag and drop or click to upload an image</p>
                                        <p class="text-sm text-gray-500 mb-2">Maximum file size is 2MB, supported formats: JPG, JPEG, PNG</p>
                                        <input type="file" ref="fileInput" accept=".jpg,.jpeg,.png" @change="handleFileUpload" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            <div v-if="imageSrc" class="text-center">
                                <h6 class="text-muted mb-4 d-flex fw-bold">User Profile Image</h6>
                                <div class="image-container">
                                    <img :src="imageSrc" alt="User Profile Image" class="profile-photo">
                                    <button type="button" class="remove-btn" @click="removeImage" title="Remove Image">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" v-model="currentUser.name" required maxlength="255">
                                <span v-if="formErrors.name" class="text-danger small">@{{ formErrors.name }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gender *</label>
                                <select class="form-select" v-model="currentUser.gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <span v-if="formErrors.gender" class="text-danger small">@{{ formErrors.gender }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" v-model="currentUser.email" required maxlength="255">
                                <span v-if="formErrors.email" class="text-danger small">@{{ formErrors.email }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password @{{ isEditing ? '(Leave blank to Keep Unchanged)' : '*' }}</label>
                                <input type="password" class="form-control" v-model="currentUser.password" :required="!isEditing" maxlength="255">
                                <span v-if="formErrors.password" class="text-danger small">@{{ formErrors.password }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select" v-model="currentUser.status" required>
                                    <option value="">Select Status</option>
                                    <option value="Enable">Enable</option>
                                    <option value="Disable">Disable</option>
                                </select>
                                <span v-if="formErrors.status" class="text-danger small">@{{ formErrors.status }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" v-model="currentUser.profile.phone" maxlength="255">
                                <span v-if="formErrors.phone" class="text-danger small">@{{ formErrors.phone }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" v-model="currentUser.profile.address" maxlength="255" rows="3"></textarea>
                                <span v-if="formErrors.address" class="text-danger small">@{{ formErrors.address }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <input type="text" class="form-control" v-model="currentUser.profile.type" maxlength="255">
                                <span v-if="formErrors.type" class="text-danger small">@{{ formErrors.type }}</span>
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="viewUserOffcanvas" aria-labelledby="viewUserOffcanvasLabel">
                <div class="offcanvas-header">
                    <div class="w-100 d-flex justify-content-between align-items-start">
                        <div v-if="viewUser">
                            <h5 class="mb-0">@{{ viewUser.name }}</h5>
                            <small class="text-white badge bg-secondary" v-if="viewUser.status">@{{ viewUser.status }}</small>
                            <small class="badge bg-danger" v-if="viewUser.deleted_at">Deleted</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="viewUser">
                    <div class="text-center mb-4">
                        <img :src="viewUser.profile && viewUser.profile.image ? api_url + '/storage/' + viewUser.profile.image : 'https://via.placeholder.com/100'"
                             alt="User Profile Image" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover">
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">ID</div>
                            <div class="fw-semibold">@{{ viewUser.id }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Name</div>
                            <div class="fw-semibold">@{{ viewUser.name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Gender</div>
                            <div class="fw-semibold">@{{ viewUser.gender || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold">@{{ viewUser.email }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Status</div>
                            <div class="fw-semibold">@{{ viewUser.status || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Phone</div>
                            <div class="fw-semibold">@{{ viewUser.profile && viewUser.profile.phone || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Address</div>
                            <div class="fw-semibold">@{{ viewUser.profile && viewUser.profile.address || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Type</div>
                            <div class="fw-semibold">@{{ viewUser.profile && viewUser.profile.type || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Created At</div>
                            <div class="fw-semibold">@{{ formatDate(viewUser.created_at) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Updated At</div>
                            <div class="fw-semibold">@{{ formatDate(viewUser.updated_at) }}</div>
                        </div>
                        <div class="mb-3" v-if="viewUser.deleted_at">
                            <div class="text-muted small">Deleted At</div>
                            <div class="fw-semibold">@{{ formatDate(viewUser.deleted_at) }}</div>
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
        } else {
            console.warn('CSRF token meta tag not found. Ensure <meta name="csrf-token" content="{{ csrf_token() }}"> is in your layout.');
        }

        // Setup Authorization token globally for Axios
        axios.interceptors.request.use(config => {
            const token = localStorage.getItem('token') ?? '';
            if (token) {
                config.headers['Authorization'] = `Bearer ${token}`;
            }
            console.log('Token:', token);
            return config;
        }, error => {
            return Promise.reject(error);
        });

        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    userList: [],
                    api_url: 'http://127.0.0.1:8000',
                    filteredUserList: [],
                    currentUser: null,
                    viewUser: null,
                    isEditing: false,
                    loading: true,
                    saving: false,
                    errorMessage: null,
                    selectedFile: null,
                    imageSrc: null,
                    isDragOver: false,
                    searchQuery: '',
                    selectedStatusFilter: '',
                    searchDebounceTimer: null,
                    currentPage: 1,
                    pageSize: 5,
                    totalUserCount: 0,
                    formErrors: {},
                    showDeleted: false,
                    removeImage: false
                };
            },
            async mounted() {
                console.log('Mounting Vue.js app to #userApp...');
                try {
                    await this.loadUsers();
                    console.log('User List:', this.userList);
                } catch (error) {
                    console.error('Error during initialization:', error);
                    this.errorMessage = 'Failed to initialize application. Details: ' + (error.message || 'Unknown error');
                }
            },
            computed: {
                availableStatuses() {
                    return [...new Set(this.userList.map(user => user.status))].filter(Boolean).sort();
                },
                searchActive() {
                    return this.searchQuery.trim() !== '' || this.selectedStatusFilter !== '';
                },
                totalFilteredRecords() {
                    return this.filteredUserList.length;
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
                displayedUsers() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    const end = start + this.pageSize;
                    return this.filteredUserList.slice(start, end);
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
                filteredUserList() {
                    this.currentPage = 1;
                },
                searchQuery() {
                    this.performSearch();
                },
                selectedStatusFilter() {
                    this.performSearch();
                },
                showDeleted() {
                    this.loadUsers();
                }
            },
            methods: {
                async loadUsers() {
                    try {
                        this.loading = true;
                        this.errorMessage = null;
                        console.log('Fetching user data with showDeleted:', this.showDeleted);
                        const response = await axios.get(`${this.api_url}/api/users`, {
                            params: { with_deleted: this.showDeleted ? 1 : 0 }
                        });
                        console.log('API Response:', response.data);
                        this.userList = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        this.totalUserCount = this.userList.length;
                        this.filteredUserList = [...this.userList];
                        this.executeSearch();
                        console.log('After loadUsers - userList:', this.userList, 'totalUserCount:', this.totalUserCount);
                    } catch (error) {
                        console.error('Error loading users:', error.response ? error.response.data : error.message);
                        this.errorMessage = this.getErrorMessage(error, 'Failed to load user data');
                        this.userList = [];
                        this.filteredUserList = [];
                    } finally {
                        this.loading = false;
                    }
                },
                performSearch() {
                    if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                    this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
                },
                executeSearch() {
                    let filtered = [...this.userList];
                    if (this.searchQuery.trim()) {
                        const query = this.searchQuery.toLowerCase().trim();
                        filtered = filtered.filter(user =>
                            (user.name && user.name.toLowerCase().includes(query)) ||
                            (user.email && user.email.toLowerCase().includes(query)) ||
                            (user.status && user.status.toLowerCase().includes(query)) ||
                            (user.profile && user.profile.phone && user.profile.phone.toLowerCase().includes(query)) ||
                            (user.profile && user.profile.address && user.profile.address.toLowerCase().includes(query)) ||
                            (user.profile && user.profile.type && user.profile.type.toLowerCase().includes(query))
                        );
                    }
                    if (this.selectedStatusFilter) {
                        filtered = filtered.filter(user => user.status?.toLowerCase() === this.selectedStatusFilter.toLowerCase());
                    }
                    this.filteredUserList = filtered;
                    console.log('After executeSearch - filteredUserList:', this.filteredUserList);
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
                    this.currentUser = {
                        name: '',
                        gender: '',
                        email: '',
                        password: '',
                        status: '',
                        profile: { phone: '', address: '', type: '', image: null }
                    };
                    this.selectedFile = null;
                    this.imageSrc = null;
                    this.removeImage = false;
                    this.formErrors = {};
                    this.showOffcanvas('userOffcanvas');
                },
                openEditModal(user) {
                    this.isEditing = true;
                    this.currentUser = {
                        ...user,
                        password: '',
                        profile: {
                            phone: user.profile?.phone || '',
                            address: user.profile?.address || '',
                            type: user.profile?.type || '',
                            image: user.profile?.image || null
                        }
                    };
                    this.selectedFile = null;
                    this.imageSrc = user.profile?.image ? `${this.api_url}/storage/${user.profile.image}` : null;
                    this.removeImage = false;
                    this.formErrors = {};
                    console.log('Editing User:', this.currentUser);
                    this.showOffcanvas('userOffcanvas');
                },
                viewUserDetails(user) {
                    this.viewUser = { ...user };
                    this.showOffcanvas('viewUserOffcanvas');
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
                    if (!this.currentUser.name || this.currentUser.name.trim().length === 0) {
                        this.formErrors.name = 'Name is required';
                    }
                    if (!this.currentUser.gender || !['Male', 'Female'].includes(this.currentUser.gender)) {
                        this.formErrors.gender = 'The selected gender is invalid';
                    }
                    if (!this.currentUser.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.currentUser.email)) {
                        this.formErrors.email = 'Valid email is required';
                    }
                    if (!this.isEditing && (!this.currentUser.password || this.currentUser.password.length < 8)) {
                        this.formErrors.password = 'Password must be at least 8 characters';
                    }
                    if (!this.currentUser.status || !['Enable', 'Disable'].includes(this.currentUser.status)) {
                        this.formErrors.status = 'The selected status is invalid';
                    }
                    if (this.currentUser.profile.phone && !/^\+?[0-9\s\-\(\)]{0,255}$/.test(this.currentUser.profile.phone)) {
                        this.formErrors.phone = 'Valid phone number is required';
                    }
                    if (this.selectedFile && !['image/jpeg', 'image/jpg', 'image/png'].includes(this.selectedFile.type)) {
                        this.formErrors.image = 'Image must be JPG, JPEG, or PNG';
                    }
                    console.log('Validation Errors:', this.formErrors);
                    return Object.keys(this.formErrors).length === 0;
                },
                async saveUser() {
                    if (!this.validateForm()) {
                        console.log('Validation failed:', this.formErrors);
                        return;
                    }
                    try {
                        this.saving = true;
                        const formData = new FormData();
                        formData.append('name', this.currentUser.name || '');
                        formData.append('gender', this.currentUser.gender || '');
                        formData.append('email', this.currentUser.email || '');
                        if (this.currentUser.password) {
                            formData.append('password', this.currentUser.password);
                        }
                        formData.append('status', this.currentUser.status || '');
                        formData.append('phone', this.currentUser.profile?.phone || '');
                        formData.append('address', this.currentUser.profile?.address || '');
                        formData.append('type', this.currentUser.profile?.type || '');
                        if (this.selectedFile) {
                            formData.append('image', this.selectedFile);
                        }
                        if (this.removeImage) {
                            formData.append('remove_image', '1');
                        }
                        formData.append('_method', this.isEditing ? 'PUT' : 'POST');

                        console.log('Form Data:', [...formData.entries()]);
                        const url = this.isEditing ? `${this.api_url}/api/users/${this.currentUser.id}` : `${this.api_url}/api/users`;
                        const response = await axios.post(url, formData, {
                            headers: { 'Content-Type': 'multipart/form-data' }
                        });
                        console.log('Response:', response.data);
                        this.hideOffcanvas('userOffcanvas');
                        Swal.fire({
                            icon: 'success',
                            title: this.isEditing ? 'User updated successfully!' : 'User added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        this.resetForm();
                        await this.loadUsers();
                    } catch (error) {
                        console.error('Save user error:', {
                            message: error.message,
                            response: error.response ? error.response.data : null,
                            status: error.response ? error.response.status : null
                        });
                        const errors = error.response?.data?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(key => {
                                this.formErrors[key] = errors[key][0];
                            });
                            this.errorMessage = 'Please fix the errors in the form';
                        } else {
                            this.errorMessage = error.response?.data?.message || 'Failed to save user';
                        }
                    } finally {
                        this.saving = false;
                    }
                },
                resetForm() {
                    this.currentUser = {
                        name: '',
                        gender: '',
                        email: '',
                        password: '',
                        status: '',
                        profile: { phone: '', address: '', type: '', image: null }
                    };
                    this.selectedFile = null;
                    this.imageSrc = null;
                    this.removeImage = false;
                    this.isEditing = false;
                    this.formErrors = {};
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                async deleteUser(userId) {
                    if (!userId) return;
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This will soft delete the user and their profile. You can restore it later.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/users/${userId}`);
                            await this.loadUsers();
                            await Swal.fire(
                                'Deleted!',
                                'User has been soft deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error deleting user:', error);
                            this.errorMessage = 'Failed to delete user';
                            await Swal.fire(
                                'Error!',
                                'Failed to delete user.',
                                'error'
                            );
                        }
                    }
                },
                async restoreUser(userId) {
                    if (!userId) return;
                    const result = await Swal.fire({
                        title: 'Restore User?',
                        text: "This will restore the user and their profile to active status.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, restore it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.post(`${this.api_url}/api/users/${userId}/restore`, {});
                            await this.loadUsers();
                            await Swal.fire(
                                'Restored!',
                                'User has been restored.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error restoring user:', error);
                            this.errorMessage = 'Failed to restore user';
                            await Swal.fire(
                                'Error!',
                                'Failed to restore user.',
                                'error'
                            );
                        }
                    }
                },
                async forceDeleteUser(userId) {
                    if (!userId) return;
                    const result = await Swal.fire({
                        title: 'Permanently Delete User?',
                        text: "This action cannot be undone and will delete both the user and their profile!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, permanently delete!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/users/${userId}/force`);
                            await this.loadUsers();
                            await Swal.fire(
                                'Permanently Deleted!',
                                'User and their profile have been permanently deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error force deleting user:', error);
                            this.errorMessage = 'Failed to permanently delete user';
                            await Swal.fire(
                                'Error!',
                                'Failed to permanently delete user.',
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
                    if (element) {
                        new bootstrap.Offcanvas(element).show();
                    } else {
                        console.error(`Offcanvas element with ID ${elementId} not found.`);
                    }
                },
                hideOffcanvas(elementId) {
                    const element = document.getElementById(elementId);
                    if (element) {
                        bootstrap.Offcanvas.getInstance(element)?.hide();
                    } else {
                        console.error(`Offcanvas element with ID ${elementId} not found.`);
                    }
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
        }).mount('#userApp');
    </script>
@endsection
