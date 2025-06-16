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
                    <h3 class="fw-bold mb-3">Category List</h3>
                </div>
                <div>
                    <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Category">
                        <i class="fa-solid fa-plus fa-lg"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-round">
                        <div class="card-header">
                            <div class="card-head-row">
                                <div class="card-title">All Categories</div>
                                <div class="card-tools">
                                    <div class="row justify-content-end align-items-center g-2">
                                        <div class="col-auto">
                                            <input type="text" class="form-control" placeholder="Search categories..."
                                                   v-model="searchQuery" @input="performSearch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-stats mt-2" v-if="searchActive">
                                Showing @{{ displayedCategories.length }} of @{{ totalCategoryCount }} categories
                                <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading category data...</p>
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
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(category, index) in displayedCategories" :key="category.id">
                                            <td @click="viewCategoryDetails(category)" style="cursor: pointer;">@{{ index + 1 }}</td>
                                            <td @click="viewCategoryDetails(category)" style="cursor: pointer;" v-html="highlightText(category.name)"></td>
                                            <td @click="viewCategoryDetails(category)" style="cursor: pointer;" v-html="highlightText(category.description || '-')"></td>
                                            <td @click="viewCategoryDetails(category)" style="cursor: pointer;">@{{ formatDate(category.created_at) }}</td>
                                            <td @click="viewCategoryDetails(category)" style="cursor: pointer;">
                                                <span :class="{'text-danger': category.deleted_at, 'text-success': !category.deleted_at}">
                                                    @{{ category.deleted_at ? 'Deleted' : 'Active' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="me-3" v-if="!category.deleted_at">
                                                        <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(category)">
                                                            <i class="fa-solid fa-pen me-2"></i>Edit
                                                        </a>
                                                    </div>
                                                    <div v-if="!category.deleted_at">
                                                        <a class="action-btn btn-delete" href="#" @click.prevent="deleteCategory(category.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                                        </a>
                                                    </div>
                                                    <div v-if="category.deleted_at">
                                                        <a class="action-btn btn-restore" href="#" @click.prevent="restoreCategory(category.id)">
                                                            <i class="fa-solid fa-trash-restore me-2"></i>Restore
                                                        </a>
                                                    </div>
                                                    <div v-if="category.deleted_at" class="ms-3">
                                                        <a class="action-btn btn-danger" href="#" @click.prevent="forceDeleteCategory(category.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Permanent Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div v-if="displayedCategories.length === 0 && !loading" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No categories found</h5>
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
                                            <span v-if="totalFilteredRecords !== totalCategoryCount">
                                                (filtered from @{{ totalCategoryCount }} total entries)
                                            </span>
                                        </div>
                                        <nav aria-label="Category pagination">
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="categoryOffcanvas" aria-labelledby="categoryOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="categoryOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Category</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="currentCategory">
                    <form @submit.prevent="saveCategory">
                        <div>
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" v-model="currentCategory.name" required maxlength="255">
                                <span v-if="formErrors.name" class="text-danger small">@{{ formErrors.name }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" v-model="currentCategory.description" maxlength="255" rows="3"></textarea>
                                <span v-if="formErrors.description" class="text-danger small">@{{ formErrors.description }}</span>
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

            <!-- View Category Details Offcanvas -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="view-category-offcanvas" aria-labelledby="viewCategoryLabel">
                <div class="offcanvas-header">
                    <div class="w-100 d-flex justify-content-between align-items-start">
                        <div v-if="viewCategory">
                            <h5 class="mb-0">@{{ viewCategory.name }}</h5>
                            <small class="text-white badge bg-secondary" v-if="viewCategory.deleted_at">Deleted</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="viewCategory">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">ID</div>
                            <div class="fw-semibold">@{{ viewCategory.id }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Name</div>
                            <div class="fw-semibold">@{{ viewCategory.name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Description</div>
                            <div class="fw-semibold">@{{ viewCategory.description || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Created At</div>
                            <div class="fw-semibold">@{{ formatDate(viewCategory.created_at) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Updated At</div>
                            <div class="fw-semibold">@{{ formatDate(viewCategory.updated_at) }}</div>
                        </div>
                        <div class="mb-3" v-if="viewCategory.deleted_at">
                            <div class="text-muted small">Deleted At</div>
                            <div class="fw-semibold">@{{ formatDate(viewCategory.deleted_at) }}</div>
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
                    categoryList: [],
                    api_url: 'http://127.0.0.1:8000',
                    filteredCategoryList: [],
                    currentCategory: null,
                    viewCategory: null,
                    isEditing: false,
                    loading: true,
                    saving: false,
                    errorMessage: null,
                    searchQuery: '',
                    searchDebounceTimer: null,
                    currentPage: 1,
                    pageSize: 5,
                    totalCategoryCount: 0,
                    formErrors: {},
                    showDeleted: false
                };
            },
            async mounted() {
                console.log('Mounting Vue.js app...');
                try {
                    await this.loadCategories();
                    console.log('Category List:', this.categoryList);
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
                    return this.filteredCategoryList.length;
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
                displayedCategories() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    const end = start + this.pageSize;
                    return this.filteredCategoryList.slice(start, end);
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
                filteredCategoryList() {
                    this.currentPage = 1;
                },
                searchQuery() {
                    this.performSearch();
                },
                showDeleted() {
                    this.loadCategories();
                }
            },
            methods: {
                async loadCategories() {
                    try {
                        this.loading = true;
                        this.errorMessage = null;
                        console.log('Fetching category data with showDeleted:', this.showDeleted);
                        const response = await axios.get(`${this.api_url}/api/categories`, {
                            params: { with_deleted: this.showDeleted ? 1 : 0 }
                        });
                        console.log('API Response:', response.data);
                        this.categoryList = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        this.totalCategoryCount = this.categoryList.length;
                        this.filteredCategoryList = [...this.categoryList];
                        this.executeSearch();
                        console.log('After loadCategories - categoryList:', this.categoryList, 'totalCategoryCount:', this.totalCategoryCount);
                    } catch (error) {
                        console.error('Error loading categories:', error.response ? error.response.data : error.message);
                        this.errorMessage = this.getErrorMessage(error, 'Failed to load category data');
                        this.categoryList = [];
                        this.filteredCategoryList = [];
                    } finally {
                        this.loading = false;
                    }
                },
                performSearch() {
                    if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                    this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
                },
                executeSearch() {
                    let filtered = [...this.categoryList];
                    if (this.searchQuery.trim()) {
                        const query = this.searchQuery.toLowerCase().trim();
                        filtered = filtered.filter(category =>
                            (category.name && category.name.toLowerCase().includes(query)) ||
                            (category.description && category.description.toLowerCase().includes(query))
                        );
                    }
                    this.filteredCategoryList = filtered;
                    console.log('After executeSearch - filteredCategoryList:', this.filteredCategoryList);
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
                    this.currentCategory = { name: '', description: '' };
                    this.formErrors = {};
                    this.showOffcanvas('categoryOffcanvas');
                },
                openEditModal(category) {
                    this.isEditing = true;
                    this.currentCategory = { ...category };
                    this.formErrors = {};
                    console.log('Editing Category:', this.currentCategory);
                    this.showOffcanvas('categoryOffcanvas');
                },
                viewCategoryDetails(category) {
                    this.viewCategory = { ...category };
                    this.showOffcanvas('view-category-offcanvas');
                },
                validateForm() {
                    this.formErrors = {};
                    if (!this.currentCategory.name || this.currentCategory.name.trim().length === 0) {
                        this.formErrors.name = 'Name is required';
                    }
                    if (this.currentCategory.description && this.currentCategory.description.length > 255) {
                        this.formErrors.description = 'Description must be less than 255 characters';
                    }
                    console.log('Validation Errors:', this.formErrors);
                    return Object.keys(this.formErrors).length === 0;
                },
                async saveCategory() {
                    if (!this.validateForm()) {
                        console.log('Validation failed:', this.formErrors);
                        return;
                    }
                    try {
                        this.saving = true;
                        const data = {
                            name: this.currentCategory.name || '',
                            description: this.currentCategory.description || ''
                        };
                        console.log('Form Data:', data);
                        let response;
                        if (this.isEditing) {
                            response = await axios.put(`${this.api_url}/api/categories/${this.currentCategory.id}`, data, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                        } else {
                            response = await axios.post(`${this.api_url}/api/categories`, data, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                        }
                        console.log('Response:', response.data);
                        this.hideOffcanvas('categoryOffcanvas');
                        Swal.fire({
                            icon: 'success',
                            title: this.isEditing ? 'Category updated successfully!' : 'Category added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        this.resetForm();
                        await this.loadCategories();
                    } catch (error) {
                        console.error('Save category error:', {
                            message: error.message,
                            response: error.response ? error.response.data : null,
                            status: error.response ? error.response.status : null
                        });
                        const errors = error.response?.data?.errors;
                        if (errors) {
                            this.formErrors = errors;
                            this.errorMessage = 'Please fix the errors in the form';
                        } else {
                            this.errorMessage = error.response?.data?.message || 'Failed to save category';
                        }
                    } finally {
                        this.saving = false;
                    }
                },
                resetForm() {
                    this.currentCategory = { name: '', description: '' };
                    this.isEditing = false;
                    this.formErrors = {};
                },
                async deleteCategory(categoryId) {
                    if (!categoryId) return;
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This will soft delete the category. You can restore it later.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/categories/${categoryId}`, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadCategories();
                            await Swal.fire(
                                'Deleted!',
                                'Category has been soft deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error deleting category:', error);
                            this.errorMessage = 'Failed to delete category';
                            await Swal.fire(
                                'Error!',
                                'Failed to delete category.',
                                'error'
                            );
                        }
                    }
                },
                async restoreCategory(categoryId) {
                    if (!categoryId) return;
                    const result = await Swal.fire({
                        title: 'Restore Category?',
                        text: "This will restore the category to active status.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, restore it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.post(`${this.api_url}/api/categories/${categoryId}/restore`, {}, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadCategories();
                            await Swal.fire(
                                'Restored!',
                                'Category has been restored.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error restoring category:', error);
                            this.errorMessage = 'Failed to restore category';
                            await Swal.fire(
                                'Error!',
                                'Failed to restore category.',
                                'error'
                            );
                        }
                    }
                },
                async forceDeleteCategory(categoryId) {
                    if (!categoryId) return;
                    const result = await Swal.fire({
                        title: 'Permanently Delete Category?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, permanently delete!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/categories/${categoryId}/force`, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadCategories();
                            await Swal.fire(
                                'Permanently Deleted!',
                                'Category has been permanently deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error force deleting category:', error);
                            this.errorMessage = 'Failed to permanently delete category';
                            await Swal.fire(
                                'Error!',
                                'Failed to permanently delete category.',
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
