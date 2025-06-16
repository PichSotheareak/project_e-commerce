@extends('admin.master')
@section('content')
<div id="app">
    <div class="container-fluid mt-4">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
            <div>
                <h3 class="fw-bold mb-3">Order List</h3>
            </div>
            <div>
                <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Order">
                    <i class="fa-solid fa-plus fa-lg"></i>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">Order Management</div>
                            <div class="card-tools">
                                <div class="row justify-content-end align-items-center g-2">
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm" v-model="selectedStatusFilter" @change="performSearch">
                                            <option value="">All Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="processing">Processing</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm" v-model="selectedPaymentStatusFilter" @change="performSearch">
                                            <option value="">All Payment Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="processing">Processing</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <input type="text" class="form-control" placeholder="Search orders..."
                                               v-model="searchQuery" @input="performSearch">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search-stats mt-2" v-if="searchActive">
                            Showing @{{ displayedOrders.length }} of @{{ totalOrderCount }} orders
                            <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="loading" class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading order data...</p>
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
                                        <th>Customer</th>
                                        <th>User</th>
                                        <th>Branch</th>
                                        <th>Order Date</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(order, index) in displayedOrders" :key="order.id">
                                        <td @click="viewOrderDetails(order)" style="cursor: pointer;">@{{ order.id }}</td>
                                        <td @click="viewOrderDetails(order)" style="cursor: pointer;" v-html="highlightText(order.customers ? order.customers.name : 'N/A')"></td>
                                        <td @click="viewOrderDetails(order)" style="cursor: pointer;" v-html="highlightText(order.users ? order.users.name : 'N/A')"></td>
                                        <td @click="viewOrderDetails(order)" style="cursor: pointer;" v-html="highlightText(order.branches ? order.branches.name : 'N/A')"></td>
                                        <td @click="viewOrderDetails(order)" style="cursor: pointer;">@{{ formatDate(order.order_date) }}</td>
                                        <td @click="viewOrderDetails(order)" style="cursor: pointer;">@{{ parseFloat(order.total_amount).toFixed(2) }}</td>
                                        <td @click="viewOrderDetails(order)" style="cursor: pointer;">
                                                <span class="status-badge" :class="'status-' + order.status">
                                                    @{{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                                                </span>
                                        </td>
                                        <td @click="viewOrderDetails(order)" style="cursor: pointer;">
                                                <span class="status-badge" :class="'status-' + order.payment_status">
                                                    @{{ order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) }}
                                                </span>
                                        </td>
                                        <td @click="viewOrderDetails(order)" style="cursor: pointer;">
                                            @{{ order.remarks ? (order.remarks.length > 20 ? order.remarks.substring(0, 20) + '...' : order.remarks) : 'N/A' }}
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(order)">
                                                        <i class="fa-solid fa-pen me-2"></i>Edit
                                                    </a>
                                                </div>
                                                <div>
                                                    <a class="action-btn btn-delete" href="#" @click.prevent="deleteOrder(order.id)">
                                                        <i class="fa-solid fa-trash me-2"></i>Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div v-if="displayedOrders.length === 0 && !loading" class="text-center py-4">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No orders found</h5>
                                    <p class="text-muted">Try adjusting your search criteria</p>
                                </div>
                            </div>

                            <!-- Pagination -->
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
                                        <span v-if="totalFilteredRecords !== totalOrderCount">
                                                (filtered from @{{ totalOrderCount }} total entries)
                                            </span>
                                    </div>
                                    <nav aria-label="Order pagination">
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
        <div class="offcanvas offcanvas-end" tabindex="-1" id="orderOffcanvas" aria-labelledby="orderOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 id="orderOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Order</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" v-if="currentOrder">
                <form @submit.prevent="saveOrder">
                    <div class="mb-3">
                        <label class="form-label">Customer *</label>
                        <select class="form-select" v-model="currentOrder.customers_id" required>
                            <option value="">Select Customer</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                @{{ customer.name }}
                            </option>
                        </select>
                        <span v-if="formErrors.customers_id" class="text-danger small">@{{ formErrors.customers_id[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User *</label>
                        <select class="form-select" v-model="currentOrder.users_id" required>
                            <option value="">Select User</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                @{{ user.name }}
                            </option>
                        </select>
                        <span v-if="formErrors.users_id" class="text-danger small">@{{ formErrors.users_id[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Branch *</label>
                        <select class="form-select" v-model="currentOrder.branches_id" required>
                            <option value="">Select Branch</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                @{{ branch.name }}
                            </option>
                        </select>
                        <span v-if="formErrors.branches_id" class="text-danger small">@{{ formErrors.branches_id[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order Date *</label>
                        <input type="date" class="form-control" v-model="currentOrder.order_date" required>
                        <span v-if="formErrors.order_date" class="text-danger small">@{{ formErrors.order_date[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Amount *</label>
                        <input type="number" class="form-control" v-model="currentOrder.total_amount" required min="0" step="0.01">
                        <span v-if="formErrors.total_amount" class="text-danger small">@{{ formErrors.total_amount[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" v-model="currentOrder.status">
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <span v-if="formErrors.status" class="text-danger small">@{{ formErrors.status[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select class="form-select" v-model="currentOrder.payment_status">
                            <option value="">Select Payment Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <span v-if="formErrors.payment_status" class="text-danger small">@{{ formErrors.payment_status[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" v-model="currentOrder.remarks" rows="3" placeholder="Enter order remarks (minimum 50 characters)"></textarea>
                        <span v-if="formErrors.remarks" class="text-danger small">@{{ formErrors.remarks[0] }}</span>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="saving">
                            <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            @{{ isEditing ? 'Update' : 'Add' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Details Offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="viewOffcanvas" aria-labelledby="viewOffcanvasLabel">
            <div class="offcanvas-header">
                <div class="w-100 d-flex justify-content-between align-items-start">
                    <div v-if="viewOrder">
                        <h5 class="mb-0">Order #@{{ viewOrder.id }}</h5>
                        <small class="text-white badge" :class="'bg-' + getStatusColor(viewOrder.status)">@{{ viewOrder.status.charAt(0).toUpperCase() + viewOrder.status.slice(1) }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" v-if="viewOrder">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Customer</div>
                        <div class="fw-semibold">@{{ viewOrder.customers ? viewOrder.customers.name : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">User</div>
                        <div class="fw-semibold">@{{ viewOrder.users ? viewOrder.users.name : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Branch</div>
                        <div class="fw-semibold">@{{ viewOrder.branches ? viewOrder.branches.name : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Order Date</div>
                        <div class="fw-semibold">@{{ formatDate(viewOrder.order_date) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Total Amount</div>
                        <div class="fw-semibold">@{{ parseFloat(viewOrder.total_amount).toFixed(2) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Status</div>
                        <div class="fw-semibold">
                                <span class="status-badge" :class="'status-' + viewOrder.status">
                                    @{{ viewOrder.status.charAt(0).toUpperCase() + viewOrder.status.slice(1) }}
                                </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Payment Status</div>
                        <div class="fw-semibold">
                                <span class="status-badge" :class="'status-' + viewOrder.payment_status">
                                    @{{ viewOrder.payment_status.charAt(0).toUpperCase() + viewOrder.payment_status.slice(1) }}
                                </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Remarks</div>
                        <div class="fw-semibold">@{{ viewOrder.remarks || 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Created Date</div>
                        <div class="fw-semibold">@{{ formatDate(viewOrder.created_at) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const token = localStorage.getItem('token');
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                orderList: [],
                api_url: 'http://127.0.0.1:8000',
                filteredOrderList: [],
                customers: [],
                users: [],
                branches: [],
                currentOrder: null,
                viewOrder: null,
                isEditing: false,
                loading: true,
                saving: false,
                errorMessage: null,
                searchQuery: '',
                selectedStatusFilter: '',
                selectedPaymentStatusFilter: '',
                searchDebounceTimer: null,
                currentPage: 1,
                pageSize: 5,
                totalOrderCount: 0,
                formErrors: {}
            };
        },
        async mounted() {
            try {
                await Promise.all([
                    this.loadOrders(),
                    this.loadCustomers(),
                    this.loadUsers(),
                    this.loadBranches()
                ]);
            } catch (error) {
                console.error('Error during initialization:', error);
                this.errorMessage = 'Failed to initialize application. Details: ' + (error.message || 'Unknown error');
            }
        },
        computed: {
            searchActive() {
                return this.searchQuery.trim() !== '' || this.selectedStatusFilter !== '' || this.selectedPaymentStatusFilter !== '';
            },
            totalFilteredRecords() {
                return this.filteredOrderList.length;
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
            displayedOrders() {
                const start = (this.currentPage - 1) * this.pageSize;
                const end = start + this.pageSize;
                return this.filteredOrderList.slice(start, end);
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
            filteredOrderList() {
                this.currentPage = 1;
            }
        },
        methods: {
            async loadOrders() {
                try {
                    this.loading = true;
                    this.errorMessage = null;
                    const response = await axios.get(`${this.api_url}/api/orders`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.orderList = Array.isArray(response.data.data) ? response.data.data : [];
                    this.totalOrderCount = this.orderList.length;
                    this.filteredOrderList = [...this.orderList];
                } catch (error) {
                    console.error('Error loading orders:', error);
                    this.errorMessage = this.getErrorMessage(error, 'Failed to load order data');
                    this.orderList = [];
                    this.filteredOrderList = [];
                } finally {
                    this.loading = false;
                }
            },
            async loadCustomers() {
                try {
                    const response = await axios.get(`${this.api_url}/api/customers`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.customers = Array.isArray(response.data.data) ? response.data.data : [];
                } catch (error) {
                    console.error('Error loading customers:', error);
                    this.customers = [];
                }
            },
            async loadUsers() {
                try {
                    const response = await axios.get(`${this.api_url}/api/users`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.users = Array.isArray(response.data.data) ? response.data.data : [];
                } catch (error) {
                    console.error('Error loading users:', error);
                    this.users = [];
                }
            },
            async loadBranches() {
                try {
                    const response = await axios.get(`${this.api_url}/api/branches`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.branches = Array.isArray(response.data.data) ? response.data.data : [];
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
                let filtered = [...this.orderList];

                if (this.searchQuery.trim()) {
                    const query = this.searchQuery.toLowerCase().trim();
                    filtered = filtered.filter(order =>
                        (order.customers && order.customers.name && order.customers.name.toLowerCase().includes(query)) ||
                        (order.users && order.users.name && order.users.name.toLowerCase().includes(query)) ||
                        (order.branches && order.branches.name && order.branches.name.toLowerCase().includes(query)) ||
                        (order.id && order.id.toString().includes(query)) ||
                        (order.remarks && order.remarks.toLowerCase().includes(query))
                    );
                }

                if (this.selectedStatusFilter) {
                    filtered = filtered.filter(order => order.status === this.selectedStatusFilter);
                }

                if (this.selectedPaymentStatusFilter) {
                    filtered = filtered.filter(order => order.payment_status === this.selectedPaymentStatusFilter);
                }

                this.filteredOrderList = filtered;
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
                this.currentOrder = {
                    customers_id: '',
                    users_id: '',
                    branches_id: '',
                    order_date: '',
                    total_amount: 0,
                    status: '',
                    payment_status: '',
                    remarks: ''
                };
                this.formErrors = {};
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderOffcanvas'));
                offcanvas.show();
            },
            openEditModal(order) {
                this.isEditing = true;
                this.currentOrder = {
                    id: order.id,
                    customers_id: order.customers_id,
                    users_id: order.users_id,
                    branches_id: order.branches_id,
                    order_date: order.order_date,
                    total_amount: order.total_amount,
                    status: order.status,
                    payment_status: order.payment_status,
                    remarks: order.remarks
                };
                this.formErrors = {};
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderOffcanvas'));
                offcanvas.show();
            },
            viewOrderDetails(order) {
                this.viewOrder = order;
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewOffcanvas'));
                offcanvas.show();
            },
            async saveOrder() {
                try {
                    this.saving = true;
                    this.formErrors = {};

                    const orderData = {
                        customers_id: this.currentOrder.customers_id,
                        users_id: this.currentOrder.users_id,
                        branches_id: this.currentOrder.branches_id,
                        order_date: this.currentOrder.order_date,
                        total_amount: this.currentOrder.total_amount,
                        status: this.currentOrder.status,
                        payment_status: this.currentOrder.payment_status,
                        remarks: this.currentOrder.remarks
                    };

                    let response;
                    if (this.isEditing) {
                        response = await axios.put(`${this.api_url}/api/orders/${this.currentOrder.id}`, orderData, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                    } else {
                        response = await axios.post(`${this.api_url}/api/orders`, orderData, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                    }

                    await this.loadOrders();
                    this.executeSearch();

                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('orderOffcanvas'));
                    offcanvas.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: this.isEditing ? 'Order updated successfully!' : 'Order created successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                } catch (error) {
                    console.error('Error saving order:', error);

                    if (error.response && error.response.status === 422) {
                        this.formErrors = error.response.data.errors || {};
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: this.getErrorMessage(error, 'Failed to save order'),
                        confirmButtonText: 'OK'
                    });
                } finally {
                    this.saving = false;
                }
            },
            async deleteOrder(orderId) {
                try {
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    });

                    if (result.isConfirmed) {
                        await axios.delete(`${this.api_url}/api/orders/${orderId}`, {
                            headers: { Authorization: `Bearer ${token}` }
                        });

                        await this.loadOrders();
                        this.executeSearch();

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Order has been deleted successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } catch (error) {
                    console.error('Error deleting order:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: this.getErrorMessage(error, 'Failed to delete order'),
                        confirmButtonText: 'OK'
                    });
                }
            },
            formatDate(dateString) {
                if (!dateString) return 'N/A';
                try {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });
                } catch (error) {
                    return dateString;
                }
            },
            getStatusColor(status) {
                const colors = {
                    'pending': 'warning',
                    'processing': 'info',
                    'completed': 'success',
                    'cancelled': 'danger'
                };
                return colors[status] || 'secondary';
            },
            getErrorMessage(error, defaultMessage) {
                if (error.response) {
                    if (error.response.data && error.response.data.message) {
                        return error.response.data.message;
                    }
                    if (error.response.status === 401) {
                        return 'Authentication failed. Please login again.';
                    }
                    if (error.response.status === 403) {
                        return 'Access denied. You do not have permission to perform this action.';
                    }
                    if (error.response.status === 404) {
                        return 'Resource not found.';
                    }
                    if (error.response.status === 422) {
                        return 'Validation error. Please check your input.';
                    }
                    if (error.response.status >= 500) {
                        return 'Server error. Please try again later.';
                    }
                }
                if (error.request) {
                    return 'Network error. Please check your internet connection.';
                }
                return error.message || defaultMessage;
            }
        }
    }).mount('#app');
</script>
@endsection
