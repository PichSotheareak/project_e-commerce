@extends('admin.master')
@section('content')
    <div id="app">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
                <div>
                    <h3 class="fw-bold mb-3">Brands List</h3>
                </div>
                <div>
                    <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Brand">
                        <i class="fa-solid fa-plus fa-lg"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-round">
                        <div class="card-header">
                            <div class="card-head-row">
                                <div class="card-title">All Brands</div>
                                <div class="card-tools">
                                    <div class="row justify-content-end align-items-center g-2">
                                        <div class="col-auto">
                                            <input type="text" class="form-control" placeholder="Search Brands..."
                                                   v-model="searchQuery" @input="performSearch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-stats mt-2" v-if="searchActive">
                                Showing @{{ displayedBrands.length }} of @{{ totalBrandCount }} Brands
                                <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading brand data...</p>
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
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(brand, index) in displayedBrands" :key="brand.id">
                                            <td @click="viewBrandDetails(brand)" style="cursor: pointer;">@{{ index + 1 }}</td>
                                            <td @click="viewBrandDetails(brand)" style="cursor: pointer;">
                                                <img :src="brand.image ? api_url+'/storage/'+brand.image : 'https://via.placeholder.com/50'"
                                                     alt="Brand Image" class="brand-img rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td @click="viewBrandDetails(brand)" style="cursor: pointer;" v-html="highlightText(brand.name)"></td>
                                            <td @click="viewBrandDetails(brand)" style="cursor: pointer;">@{{ formatDate(brand.created_at) }}</td>
                                            <td @click="viewBrandDetails(brand)" style="cursor: pointer;">
                                                <span :class="{'text-danger': brand.deleted_at, 'text-success': !brand.deleted_at}">
                                                    @{{ brand.deleted_at ? 'Deleted' : 'Active' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="me-3" v-if="!brand.deleted_at">
                                                        <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(brand)">
                                                            <i class="fa-solid fa-pen me-2"></i>Edit
                                                        </a>
                                                    </div>
                                                    <div v-if="!brand.deleted_at">
                                                        <a class="action-btn btn-delete" href="#" @click.prevent="deleteBrand(brand.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                                        </a>
                                                    </div>
                                                    <div v-if="brand.deleted_at">
                                                        <a class="action-btn btn-restore" href="#" @click.prevent="restoreBrand(brand.id)">
                                                            <i class="fa-solid fa-trash-restore me-2"></i>Restore
                                                        </a>
                                                    </div>
                                                    <div v-if="brand.deleted_at" class="ms-3">
                                                        <a class="action-btn btn-danger" href="#" @click.prevent="forceDeleteBrand(brand.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Permanent Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div v-if="displayedBrands.length === 0 && !loading" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No brands found</h5>
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
                                            <span v-if="totalFilteredRecords !== totalBrandCount">
                                                (filtered from @{{ totalBrandCount }} total entries)
                                            </span>
                                        </div>
                                        <nav aria-label="Brand pagination">
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="brandOffcanvas" aria-labelledby="brandOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="brandOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Brand</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="currentBrand">
                    <form @submit.prevent="saveBrand" enctype="multipart/form-data">
                        <div>
                            <div v-if="!brandImageSrc" class="d-flex justify-content-lg-start align-content-start">
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
                            <div v-if="brandImageSrc" class="text-center">
                                <h6 class="text-muted mb-4 d-flex fw-bold">Brand Image</h6>
                                <div class="brand-image-container">
                                    <img :src="brandImageSrc" alt="Brand Image" class="brand-image" style="width: 100px; height: 100px; object-fit: cover;">
                                    <button type="button" class="remove-btn" @click="removePhoto" title="Remove image">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" v-model="currentBrand.name" required maxlength="255">
                                <span v-if="formErrors.name" class="text-danger small">@{{ formErrors.name }}</span>
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

            <!-- View Brand Details Offcanvas -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="view-brand-offcanvas" aria-labelledby="viewBrandLabel">
                <div class="offcanvas-header">
                    <div class="w-100 d-flex justify-content-between align-items-start">
                        <div v-if="viewBrand">
                            <h5 class="mb-0">@{{ viewBrand.name }}</h5>
                            <small class="text-white badge bg-secondary" v-if="viewBrand.deleted_at">Deleted</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="viewBrand">
                    <div class="text-center mb-4">
                        <img :src="viewBrand.image ? '/storage/' + viewBrand.image : 'https://via.placeholder.com/100'"
                             alt="Brand Image" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover;">
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">ID</div>
                            <div class="fw-semibold">@{{ viewBrand.id }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Name</div>
                            <div class="fw-semibold">@{{ viewBrand.name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Created At</div>
                            <div class="fw-semibold">@{{ formatDate(viewBrand.created_at) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Updated At</div>
                            <div class="fw-semibold">@{{ formatDate(viewBrand.updated_at) }}</div>
                        </div>
                        <div class="mb-3" v-if="viewBrand.deleted_at">
                            <div class="text-muted small">Deleted At</div>
                            <div class="fw-semibold">@{{ formatDate(viewBrand.deleted_at) }}</div>
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
                    brandList: [],
                    api_url: 'http://127.0.0.1:8000',
                    filteredBrandList: [],
                    currentBrand: null,
                    viewBrand: null,
                    isEditing: false,
                    loading: true,
                    saving: false,
                    errorMessage: null,
                    selectedFile: null,
                    brandImageSrc: null,
                    isDragOver: false,
                    searchQuery: '',
                    searchDebounceTimer: null,
                    currentPage: 1,
                    pageSize: 5,
                    totalBrandCount: 0,
                    formErrors: {},
                    removeImage: false,
                    showDeleted: false
                };
            },
            async mounted() {
                console.log('Mounting Vue app...');
                try {
                    await this.loadBrands();
                    console.log('Brand List:', this.brandList);
                    console.log('Filtered Brand List:', this.filteredBrandList);
                    console.log('Displayed Brands:', this.displayedBrands);
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
                    return this.filteredBrandList.length;
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
                displayedBrands() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    const end = start + this.pageSize;
                    const brands = this.filteredBrandList.slice(start, end);
                    console.log('Computing displayedBrands:', brands);
                    return brands;
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
                filteredBrandList() {
                    this.currentPage = 1;
                    console.log('filteredBrandList changed:', this.filteredBrandList);
                },
                searchQuery() {
                    this.performSearch();
                    console.log('Search Query:', this.searchQuery);
                },
                showDeleted() {
                    this.loadBrands();
                    console.log('Show Deleted toggled:', this.showDeleted);
                }
            },
            methods: {
                async loadBrands() {
                    try {
                        this.loading = true;
                        this.errorMessage = null;
                        console.log('Fetching brand data with showDeleted:', this.showDeleted);
                        const response = await axios.get(`${this.api_url}/api/brand`, {
                            params: { with_deleted: this.showDeleted ? 1 : 0 }
                        });
                        console.log('API Response:', response.data);
                        this.brandList = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        this.totalBrandCount = this.brandList.length;
                        this.filteredBrandList = [...this.brandList];
                        this.executeSearch();
                        console.log('After loadBrands - brandList:', this.brandList, 'totalBrandCount:', this.totalBrandCount);
                    } catch (error) {
                        console.error('Error loading brands:', error.response ? error.response.data : error.message);
                        this.errorMessage = this.getErrorMessage(error, 'Failed to load brand data');
                        this.brandList = [];
                        this.filteredBrandList = [];
                    } finally {
                        this.loading = false;
                    }
                },
                performSearch() {
                    if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                    this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
                },
                executeSearch() {
                    let filtered = [...this.brandList];
                    if (this.searchQuery.trim()) {
                        const query = this.searchQuery.toLowerCase().trim();
                        filtered = filtered.filter(brand =>
                            (brand.name && brand.name.toLowerCase().includes(query))
                        );
                    }
                    this.filteredBrandList = filtered;
                    console.log('After executeSearch - filteredBrandList:', this.filteredBrandList);
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
                    console.log('Page Size changed:', this.pageSize);
                },
                toggleShowDeleted() {
                    this.showDeleted = !this.showDeleted;
                },
                openAddModal() {
                    this.isEditing = false;
                    this.currentBrand = { name: '' };
                    this.selectedFile = null;
                    this.brandImageSrc = null;
                    this.removeImage = false;
                    this.formErrors = {};
                    this.showOffcanvas('brandOffcanvas');
                },
                openEditModal(brand) {
                    this.isEditing = true;
                    this.currentBrand = { ...brand };
                    this.selectedFile = null;
                    this.brandImageSrc = brand.image ? `${this.api_url}/storage/${brand.image}` : null;
                    this.removeImage = false;
                    this.formErrors = {};
                    console.log('Editing Brand:', this.currentBrand);
                    this.showOffcanvas('brandOffcanvas');
                },
                viewBrandDetails(brand) {
                    this.viewBrand = { ...brand };
                    this.showOffcanvas('view-brand-offcanvas');
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
                        this.brandImageSrc = URL.createObjectURL(file);
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
                removePhoto() {
                    this.brandImageSrc = null;
                    this.selectedFile = null;
                    this.removeImage = true;
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                triggerFileInput() {
                    this.$refs.fileInput.click();
                },
                validateForm() {
                    this.formErrors = {};
                    if (!this.currentBrand.name || this.currentBrand.name.trim().length === 0) {
                        this.formErrors.name = 'Name is required';
                    }
                    console.log('Validation Errors:', this.formErrors);
                    return Object.keys(this.formErrors).length === 0;
                },
                async saveBrand() {
                    if (!this.validateForm()) {
                        console.log('Validation failed:', this.formErrors);
                        return;
                    }
                    try {
                        this.saving = true;
                        const formData = new FormData();
                        formData.append('name', this.currentBrand.name || '');
                        if (this.selectedFile) {
                            formData.append('image', this.selectedFile);
                        }
                        if (this.removeImage) {
                            formData.append('remove_image', '1');
                        }
                        console.log('FormData:', [...formData.entries()]);
                        let response;
                        if (this.isEditing) {
                            formData.append('_method', 'PUT');
                            console.log('Update Request URL:', `${this.api_url}/api/brand/${this.currentBrand.id}`);
                            response = await axios.post(`${this.api_url}/api/brand/${this.currentBrand.id}`, formData, {
                                headers: {
                                    Authorization: `Bearer ${token}`,
                                    'Content-Type': 'multipart/form-data'
                                }
                            });
                        } else {
                            console.log('Add Request URL:', `${this.api_url}/api/brand`);
                            response = await axios.post(`${this.api_url}/api/brand`, formData, {
                                headers: {
                                    Authorization: `Bearer ${token}`,
                                    'Content-Type': 'multipart/form-data'
                                }
                            });
                        }
                        console.log('Response:', response.data);
                        this.hideOffcanvas('brandOffcanvas');
                        Swal.fire({
                            icon: 'success',
                            title: this.isEditing ? 'Brand updated successfully!' : 'Brand added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        this.resetForm();
                        await this.loadBrands();
                    } catch (error) {
                        console.error('Save brand error:', {
                            message: error.message,
                            response: error.response ? error.response.data : null,
                            status: error.response ? error.response.status : null
                        });
                        const errors = error.response?.data?.errors;
                        if (errors) {
                            this.formErrors = errors;
                            this.errorMessage = 'Please fix the errors in the form';
                        } else {
                            this.errorMessage = error.response?.data?.message || 'Failed to save brand';
                        }
                    } finally {
                        this.saving = false;
                    }
                },
                resetForm() {
                    this.currentBrand = { name: '' };
                    this.selectedFile = null;
                    this.brandImageSrc = null;
                    this.removeImage = false;
                    this.isEditing = false;
                    this.formErrors = {};
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                async deleteBrand(brandId) {
                    if (!brandId) return;
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This will soft delete the brand. You can restore it later.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/brand/${brandId}`, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadBrands();
                            await Swal.fire(
                                'Deleted!',
                                'Brand has been soft deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error deleting brand:', error);
                            this.errorMessage = 'Failed to delete brand';
                            await Swal.fire(
                                'Error!',
                                'Failed to delete brand.',
                                'error'
                            );
                        }
                    }
                },
                async restoreBrand(brandId) {
                    if (!brandId) return;
                    const result = await Swal.fire({
                        title: 'Restore Brand?',
                        text: "This will restore the brand to active status.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, restore it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.post(`${this.api_url}/api/brand/${brandId}/restore`, {}, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadBrands();
                            await Swal.fire(
                                'Restored!',
                                'Brand has been restored.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error restoring brand:', error);
                            this.errorMessage = 'Failed to restore brand';
                            await Swal.fire(
                                'Error!',
                                'Failed to restore brand.',
                                'error'
                            );
                        }
                    }
                },
                async forceDeleteBrand(brandId) {
                    if (!brandId) return;
                    const result = await Swal.fire({
                        title: 'Permanently Delete Brand?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, permanently delete!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/brand/${brandId}/force`, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadBrands();
                            await Swal.fire(
                                'Permanently Deleted!',
                                'Brand has been permanently deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error force deleting brand:', error);
                            this.errorMessage = 'Failed to permanently delete brand';
                            await Swal.fire(
                                'Error!',
                                'Failed to permanently delete brand.',
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
