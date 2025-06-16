@extends('admin.master')
<style>
    [v-cloak] {
        display: none;
    }
</style>
@section('content')
<div id="app" v-cloak>
    <div class="container-fluid mt-4">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
            <div>
<!--                <h3 class="fw-bold mb-3">Invoice List</h3>-->
            </div>
            <div>
                <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Invoice">
                    <i class="fa-solid fa-plus fa-lg"></i>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">Invoice Management</div>
                            <div class="card-tools">
                                <div class="row justify-content-end align-items-center g-2">
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm" v-model="selectedStatusFilter" @change="performSearch">
                                            <option value="">All Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                            <option value="sent">Sent</option>
                                            <option value="draft">Draft</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <input type="text" class="form-control" placeholder="Search invoices..."
                                               v-model="searchQuery" @input="performSearch">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search-stats mt-2" v-if="searchActive">
                            Showing @{{ displayedInvoices.length }} of @{{ totalInvoiceCount }} invoices
                            <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="loading" class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading invoice data...</p>
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
                                        <th>Transaction Date</th>
                                        <th>Pick Up Date</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Status</th>
                                        <th>Order ID</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(invoice, index) in displayedInvoices" :key="invoice.id">
                                        <td @click="viewInvoiceDetails(invoice)" style="cursor: pointer;">@{{ invoice.id }}</td>
                                        <td @click="viewInvoiceDetails(invoice)" style="cursor: pointer;" v-html="highlightText(invoice.customers ? invoice.customers.name : 'N/A')"></td>
                                        <td @click="viewInvoiceDetails(invoice)" style="cursor: pointer;" v-html="highlightText(invoice.users ? invoice.users.name : 'N/A')"></td>
                                        <td @click="viewInvoiceDetails(invoice)" style="cursor: pointer;">@{{ formatDate(invoice.transaction_date) }}</td>
                                        <td @click="viewInvoiceDetails(invoice)" style="cursor: pointer;">@{{ formatDateTime(invoice.pick_up_date_time) }}</td>
                                        <td @click="viewInvoiceDetails(invoice)" style="cursor: pointer;">@{{ parseFloat(invoice.total_amount).toFixed(2) }}</td>
                                        <td @click="viewInvoiceDetails(invoice)" style="cursor: pointer;">@{{ parseFloat(invoice.paid_amount).toFixed(2) }}</td>
                                        <td @click="viewInvoiceDetails(invoice)" style="cursor: pointer;">
                                                <span class="status-badge" :class="'status-' + invoice.status">
                                                    @{{ invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1) }}
                                                </span>
                                        </td>
                                        <td @click="viewInvoiceDetails(invoice)" style="cursor: pointer;">@{{ invoice.order_id }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(invoice)">
                                                        <i class="fa-solid fa-pen me-2"></i>Edit
                                                    </a>
                                                </div>
                                                <div>
                                                    <a class="action-btn btn-delete" href="#" @click.prevent="deleteInvoice(invoice.id)">
                                                        <i class="fa-solid fa-trash me-2"></i>Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div v-if="displayedInvoices.length === 0 && !loading" class="text-center py-4">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No invoices found</h5>
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
                                        <span v-if="totalFilteredRecords !== totalInvoiceCount">
                                                (filtered from @{{ totalInvoiceCount }} total entries)
                                            </span>
                                    </div>
                                    <nav aria-label="Invoice pagination">
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
        <div class="offcanvas offcanvas-end" tabindex="-1" id="invoiceOffcanvas" aria-labelledby="invoiceOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 id="invoiceOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Invoice</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" v-if="currentInvoice">
                <form @submit.prevent="saveInvoice">
                    <div class="mb-3">
                        <label class="form-label">Customer *</label>
                        <select class="form-select" v-model="currentInvoice.customer_id" required>
                            <option value="">Select Customer</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                @{{ customer.name }}
                            </option>
                        </select>
                        <span v-if="formErrors.customer_id" class="text-danger small">@{{ formErrors.customer_id }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User *</label>
                        <select class="form-select" v-model="currentInvoice.user_id" required>
                            <option value="">Select User</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                @{{ user.name }}
                            </option>
                        </select>
                        <span v-if="formErrors.user_id" class="text-danger small">@{{ formErrors.user_id }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Date *</label>
                        <input type="date" class="form-control" v-model="currentInvoice.transaction_date" required>
                        <span v-if="formErrors.transaction_date" class="text-danger small">@{{ formErrors.transaction_date }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pick Up Date & Time *</label>
                        <input type="datetime-local" class="form-control" v-model="currentInvoice.pick_up_date_time" required>
                        <span v-if="formErrors.pick_up_date_time" class="text-danger small">@{{ formErrors.pick_up_date_time }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Amount *</label>
                        <input type="number" class="form-control" v-model="currentInvoice.total_amount" required min="0" step="0.01">
                        <span v-if="formErrors.total_amount" class="text-danger small">@{{ formErrors.total_amount }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paid Amount *</label>
                        <input type="number" class="form-control" v-model="currentInvoice.paid_amount" required min="0" step="0.01">
                        <span v-if="formErrors.paid_amount" class="text-danger small">@{{ formErrors.paid_amount }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" v-model="currentInvoice.status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="sent">Sent</option>
                            <option value="draft">Draft</option>
                        </select>
                        <span v-if="formErrors.status" class="text-danger small">@{{ formErrors.status }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order *</label>
                        <select class="form-select" v-model="currentInvoice.order_id" required>
                            <option value="">Select Order</option>
                            <option v-for="order in orders" :key="order.id" :value="order.id">
                                Order @{{ order.id }}
                            </option>
                        </select>
                        <span v-if="formErrors.order_id" class="text-danger small">@{{ formErrors.order_id ? formErrors.order_id : "1"}}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method *</label>
                        <select class="form-select" v-model="currentInvoice.payment_method_id" required>
                            <option value="">Select Payment Method</option>
                            <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                                @{{ method.name }}
                            </option>
                        </select>
                        <span v-if="formErrors.payment_method_id" class="text-danger small">@{{ formErrors.payment_method_id }}</span>
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
                    <div v-if="viewInvoice">
                        <h5 class="mb-0">Invoice @{{ viewInvoice.id }}</h5>
                        <small class="text-white badge" :class="'bg-' + getStatusColor(viewInvoice.status)">@{{ viewInvoice.status.charAt(0).toUpperCase() + viewInvoice.status.slice(1) }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" v-if="viewInvoice">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Customer</div>
                        <div class="fw-semibold">@{{ viewInvoice.customers ? viewInvoice.customers.name : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">User</div>
                        <div class="fw-semibold">@{{ viewInvoice.users ? viewInvoice.users.name : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Transaction Date</div>
                        <div class="fw-semibold">@{{ formatDate(viewInvoice.transaction_date) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Pick Up Date & Time</div>
                        <div class="fw-semibold">@{{ formatDateTime(viewInvoice.pick_up_date_time) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Total Amount</div>
                        <div class="fw-semibold">@{{ parseFloat(viewInvoice.total_amount).toFixed(2) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Paid Amount</div>
                        <div class="fw-semibold">@{{ parseFloat(viewInvoice.paid_amount).toFixed(2) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Status</div>
                        <div class="fw-semibold">
                                <span class="status-badge" :class="'status-' + viewInvoice.status">
                                    @{{ viewInvoice.status.charAt(0).toUpperCase() + viewInvoice.status.slice(1) }}
                                </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Order ID</div>
                        <div class="fw-semibold">@{{ viewInvoice.order_id }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Payment Method</div>
                        <div class="fw-semibold">@{{ viewInvoice.paymentMethods ? viewInvoice.paymentMethods.name : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Created Date</div>
                        <div class="fw-semibold">@{{ formatDate(viewInvoice.created_at) }}</div>
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
    // Retrieve token from localStorage
    const token = localStorage.getItem('token');

    const { createApp } = Vue;

    createApp({
        data() {
            return {
                invoiceList: [],
                api_url: 'http://127.0.0.1:8000',
                filteredInvoiceList: [],
                customers: [],
                users: [],
                orders: [],
                paymentMethods: [],
                currentInvoice: null,
                viewInvoice: null,
                isEditing: false,
                loading: true,
                saving: false,
                errorMessage: null,
                searchQuery: '',
                selectedStatusFilter: '',
                searchDebounceTimer: null,
                currentPage: 1,
                pageSize: 5,
                totalInvoiceCount: 0,
                formErrors: {}
            };
        },
        async mounted() {
            console.log('Mounting Vue app...');
            try {
                await Promise.all([
                    this.loadInvoices(),
                    this.loadCustomers(),
                    this.loadUsers(),
                    this.loadOrders(),
                    this.loadPaymentMethods()
                ]);
                console.log('Invoice List:', this.invoiceList);
            } catch (error) {
                console.error('Error during initialization:', error);
                this.errorMessage = 'Failed to initialize application. Details: ' + (error.message || 'Unknown error');
            }
        },
        computed: {
            searchActive() {
                return this.searchQuery.trim() !== '' || this.selectedStatusFilter !== '';
            },
            totalFilteredRecords() {
                return this.filteredInvoiceList.length;
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
            displayedInvoices() {
                const start = (this.currentPage - 1) * this.pageSize;
                const end = start + this.pageSize;
                return this.filteredInvoiceList.slice(start, end);
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
            filteredInvoiceList() {
                this.currentPage = 1;
            },
            searchQuery() {
                this.performSearch();
            },
            selectedStatusFilter() {
                this.performSearch();
            }
        },
        methods: {
            async loadInvoices() {
                try {
                    this.loading = true;
                    this.errorMessage = null;
                    console.log('Fetching invoice data...');
                    const response = await axios.get(`${this.api_url}/api/invoices`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.invoiceList = Array.isArray(response.data.data) ? response.data.data : [];
                    this.totalInvoiceCount = this.invoiceList.length;
                    this.filteredInvoiceList = [...this.invoiceList];
                } catch (error) {
                    console.error('Error loading invoices:', error.response ? error.response.data : error.message);
                    this.errorMessage = this.getErrorMessage(error, 'Failed to load invoice data');
                    this.invoiceList = [];
                    this.filteredInvoiceList = [];
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
            async loadOrders() {
                try {
                    const response = await axios.get(`${this.api_url}/api/orders`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.orders = Array.isArray(response.data.data) ? response.data.data : [];
                } catch (error) {
                    console.error('Error loading orders:', error);
                    this.orders = [];
                }
            },
            async loadPaymentMethods() {
                try {
                    const response = await axios.get(`${this.api_url}/api/payment-methods`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.paymentMethods = Array.isArray(response.data.data) ? response.data.data : [];
                } catch (error) {
                    console.error('Error loading payment methods:', error);
                    this.paymentMethods = [];
                }
            },
            performSearch() {
                if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
            },
            executeSearch() {
                let filtered = [...this.invoiceList];
                if (this.searchQuery.trim()) {
                    const query = this.searchQuery.toLowerCase().trim();
                    filtered = filtered.filter(invoice =>
                        (invoice.customers && invoice.customers.name && invoice.customers.name.toLowerCase().includes(query)) ||
                        (invoice.users && invoice.users.name && invoice.users.name.toLowerCase().includes(query)) ||
                        (invoice.id && invoice.id.toString().includes(query)) ||
                        (invoice.order_id && invoice.order_id.toString().includes(query))
                    );
                }
                if (this.selectedStatusFilter) {
                    filtered = filtered.filter(invoice => invoice.status === this.selectedStatusFilter);
                }
                this.filteredInvoiceList = filtered;
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
                this.currentInvoice = {
                    customer_id: '1',
                    user_id: '',
                    transaction_date: '',
                    pick_up_date_time: '',
                    total_amount: 0,
                    paid_amount: 0,
                    status: '',
                    order_id: '1',
                    payment_method_id: 'ABA'
                };
                this.formErrors = {};
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('invoiceOffcanvas'));
                offcanvas.show();
            },
            openEditModal(invoice) {
                this.isEditing = true;
                this.currentInvoice = {
                    id: invoice.id,
                    customer_id: invoice.customer_id,
                    user_id: invoice.user_id,
                    transaction_date: invoice.transaction_date,
                    pick_up_date_time: invoice.pick_up_date_time,
                    total_amount: invoice.total_amount,
                    paid_amount: invoice.paid_amount,
                    status: invoice.status,
                    order_id: invoice.order_id,
                    payment_method_id: invoice.payment_method_id
                };
                this.formErrors = {};
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('invoiceOffcanvas'));
                offcanvas.show();
            },
            viewInvoiceDetails(invoice) {
                this.viewInvoice = invoice;
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewOffcanvas'));
                offcanvas.show();
            },
            async saveInvoice() {
                try {
                    this.saving = true;
                    this.formErrors = {};

                    const invoiceData = {
                        customer_id: this.currentInvoice.customer_id,
                        user_id: this.currentInvoice.user_id,
                        transaction_date: this.currentInvoice.transaction_date,
                        pick_up_date_time: this.currentInvoice.pick_up_date_time,
                        total_amount: parseFloat(this.currentInvoice.total_amount),
                        paid_amount: parseFloat(this.currentInvoice.paid_amount),
                        status: this.currentInvoice.status,
                        order_id: this.currentInvoice.order_id,
                        payment_method_id: this.currentInvoice.payment_method_id
                    };

                    let response;
                    if (this.isEditing) {
                        response = await axios.put(`${this.api_url}/api/invoices/${this.currentInvoice.id}`, invoiceData, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                    } else {
                        response = await axios.post(`${this.api_url}/api/invoices`, invoiceData, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                    }

                    await this.loadInvoices();

                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('invoiceOffcanvas'));
                    offcanvas.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: `Invoice ${this.isEditing ? 'updated' : 'created'} successfully`,
                        timer: 3000,
                        showConfirmButton: false
                    });

                } catch (error) {
                    console.error('Error saving invoice:', error);

                    if (error.response && error.response.status === 422) {
                        this.formErrors = error.response.data.errors || {};
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: this.getErrorMessage(error, `Failed to ${this.isEditing ? 'update' : 'create'} invoice`)
                    });
                } finally {
                    this.saving = false;
                }
            },
            async deleteInvoice(invoiceId) {
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
                        await axios.delete(`${this.api_url}/api/invoices/${invoiceId}`, {
                            headers: { Authorization: `Bearer ${token}` }
                        });

                        await this.loadInvoices();

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Invoice has been deleted successfully.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                } catch (error) {
                    console.error('Error deleting invoice:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: this.getErrorMessage(error, 'Failed to delete invoice')
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
                        day: 'numeric'
                    });
                } catch (error) {
                    return 'Invalid Date';
                }
            },
            formatDateTime(dateTimeString) {
                if (!dateTimeString) return 'N/A';
                try {
                    const date = new Date(dateTimeString);
                    return date.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (error) {
                    return 'Invalid Date';
                }
            },
            getStatusColor(status) {
                const colorMap = {
                    'pending': 'warning',
                    'paid': 'info',
                    'sent': 'success',
                    'draft': 'danger'
                };
                return colorMap[status] || 'secondary';
            },
            getErrorMessage(error, defaultMessage) {
                if (error.response) {
                    if (error.response.data && error.response.data.message) {
                        return error.response.data.message;
                    }
                    if (error.response.status === 401) {
                        return 'Unauthorized access. Please login again.';
                    }
                    if (error.response.status === 403) {
                        return 'You do not have permission to perform this action.';
                    }
                    if (error.response.status === 404) {
                        return 'Resource not found.';
                    }
                    if (error.response.status >= 500) {
                        return 'Server error. Please try again later.';
                    }
                }
                return error.message || defaultMessage;
            }
        }
    }).mount('#app');
</script>

@endsection
