@extends('admin.master')
<style>
    [v-cloak] {
        display: none;
    }
</style>
@section('content')
    <div id="app" v-cloak>
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
                <div>
                    <h3 class="fw-bold mb-3">Product Details List</h3>
                </div>
                <div>
                    <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Product Details">
                        <i class="fa-solid fa-plus fa-lg"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-round">
                        <div class="card-header">
                            <div class="card-head-row">
                                <div class="card-title">All Product Details</div>
                                <div class="card-tools">
                                    <div class="row justify-content-end align-items-center g-2">
                                        <div class="col-auto">
                                            <input type="text" class="form-control" placeholder="Search product details..."
                                                   v-model="searchQuery" @input="performSearch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-stats mt-2" v-if="searchActive">
                                Showing @{{ displayedProductDetails.length }} of @{{ totalProductDetailsCount }} product details
                                <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading product details data...</p>
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
                                            <th>Model</th>
                                            <th>Processor</th>
                                            <th>RAM</th>
                                            <th>Storage</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(productDetail, index) in displayedProductDetails" :key="productDetail.id">
                                            <td @click="viewProductDetails(productDetail)" style="cursor: pointer;">@{{ index + 1 }}</td>
                                            <td @click="viewProductDetails(productDetail)" style="cursor: pointer;" v-html="highlightText(productDetail.model)"></td>
                                            <td @click="viewProductDetails(productDetail)" style="cursor: pointer;" v-html="highlightText(productDetail.processor || '-')"></td>
                                            <td @click="viewProductDetails(productDetail)" style="cursor: pointer;" v-html="highlightText(productDetail.ram || '-')"></td>
                                            <td @click="viewProductDetails(productDetail)" style="cursor: pointer;" v-html="highlightText(productDetail.storage || '-')"></td>
                                            <td @click="viewProductDetails(productDetail)" style="cursor: pointer;">@{{ formatDate(productDetail.created_at) }}</td>
                                            <td @click="viewProductDetails(productDetail)" style="cursor: pointer;">
                                                <span :class="{'text-danger': productDetail.deleted_at, 'text-success': !productDetail.deleted_at}">
                                                    @{{ productDetail.deleted_at ? 'Deleted' : 'Active' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="me-3" v-if="!productDetail.deleted_at">
                                                        <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(productDetail)">
                                                            <i class="fa-solid fa-pen me-2"></i>Edit
                                                        </a>
                                                    </div>
                                                    <div v-if="!productDetail.deleted_at">
                                                        <a class="action-btn btn-delete" href="#" @click.prevent="deleteProductDetail(productDetail.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                                        </a>
                                                    </div>
                                                    <div v-if="productDetail.deleted_at">
                                                        <a class="action-btn btn-restore" href="#" @click.prevent="restoreProductDetail(productDetail.id)">
                                                            <i class="fa-solid fa-trash-restore me-2"></i>Restore
                                                        </a>
                                                    </div>
                                                    <div v-if="productDetail.deleted_at" class="ms-3">
                                                        <a class="action-btn btn-danger" href="#" @click.prevent="forceDeleteProductDetail(productDetail.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Permanent Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div v-if="displayedProductDetails.length === 0 && !loading" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No product details found</h5>
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
                                            <span v-if="totalFilteredRecords !== totalProductDetailsCount">
                                                (filtered from @{{ totalProductDetailsCount }} total entries)
                                            </span>
                                        </div>
                                        <nav aria-label="Product details pagination">
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="productDetailsOffcanvas" aria-labelledby="productDetailsOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="productDetailsOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Product Details</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="currentProductDetail">
                    <form @submit.prevent="saveProductDetail">
                        <div>
                            <div class="mb-3">
                                <label class="form-label">Model *</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.model" required maxlength="255">
                                <span v-if="formErrors.model" class="text-danger small">@{{ formErrors.model }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Processor</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.processor" maxlength="255">
                                <span v-if="formErrors.processor" class="text-danger small">@{{ formErrors.processor }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">RAM</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.ram" maxlength="255">
                                <span v-if="formErrors.ram" class="text-danger small">@{{ formErrors.ram }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Storage</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.storage" maxlength="255">
                                <span v-if="formErrors.storage" class="text-danger small">@{{ formErrors.storage }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Display</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.display" maxlength="255">
                                <span v-if="formErrors.display" class="text-danger small">@{{ formErrors.display }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Graphics</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.graphics" maxlength="255">
                                <span v-if="formErrors.graphics" class="text-danger small">@{{ formErrors.graphics }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Operating System</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.os" maxlength="255">
                                <span v-if="formErrors.os" class="text-danger small">@{{ formErrors.os }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Battery</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.battery" maxlength="255">
                                <span v-if="formErrors.battery" class="text-danger small">@{{ formErrors.battery }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Weight</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.weight" maxlength="255">
                                <span v-if="formErrors.weight" class="text-danger small">@{{ formErrors.weight }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Warranty</label>
                                <input type="text" class="form-control" v-model="currentProductDetail.warranty" maxlength="255">
                                <span v-if="formErrors.warranty" class="text-danger small">@{{ formErrors.warranty }}</span>
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

            <!-- View Product Details Offcanvas -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="view-product-details-offcanvas" aria-labelledby="viewProductDetailsLabel">
                <div class="offcanvas-header">
                    <div class="w-100 d-flex justify-content-between align-items-start">
                        <div v-if="viewProductDetail">
                            <h5 class="mb-0">@{{ viewProductDetail.model }}</h5>
                            <small class="text-white badge bg-secondary" v-if="viewProductDetail.deleted_at">Deleted</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="viewProductDetail">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">ID</div>
                            <div class="fw-semibold">@{{ viewProductDetail.id }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Model</div>
                            <div class="fw-semibold">@{{ viewProductDetail.model }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Processor</div>
                            <div class="fw-semibold">@{{ viewProductDetail.processor || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">RAM</div>
                            <div class="fw-semibold">@{{ viewProductDetail.ram || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Storage</div>
                            <div class="fw-semibold">@{{ viewProductDetail.storage || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Display</div>
                            <div class="fw-semibold">@{{ viewProductDetail.display || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Graphics</div>
                            <div class="fw-semibold">@{{ viewProductDetail.graphics || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Operating System</div>
                            <div class="fw-semibold">@{{ viewProductDetail.os || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Battery</div>
                            <div class="fw-semibold">@{{ viewProductDetail.battery || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Weight</div>
                            <div class="fw-semibold">@{{ viewProductDetail.weight || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Warranty</div>
                            <div class="fw-semibold">@{{ viewProductDetail.warranty || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Created At</div>
                            <div class="fw-semibold">@{{ formatDate(viewProductDetail.created_at) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Updated At</div>
                            <div class="fw-semibold">@{{ formatDate(viewProductDetail.updated_at) }}</div>
                        </div>
                        <div class="mb-3" v-if="viewProductDetail.deleted_at">
                            <div class="text-muted small">Deleted At</div>
                            <div class="fw-semibold">@{{ formatDate(viewProductDetail.deleted_at) }}</div>
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
                    productDetailsList: [],
                    api_url: 'http://127.0.0.1:8000',
                    filteredProductDetailsList: [],
                    currentProductDetail: null,
                    viewProductDetail: null,
                    isEditing: false,
                    loading: true,
                    saving: false,
                    errorMessage: null,
                    searchQuery: '',
                    searchDebounceTimer: null,
                    currentPage: 1,
                    pageSize: 5,
                    totalProductDetailsCount: 0,
                    formErrors: {},
                    showDeleted: false
                };
            },
            async mounted() {
                console.log('Mounting Vue app...');
                try {
                    await this.loadProductDetails();
                    console.log('Product Details List:', this.productDetailsList);
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
                    return this.filteredProductDetailsList.length;
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
                displayedProductDetails() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    const end = start + this.pageSize;
                    return this.filteredProductDetailsList.slice(start, end);
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
                filteredProductDetailsList() {
                    this.currentPage = 1;
                },
                searchQuery() {
                    this.performSearch();
                },
                showDeleted() {
                    this.loadProductDetails();
                }
            },
            methods: {
                async loadProductDetails() {
                    try {
                        this.loading = true;
                        this.errorMessage = null;
                        console.log('Fetching product details data...');
                        const response = await axios.get(`${this.api_url}/api/productDetails`, {
                            params: { with_deleted: this.showDeleted ? 1 : 0 }
                        });
                        this.productDetailsList = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        this.totalProductDetailsCount = this.productDetailsList.length;
                        this.filteredProductDetailsList = [...this.productDetailsList];
                        this.executeSearch();
                    } catch (error) {
                        console.error('Error loading product details:', error.response ? error.response.data : error.message);
                        this.errorMessage = this.getErrorMessage(error, 'Failed to load product details data');
                        this.productDetailsList = [];
                        this.filteredProductDetailsList = [];
                    } finally {
                        this.loading = false;
                    }
                },
                performSearch() {
                    if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                    this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
                },
                executeSearch() {
                    let filtered = [...this.productDetailsList];
                    if (this.searchQuery.trim()) {
                        const query = this.searchQuery.toLowerCase().trim();
                        filtered = filtered.filter(productDetail =>
                            (productDetail.model && productDetail.model.toLowerCase().includes(query)) ||
                            (productDetail.processor && productDetail.processor.toLowerCase().includes(query)) ||
                            (productDetail.ram && productDetail.ram.toLowerCase().includes(query)) ||
                            (productDetail.storage && productDetail.storage.toLowerCase().includes(query)) ||
                            (productDetail.os && productDetail.os.toLowerCase().includes(query))
                        );
                    }
                    this.filteredProductDetailsList = filtered;
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
                    this.currentProductDetail = {
                        model: '',
                        processor: '',
                        ram: '',
                        storage: '',
                        display: '',
                        graphics: '',
                        os: '',
                        battery: '',
                        weight: '',
                        warranty: ''
                    };
                    this.formErrors = {};
                    this.showOffcanvas('productDetailsOffcanvas');
                },
                openEditModal(productDetail) {
                    this.isEditing = true;
                    this.currentProductDetail = { ...productDetail };
                    this.formErrors = {};
                    console.log('Editing Product Detail:', this.currentProductDetail);
                    this.showOffcanvas('productDetailsOffcanvas');
                },
                viewProductDetails(productDetail) {
                    this.viewProductDetail = { ...productDetail };
                    this.showOffcanvas('view-product-details-offcanvas');
                },
                validateForm() {
                    this.formErrors = {};
                    if (!this.currentProductDetail.model || this.currentProductDetail.model.trim().length === 0) {
                        this.formErrors.model = 'Model is required';
                    }
                    const fields = ['model', 'processor', 'ram', 'storage', 'display', 'graphics', 'os', 'battery', 'weight', 'warranty'];
                    fields.forEach(field => {
                        if (this.currentProductDetail[field] && this.currentProductDetail[field].length > 255) {
                            this.formErrors[field] = `${field.charAt(0).toUpperCase() + field.slice(1)} must be less than 255 characters`;
                        }
                    });
                    console.log('Validation Errors:', this.formErrors);
                    return Object.keys(this.formErrors).length === 0;
                },
                async saveProductDetail() {
                    if (!this.validateForm()) {
                        console.log('Validation failed:', this.formErrors);
                        return;
                    }
                    try {
                        this.saving = true;
                        const data = {
                            model: this.currentProductDetail.model || '',
                            processor: this.currentProductDetail.processor || null,
                            ram: this.currentProductDetail.ram || null,
                            storage: this.currentProductDetail.storage || null,
                            display: this.currentProductDetail.display || null,
                            graphics: this.currentProductDetail.graphics || null,
                            os: this.currentProductDetail.os || null,
                            battery: this.currentProductDetail.battery || null,
                            weight: this.currentProductDetail.weight || null,
                            warranty: this.currentProductDetail.warranty || null
                        };
                        console.log('Data:', data);
                        let response;
                        if (this.isEditing) {
                            response = await axios.put(`${this.api_url}/api/productDetails/${this.currentProductDetail.id}`, data, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                        } else {
                            response = await axios.post(`${this.api_url}/api/productDetails`, data, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                        }
                        console.log('Response:', response.data);
                        this.hideOffcanvas('productDetailsOffcanvas');
                        Swal.fire({
                            icon: 'success',
                            title: this.isEditing ? 'Product details updated successfully!' : 'Product details added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        this.resetForm();
                        await this.loadProductDetails();
                    } catch (error) {
                        console.error('Save product detail error:', {
                            message: error.message,
                            response: error.response ? error.response.data : null,
                            status: error.response ? error.response.status : null
                        });
                        const errors = error.response?.data?.errors;
                        if (errors) {
                            this.formErrors = errors;
                            this.errorMessage = 'Please fix the errors in the form';
                        } else {
                            this.errorMessage = error.response?.data?.message || 'Failed to save product details';
                        }
                    } finally {
                        this.saving = false;
                    }
                },
                resetForm() {
                    this.currentProductDetail = {
                        model: '',
                        processor: '',
                        ram: '',
                        storage: '',
                        display: '',
                        graphics: '',
                        os: '',
                        battery: '',
                        weight: '',
                        warranty: ''
                    };
                    this.isEditing = false;
                    this.formErrors = {};
                },
                async deleteProductDetail(productDetailId) {
                    if (!productDetailId) return;
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This will soft delete the product details. You can restore it later.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/productDetails/${productDetailId}`, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadProductDetails();
                            await Swal.fire(
                                'Deleted!',
                                'Product details have been soft deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error deleting product details:', error);
                            this.errorMessage = 'Failed to delete product details';
                            await Swal.fire(
                                'Error!',
                                'Failed to delete product details.',
                                'error'
                            );
                        }
                    }
                },
                async restoreProductDetail(productDetailId) {
                    if (!productDetailId) return;
                    const result = await Swal.fire({
                        title: 'Restore Product Details?',
                        text: "This will restore the product details to active status.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, restore it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.post(`${this.api_url}/api/productDetails/${productDetailId}/restore`, {}, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadProductDetails();
                            await Swal.fire(
                                'Restored!',
                                'Product details have been restored.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error restoring product details:', error);
                            this.errorMessage = 'Failed to restore product details';
                            await Swal.fire(
                                'Error!',
                                'Failed to restore product details.',
                                'error'
                            );
                        }
                    }
                },
                async forceDeleteProductDetail(productDetailId) {
                    if (!productDetailId) return;
                    const result = await Swal.fire({
                        title: 'Permanently Delete Product Details?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, permanently delete!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/productDetails/${productDetailId}/force`, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadProductDetails();
                            await Swal.fire(
                                'Permanently Deleted!',
                                'Product details have been permanently deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error force deleting product details:', error);
                            this.errorMessage = 'Failed to permanently delete product details';
                            await Swal.fire(
                                'Error!',
                                'Failed to permanently delete product details.',
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
