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
                    <h3 class="fw-bold mb-3">Product List</h3>
                </div>
                <div>
                    <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Product">
                        <i class="fa-solid fa-plus fa-lg"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-round">
                        <div class="card-header">
                            <div class="card-head-row">
                                <div class="card-title">All Products</div>
                                <div class="card-tools">
                                    <div class="row justify-content-end align-items-center g-2">
                                        <div class="col-auto">
                                            <select class="form-select form-select-sm" v-model="selectedBrandFilter" @change="performSearch">
                                                <option value="">All Brands</option>
                                                <option v-for="brand in availableBrands" :key="brand.id" :value="brand.id">
                                                    @{{ brand.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <input type="text" class="form-control" placeholder="Search products..."
                                                   v-model="searchQuery" @input="performSearch">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-stats mt-2" v-if="searchActive">
                                Showing @{{ displayedProducts.length }} of @{{ totalProductCount }} products
                                <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading product data...</p>
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
                                            <th>Description</th>
                                            <th>Cost</th>
                                            <th>Price</th>
                                            <th>In Stock</th>
                                            <th>Category</th>
                                            <th>Brand</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(product, index) in displayedProducts" :key="product.id">
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;">@{{ index + 1 }}</td>
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;">
                                                <img :src="product.image ? api_url+'/storage/'+product.image : 'https://via.placeholder.com/50'"
                                                     alt="Product Image" class="product-img rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;" v-html="highlightText(product.name)"></td>
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;" v-html="highlightText(product.description || '-')"></td>
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;">$@{{ product.cost }}</td>
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;">$@{{ product.price }}</td>
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;">@{{ product.inStock }}</td>
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;" v-html="highlightText(product.categories ? product.categories.name : 'N/A')"></td>
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;" v-html="highlightText(product.brands ? product.brands.name : 'N/A')"></td>
                                            <td @click="viewProductDetails(product)" style="cursor: pointer;">
                                                <span :class="{'text-danger': product.deleted_at, 'text-success': !product.deleted_at}">
                                                    @{{ product.deleted_at ? 'Deleted' : 'Active' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="me-3" v-if="!product.deleted_at">
                                                        <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(product)">
                                                            <i class="fa-solid fa-pen me-2"></i>Edit
                                                        </a>
                                                    </div>
                                                    <div v-if="!product.deleted_at">
                                                        <a class="action-btn btn-delete" href="#" @click.prevent="deleteProduct(product.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                                        </a>
                                                    </div>
                                                    <div v-if="product.deleted_at">
                                                        <a class="action-btn btn-restore" href="#" @click.prevent="restoreProduct(product.id)">
                                                            <i class="fa-solid fa-trash-restore me-2"></i>Restore
                                                        </a>
                                                    </div>
                                                    <div v-if="product.deleted_at" class="ms-3">
                                                        <a class="action-btn btn-danger" href="#" @click.prevent="forceDeleteProduct(product.id)">
                                                            <i class="fa-solid fa-trash me-2"></i>Permanent Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div v-if="displayedProducts.length === 0 && !loading" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No products found</h5>
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
                                            <span v-if="totalFilteredRecords !== totalProductCount">
                                                (filtered from @{{ totalProductCount }} total entries)
                                            </span>
                                        </div>
                                        <nav aria-label="Product pagination">
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="productOffcanvas" aria-labelledby="productOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="productOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Product</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="currentProduct">
                    <form @submit.prevent="saveProduct" enctype="multipart/form-data">
                        <div>
                            <div v-if="!productImageSrc" class="d-flex justify-content-lg-start align-content-start">
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
                            <div v-if="productImageSrc" class="text-center">
                                <h6 class="text-muted mb-4 d-flex fw-bold">Product Image</h6>
                                <div class="product-image-container">
                                    <img :src="productImageSrc" alt="Product Image" class="product-image" style="width: 100px; height: 100px; object-fit: cover;">
                                    <button type="button" class="remove-btn" @click="removePhoto" title="Remove image">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" v-model="currentProduct.name" required maxlength="255">
                                <span v-if="formErrors.name" class="text-danger small">@{{ formErrors.name }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" v-model="currentProduct.description" maxlength="255" rows="3"></textarea>
                                <span v-if="formErrors.description" class="text-danger small">@{{ formErrors.description }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Cost *</label>
                                <input type="number" class="form-control" v-model="currentProduct.cost" required min="0" step="0.01">
                                <span v-if="formErrors.cost" class="text-danger small">@{{ formErrors.cost }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price *</label>
                                <input type="number" class="form-control" v-model="currentProduct.price" required min="0" step="0.01">
                                <span v-if="formErrors.price" class="text-danger small">@{{ formErrors.price }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">In Stock *</label>
                                <input type="number" class="form-control" v-model="currentProduct.inStock" required min="0">
                                <span v-if="formErrors.inStock" class="text-danger small">@{{ formErrors.inStock }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Product Details ID</label>
                                <input type="number" class="form-control" v-model="currentProduct.product_details_id">
                                <span v-if="formErrors.product_details_id" class="text-danger small">@{{ formErrors.product_details_id }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" v-model="currentProduct.categories_id" required>
                                    <option value="">Select Category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        @{{ category.name }}
                                    </option>
                                </select>
                                <span v-if="formErrors.categories_id" class="text-danger small">@{{ formErrors.categories_id }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Brand *</label>
                                <select class="form-select" v-model="currentProduct.brands_id" required>
                                    <option value="">Select Brand</option>
                                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                        @{{ brand.name }}
                                    </option>
                                </select>
                                <span v-if="formErrors.brands_id" class="text-danger small">@{{ formErrors.brands_id }}</span>
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="view-product-offcanvas" aria-labelledby="viewProductLabel">
                <div class="offcanvas-header">
                    <div class="w-100 d-flex justify-content-between align-items-start">
                        <div v-if="viewProduct">
                            <h5 class="mb-0">@{{ viewProduct.name }}</h5>
                            <small class="text-white badge bg-secondary" v-if="viewProduct.deleted_at">Deleted</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" v-if="viewProduct">
                    <div class="text-center mb-4">
                        <img :src="viewProduct.image ? api_url+'/storage/'+viewProduct.image : 'https://via.placeholder.com/100'"
                             alt="Product Image" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover;">
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">ID</div>
                            <div class="fw-semibold">@{{ viewProduct.id }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Name</div>
                            <div class="fw-semibold">@{{ viewProduct.name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Description</div>
                            <div class="fw-semibold">@{{ viewProduct.description || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Cost</div>
                            <div class="fw-semibold">$@{{ viewProduct.cost }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Price</div>
                            <div class="fw-semibold">$@{{ viewProduct.price }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">In Stock</div>
                            <div class="fw-semibold">@{{ viewProduct.inStock }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Product Details ID</div>
                            <div class="fw-semibold">@{{ viewProduct.product_details_id || '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Category</div>
                            <div class="fw-semibold">@{{ viewProduct.categories ? viewProduct.categories.name : 'N/A' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Brand</div>
                            <div class="fw-semibold">@{{ viewProduct.brands ? viewProduct.brands.name : 'N/A' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Created At</div>
                            <div class="fw-semibold">@{{ formatDate(viewProduct.created_at) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Updated At</div>
                            <div class="fw-semibold">@{{ formatDate(viewProduct.updated_at) }}</div>
                        </div>
                        <div class="mb-3" v-if="viewProduct.deleted_at">
                            <div class="text-muted small">Deleted At</div>
                            <div class="fw-semibold">@{{ formatDate(viewProduct.deleted_at) }}</div>
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
                    productList: [],
                    api_url: 'http://127.0.0.1:8000',
                    filteredProductList: [],
                    categories: [],
                    brands: [],
                    currentProduct: null,
                    viewProduct: null,
                    isEditing: false,
                    loading: true,
                    saving: false,
                    errorMessage: null,
                    selectedFile: null,
                    productImageSrc: null,
                    isDragOver: false,
                    searchQuery: '',
                    selectedBrandFilter: '',
                    searchDebounceTimer: null,
                    currentPage: 1,
                    pageSize: 5,
                    totalProductCount: 0,
                    formErrors: {},
                    removeImage: false,
                    showDeleted: false
                };
            },
            async mounted() {
                console.log('Mounting Vue app...');
                try {
                    await Promise.all([this.loadProducts(), this.loadCategories(), this.loadBrands()]);
                    console.log('Product List:', this.productList);
                    console.log('Filtered Product List:', this.filteredProductList);
                    console.log('Displayed Products:', this.displayedProducts);
                } catch (error) {
                    console.error('Error during initialization:', error);
                    this.errorMessage = 'Failed to initialize application. Details: ' + (error.message || 'Unknown error');
                }
            },
            computed: {
                availableBrands() {
                    return this.brands.sort((a, b) => a.name.localeCompare(b.name));
                },
                searchActive() {
                    return this.searchQuery.trim() !== '' || this.selectedBrandFilter !== '';
                },
                totalFilteredRecords() {
                    return this.filteredProductList.length;
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
                displayedProducts() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    const end = start + this.pageSize;
                    const products = this.filteredProductList.slice(start, end);
                    console.log('Computing displayedProducts:', products);
                    return products;
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
                filteredProductList() {
                    this.currentPage = 1;
                    console.log('filteredProductList changed:', this.filteredProductList);
                },
                searchQuery() {
                    this.performSearch();
                    console.log('Search Query:', this.searchQuery);
                },
                selectedBrandFilter() {
                    this.performSearch();
                    console.log('Selected Brand Filter:', this.selectedBrandFilter);
                },
                showDeleted() {
                    this.loadProducts();
                    console.log('Show Deleted toggled:', this.showDeleted);
                }
            },
            methods: {
                async loadProducts() {
                    try {
                        this.loading = true;
                        this.errorMessage = null;
                        console.log('Fetching product data with showDeleted:', this.showDeleted);
                        const response = await axios.get(`${this.api_url}/api/products`, {
                            params: { with_deleted: this.showDeleted ? 1 : 0 }
                        });
                        console.log('API Response:', response.data);
                        this.productList = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        this.totalProductCount = this.productList.length;
                        this.filteredProductList = [...this.productList];
                        this.executeSearch();
                        console.log('After loadProducts - productList:', this.productList, 'totalProductCount:', this.totalProductCount);
                    } catch (error) {
                        console.error('Error loading products:', error.response ? error.response.data : error.message);
                        this.errorMessage = this.getErrorMessage(error, 'Failed to load product data');
                        this.productList = [];
                        this.filteredProductList = [];
                    } finally {
                        this.loading = false;
                    }
                },
                async loadCategories() {
                    try {
                        const response = await axios.get(`${this.api_url}/api/categories`, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                        this.categories = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        console.log('Categories:', this.categories);
                    } catch (error) {
                        console.error('Error loading categories:', error);
                        this.categories = [];
                    }
                },
                async loadBrands() {
                    try {
                        const response = await axios.get(`${this.api_url}/api/brands`, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                        this.brands = Array.isArray(response.data) ? response.data : (response.data.data || []);
                        console.log('Brands:', this.brands);
                    } catch (error) {
                        console.error('Error loading brands:', error);
                        this.brands = [];
                    }
                },
                performSearch() {
                    if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                    this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
                },
                executeSearch() {
                    let filtered = [...this.productList];
                    if (this.searchQuery.trim()) {
                        const query = this.searchQuery.toLowerCase().trim();
                        filtered = filtered.filter(product =>
                            (product.name && product.name.toLowerCase().includes(query)) ||
                            (product.description && product.description.toLowerCase().includes(query)) ||
                            (product.brands && product.brands.name && product.brands.name.toLowerCase().includes(query)) ||
                            (product.categories && product.categories.name && product.categories.name.toLowerCase().includes(query))
                        );
                    }
                    if (this.selectedBrandFilter) {
                        filtered = filtered.filter(product => product.brands_id === parseInt(this.selectedBrandFilter));
                    }
                    this.filteredProductList = filtered;
                    console.log('After executeSearch - filteredProductList:', this.filteredProductList);
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
                    this.currentProduct = { name: '', description: '', cost: '', price: '', inStock: '', product_details_id: '', categories_id: '', brands_id: '' };
                    this.selectedFile = null;
                    this.productImageSrc = null;
                    this.removeImage = false;
                    this.formErrors = {};
                    this.showOffcanvas('productOffcanvas');
                },
                openEditModal(product) {
                    this.isEditing = true;
                    this.currentProduct = {
                        ...product,
                        categories_id: product.categories_id || '',
                        brands_id: product.brands_id || '',
                        cost: product.cost || '',
                        price: product.price || '',
                        inStock: product.inStock || ''
                    };
                    this.selectedFile = null;
                    this.productImageSrc = product.image ? `${this.api_url}/storage/${product.image}` : null;
                    this.removeImage = false;
                    this.formErrors = {};
                    console.log('Editing Product:', this.currentProduct);
                    this.showOffcanvas('productOffcanvas');
                },
                viewProductDetails(product) {
                    this.viewProduct = { ...product };
                    this.showOffcanvas('view-product-offcanvas');
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
                        this.productImageSrc = URL.createObjectURL(file);
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
                    this.productImageSrc = null;
                    this.selectedFile = null;
                    this.removeImage = true;
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                triggerFileInput() {
                    this.$refs.fileInput.click();
                },
                validateForm() {
                    this.formErrors = {};
                    if (!this.currentProduct.name || this.currentProduct.name.trim().length === 0)
                        this.formErrors.name = 'Name is required';
                    if (this.currentProduct.description && this.currentProduct.description.length > 255)
                        this.formErrors.description = 'Description must be less than 255 characters';
                    if (!this.currentProduct.cost || parseFloat(this.currentProduct.cost) <= 0)
                        this.formErrors.cost = 'Valid cost is required';
                    if (!this.currentProduct.price || parseFloat(this.currentProduct.price) <= 0)
                        this.formErrors.price = 'Valid price is required';
                    if (this.currentProduct.inStock === '' || parseInt(this.currentProduct.inStock) < 0)
                        this.formErrors.inStock = 'Valid stock quantity is required';
                    if (!this.currentProduct.categories_id)
                        this.formErrors.categories_id = 'Category is required';
                    if (!this.currentProduct.brands_id)
                        this.formErrors.brands_id = 'Brand is required';
                    console.log('Validation Errors:', this.formErrors);
                    return Object.keys(this.formErrors).length === 0;
                },
                async saveProduct() {
                    if (!this.validateForm()) {
                        console.log('Validation failed:', this.formErrors);
                        return;
                    }
                    try {
                        this.saving = true;
                        const formData = new FormData();
                        const fields = ['name', 'description', 'cost', 'price', 'inStock', 'product_details_id', 'categories_id', 'brands_id'];
                        fields.forEach(key => {
                            formData.append(key, this.currentProduct[key] || '');
                        });
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
                            console.log('Update Request URL:', `${this.api_url}/api/products/${this.currentProduct.id}`);
                            response = await axios.post(`${this.api_url}/api/products/${this.currentProduct.id}`, formData, {
                                headers: {
                                    Authorization: `Bearer ${token}`,
                                    'Content-Type': 'multipart/form-data'
                                }
                            });
                        } else {
                            console.log('Add Request URL:', `${this.api_url}/api/products`);
                            response = await axios.post(`${this.api_url}/api/products`, formData, {
                                headers: {
                                    Authorization: `Bearer ${token}`,
                                    'Content-Type': 'multipart/form-data'
                                }
                            });
                        }
                        console.log('Response:', response.data);
                        this.hideOffcanvas('productOffcanvas');
                        Swal.fire({
                            icon: 'success',
                            title: this.isEditing ? 'Product updated successfully!' : 'Product added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        this.resetForm();
                        await this.loadProducts();
                    } catch (error) {
                        console.error('Save product error:', {
                            message: error.message,
                            response: error.response ? error.response.data : null,
                            status: error.response ? error.response.status : null
                        });
                        const errors = error.response?.data?.errors;
                        if (errors) {
                            this.formErrors = errors;
                            this.errorMessage = 'Please fix the errors in the form';
                        } else {
                            this.errorMessage = error.response?.data?.message || 'Failed to save product';
                        }
                    } finally {
                        this.saving = false;
                    }
                },
                resetForm() {
                    this.currentProduct = { name: '', description: '', cost: '', price: '', inStock: '', product_details_id: '', categories_id: '', brands_id: '' };
                    this.selectedFile = null;
                    this.productImageSrc = null;
                    this.removeImage = false;
                    this.isEditing = false;
                    this.formErrors = {};
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                async deleteProduct(productId) {
                    if (!productId) return;
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This will soft delete the product. You can restore it later.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/products/${productId}`, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadProducts();
                            await Swal.fire(
                                'Deleted!',
                                'Product has been soft deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error deleting product:', error);
                            this.errorMessage = 'Failed to delete product';
                            await Swal.fire(
                                'Error!',
                                'Failed to delete product.',
                                'error'
                            );
                        }
                    }
                },
                async restoreProduct(productId) {
                    if (!productId) return;
                    const result = await Swal.fire({
                        title: 'Restore Product?',
                        text: "This will restore the product to active status.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, restore it!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.post(`${this.api_url}/api/products/${productId}/restore`, {}, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadProducts();
                            await Swal.fire(
                                'Restored!',
                                'Product has been restored.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error restoring product:', error);
                            this.errorMessage = 'Failed to restore product';
                            await Swal.fire(
                                'Error!',
                                'Failed to restore product.',
                                'error'
                            );
                        }
                    }
                },
                async forceDeleteProduct(productId) {
                    if (!productId) return;
                    const result = await Swal.fire({
                        title: 'Permanently Delete Product?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, permanently delete!'
                    });
                    if (result.isConfirmed) {
                        try {
                            await axios.delete(`${this.api_url}/api/products/${productId}/force`, {
                                headers: { Authorization: `Bearer ${token}` }
                            });
                            await this.loadProducts();
                            await Swal.fire(
                                'Permanently Deleted!',
                                'Product has been permanently deleted.',
                                'success'
                            );
                        } catch (error) {
                            console.error('Error force deleting product:', error);
                            this.errorMessage = 'Failed to permanently delete product';
                            await Swal.fire(
                                'Error!',
                                'Failed to permanently delete product.',
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
