@extends('admin.master')
@section('content')
    <div id="branchApp">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
                <div>
                    <h3 class="fw-bold mb-3">Branch List</h3>
                </div>
                <div>
                    <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Branch">
                        <i class="fa-solid fa-building fa-lg"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-round">
                        <div class="card-header">
                            <div class="card-head-row">
                                <div class="card-title">All Branches</div>
                                <div class="card-tools">
                                    <div class="row justify-content-end align-items-center g-2">
                                        <div class="col-auto">
                                            <input type="text" class="form-control" placeholder="Search branches..."
                                                   v-model="searchQuery" @input="performSearch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-stats mt-2" v-if="searchActive">
                                Showing @{{ displayedBranches.length }} of @{{ totalBranchCount }} branches
                                <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading branch data...</p>
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
                                            <th>Logo</th>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(branch, index) in displayedBranches" :key="branch.id">
                                            <td @click="viewBranchDetails(branch)" style="cursor: pointer;">@{{ index + 1 }}</td>
                                            <td @click="viewBranchDetails(branch)" style="cursor: pointer;">
                                                <img v-if="branch.logo" :src="api_url + '/storage/' + branch.logo"
                                                     alt="Branch Logo" class="profile-img rounded-circle" style="width: 40px; height: 40px;">
                                                <span v-else>-</span>
                                            </td>
                                            <td @click="viewBranchDetails(branch)" style="cursor: pointer;" v-html="highlightText(branch.name)"></td>
                                            <td @click="viewBranchDetails(branch)" style="cursor: pointer;" v-html="highlightText(branch.address || '-')"></td>
                                            <td @click="viewBranchDetails(branch)" style="cursor: pointer;" v-html="highlightText(branch.phone || '-')"></td>
                                            <td @click="viewBranchDetails(branch)" style="cursor: pointer;" v-html="highlightText(branch.email || '-')"></td>
                                            <td @click="viewBranchDetails(branch)" style="cursor: pointer;">@{{ formatDate(branch.created_at) }}</td>
                                            <td @click="viewBranchDetails(branch)" style="cursor: pointer;">
                                                <span :class="{'text-danger': branch.deleted_at, 'text-success': !branch.deleted_at}">
                                                    @{{ branch.deleted_at ? 'Deleted' : 'Active' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="me-3" v-if="!branch.deleted_at">
                                                        <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(branch)">
                                                            <i class="fa-solid fa-pen me-2"></i>Edit
                                                        </a>
                                                    </div>
                                                    <div v-if="!branch.deleted_at">
                                                        <a class="action-btn btn-delete" href="#" @click.prevent="deleteBranch(branch.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                                        </a>
                                                    </div>
                                                    <div v-if="branch.deleted_at">
                                                        <a class="action-btn btn-restore" href="#" @click.prevent="restoreBranch(branch.id)">
                                                            <i class="fa-solid fa-trash-restore me-2"></i>Restore
                                                        </a>
                                                    </div>
                                                    <div v-if="branch.deleted_at" class="ms-3">
                                                        <a class="action-btn btn-danger" href="#" @click.prevent="forceDeleteBranch(branch.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Permanent Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div v-if="displayedBranches.length === 0 && !loading" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No branches found</h5>
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
                                            <span v-if="totalFilteredRecords !== totalBranchCount">
                                                (filtered from @{{ totalBranchCount }} total entries)
                                            </span>
                                        </div>
                                        <nav aria-label="Branch pagination">
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="branchOffcanvas" aria-labelledby="branchOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="branchOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Branch</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="currentBranch">
                    <form @submit.prevent="saveBranch">
                        <div>
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" v-model="currentBranch.name" required maxlength="255">
                                <span v-if="formErrors.name" class="text-danger small">@{{ formErrors.name }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" v-model="currentBranch.address" maxlength="255" rows="3"></textarea>
                                <span v-if="formErrors.address" class="text-danger small">@{{ formErrors.address }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" v-model="currentBranch.phone" maxlength="255" pattern="\+?[0-9\s\-\(\)]*">
                                <span v-if="formErrors.phone" class="text-danger small">@{{ formErrors.phone }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" v-model="currentBranch.email" maxlength="255">
                                <span v-if="formErrors.email" class="text-danger small">@{{ formErrors.email }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Logo</label>
                                <input type="file" @change="handleFileUpload" class="form-control" accept="image/*">
                                <div v-if="currentBranch.logo_preview" class="mt-2">
                                    <img :src="currentBranch.logo_preview" alt="Logo Preview" class="rounded shadow" style="width: 100px; height: auto;">
                                </div>
                                <div v-else-if="currentBranch.logo && !currentBranch.logo_file" class="mt-2">
                                    <img :src="api_url + '/storage/' + currentBranch.logo" alt="Current Logo" class="rounded shadow" style="width: 100px; height: auto;">
                                </div>
                                <span v-if="formErrors.logo" class="text-danger small">@{{ formErrors.logo }}</span>
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

            <!-- View Branch Details Offcanvas -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="view-branch-offcanvas" aria-labelledby="viewBranchLabel">
                <div class="offcanvas-header">
                    <div class="w-100 d-flex justify-content-between align-items-start">
                        <div v-if="viewBranch">
                            <h5 class="mb-0">@{{ viewBranch.name }}</h5>
                            <small class="text-white badge bg-secondary" v-if="viewBranch.deleted_at">Deleted</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="viewBranch">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">ID</div>
                            <div class="fw-semibold">@{{ viewBranch.id }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Name</div>
                            <div class="fw-semibold">@{{ viewBranch.name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Address</div>
                            <div class="fw-semibold">@{{ viewBranch.address || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Phone</div>
                            <div class="fw-semibold">@{{ viewBranch.phone || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold">@{{ viewBranch.email || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Logo</div>
                            <div v-if="viewBranch.logo" class="fw-semibold">
                                <img :src="api_url + '/storage/' + viewBranch.logo" alt="Branch Logo" class="rounded shadow" style="width: 100px; height: auto;">
                            </div>
                            <div v-else>-</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Created At</div>
                            <div class="fw-semibold">@{{ formatDate(viewBranch.created_at) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Updated At</div>
                            <div class="fw-semibold">@{{ formatDate(viewBranch.updated_at) }}</div>
                        </div>
                        <div class="mb-3" v-if="viewBranch.deleted_at">
                            <div class="text-muted small">Deleted At</div>
                            <div class="fw-semibold">@{{ formatDate(viewBranch.deleted_at) }}</div>
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
                    branchList: [],
                    api_url: 'http://127.0.0.1:8000',
                    filteredBranchList: [],
                    currentBranch: null,
                    viewBranch: null,
                    isEditing: false,
                    loading: true,
                    saving: false,
                    errorMessage: null,
                    searchQuery: '',
                    searchDebounceTimer: null,
                    currentPage: 1,
                    pageSize: 5,
                    totalBranchCount: 0,
                    formErrors: {},
                    showDeleted: false
                };
            },
            async mounted() {
                console.log('Mounting Vue.js app...');
                try {
                    await this.loadBranches();
                    console.log('Branch List:', this.branchList);
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
                    return this.filteredBranchList.length;
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
                displayedBranches() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    const end = start + this.pageSize;
                    return this.filteredBranchList.slice(start, end);
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
                filteredBranchList() {
                    this.currentPage = 1;
                },
                searchQuery() {
                    this.performSearch();
                },
                showDeleted() {
                    this.loadBranches();
                }
            },
            methods: {
                async loadBranches() {
                    try {
                        this.loading = true;
                        this.errorMessage = null;
                        console.log('Fetching branch data with showDeleted:', this.showDeleted);
                        const response = await axios.get(`${this.api_url}/api/branches`, {
                            params: { with_deleted: this.showDeleted ? 1 : 0 }
                        });
                        console.log('API Response:', response.data);
                        this.branchList = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        this.totalBranchCount = this.branchList.length;
                        this.filteredBranchList = [...this.branchList];
                        this.executeSearch();
                        console.log('After loadBranches - branchList:', this.branchList, 'totalBranchCount:', this.totalBranchCount);
                    } catch (error) {
                        console.error('Error loading branches:', error.response ? error.response.data : error.message);
                        this.errorMessage = this.getErrorMessage(error, 'Failed to load branch data');
                        this.branchList = [];
                        this.filteredBranchList = [];
                    } finally {
                        this.loading = false;
                    }
                },
                performSearch() {
                    if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                    this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
                },
                executeSearch() {
                    let filtered = [...this.branchList];
                    if (this.searchQuery.trim()) {
                        const query = this.searchQuery.toLowerCase().trim();
                        filtered = filtered.filter(branch =>
                            (branch.name && branch.name.toLowerCase().includes(query)) ||
                            (branch.address && branch.address.toLowerCase().includes(query)) ||
                            (branch.phone && branch.phone.toLowerCase().includes(query)) ||
                            (branch.email && branch.email.toLowerCase().includes(query))
                        );
                    }
                    this.filteredBranchList = filtered;
                    console.log('After executeSearch - filteredBranchList:', this.filteredBranchList);
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
                    this.currentBranch = { name: '', address: '', phone: '', email: '', logo: null, logo_file: null, logo_preview: null };
                    this.formErrors = {};
                    this.showOffcanvas('branchOffcanvas');
                },
                openEditModal(branch) {
                    this.isEditing = true;
                    this.currentBranch = {
                        ...branch,
                        logo_file: null,
                        logo_preview: branch.logo ? `${this.api_url}/storage/${branch.logo}` : null
                    };
                    this.formErrors = {};
                    console.log('Editing Branch:', this.currentBranch);
                    this.showOffcanvas('branchOffcanvas');
                },
                viewBranchDetails(branch) {
                    this.viewBranch = { ...branch };
                    this.showOffcanvas('view-branch-offcanvas');
                },
                handleFileUpload(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.currentBranch.logo_file = file;
                        this.currentBranch.logo_preview = URL.createObjectURL(file);
                    }
                },
                validateForm() {
                    this.formErrors = {};
                    if (!this.currentBranch.name || this.currentBranch.name.trim().length === 0) {
                        this.formErrors.name = 'Name is required';
                    }
                    if (this.currentBranch.address && this.currentBranch.address.length > 255) {
                        this.formErrors.address = 'Address must be less than 255 characters';
                    }
                    if (this.currentBranch.phone && !/^\+?[0-9\s\-\(\)]{0,255}$/.test(this.currentBranch.phone)) {
                        this.formErrors.phone = 'Invalid phone number format';
                    }
                    if (this.currentBranch.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.currentBranch.email)) {
                        this.formErrors.email = 'Invalid email format';
                    }
                    if (this.currentBranch.logo_file && !['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'].includes(this.currentBranch.logo_file.type)) {
                        this.formErrors.logo = 'Logo must be an image (JPEG, PNG, GIF, SVG)';
                    }
                    console.log('Validation Errors:', this.formErrors);
                    return Object.keys(this.formErrors).length === 0;
                },
                async saveBranch() {
                    if (!this.validateForm()) {
                        console.log('Validation failed:', this.formErrors);
                        return;
                    }
                    try {
                        this.saving = true;
                        const formData = new FormData();
                        formData.append('name', this.currentBranch.name || '');
                        formData.append('address', this.currentBranch.address || '');
                        formData.append('phone', this.currentBranch.phone || '');
                        formData.append('email', this.currentBranch.email || '');
                        if (this.currentBranch.logo_file) {
                            formData.append('logo', this.currentBranch.logo_file);
                        }
                        formData.append('_method', this.isEditing ? 'PUT' : 'POST');

                        console.log('Form Data:', [...formData.entries()]);
                        const url = this.isEditing ? `${this.api_url}/api/branches/${this.currentBranch.id}` : `${this.api_url}/api/branches`;
                        const response = await axios.post(url, formData, {
                            headers: { 'Content-Type': 'multipart/form-data' }
                        });
                        console.log('Response:', response.data);
                        this.hideOffcanvas('branchOffcanvas');
                        Swal.fire({
                            icon: 'success',
                            title: this.isEditing ? 'Branch updated successfully!' : 'Branch added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        this.resetForm();
                        await this.loadBranches();
                    } catch (error) {
                        console.error('Save branch error:', {
                            message: error.message,
                            response: error.response ? error.response.data : null,
                            status: error.response ? error.response.status : null
                        });
                        const errors = error.response?.data?.errors;
                        if (errors) {
                            this.formErrors = errors;
                            this.errorMessage = 'Please fix the errors in the form';
                        } else {
                            this.errorMessage = error.response?.data?.message || 'Failed to save branch';
                        }
                    } finally {
                        this.saving = false;
                    }
                },
                resetForm() {
                    this.currentBranch = { name: '', address: '', phone: '', email: '', logo: null, logo_file: null, logo_preview: null };
                    this.isEditing = false;
                    this.formErrors = {};
                },
                async deleteBranch(branchId) {
                    if (!branchId) return;
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This will soft delete the branch. You can restore it later.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/branches/${branchId}`);
                            await this.loadBranches();
                            await Swal.fire(
                                'Deleted!',
                                'Branch has been soft deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error deleting branch:', error);
                            this.errorMessage = 'Failed to delete branch';
                            await Swal.fire(
                                'Error!',
                                'Failed to delete branch.',
                                'error'
                            );
                        }
                    }
                },
                async restoreBranch(branchId) {
                    if (!branchId) return;
                    const result = await Swal.fire({
                        title: 'Restore Branch?',
                        text: "This will restore the branch to active status.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, restore it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.post(`${this.api_url}/api/branches/${branchId}/restore`, {});
                            await this.loadBranches();
                            await Swal.fire(
                                'Restored!',
                                'Branch has been restored.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error restoring branch:', error);
                            this.errorMessage = 'Failed to restore branch';
                            await Swal.fire(
                                'Error!',
                                'Failed to restore branch.',
                                'error'
                            );
                        }
                    }
                },
                async forceDeleteBranch(branchId) {
                    if (!branchId) return;
                    const result = await Swal.fire({
                        title: 'Permanently Delete Branch?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, permanently delete!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/branches/${branchId}/force`);
                            await this.loadBranches();
                            await Swal.fire(
                                'Permanently Deleted!',
                                'Branch has been permanently deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error force deleting branch:', error);
                            this.errorMessage = 'Failed to permanently delete branch';
                            await Swal.fire(
                                'Error!',
                                'Failed to permanently delete branch.',
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
        }).mount('#branchApp');
    </script>
@endsection
