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
                <!--                <h3 class="fw-bold mb-3">Payment List</h3>-->
            </div>
            <div>
                <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Payment">
                    <i class="fa-solid fa-plus fa-lg"></i>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">Payment Management</div>
                            <div class="card-tools">
                                <div class="row justify-content-end align-items-center g-2">
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm" v-model="selectedPaymentMethodFilter" @change="performSearch">
                                            <option value="">All Payment Methods</option>
                                            <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                                                @{{ method.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <input type="text" class="form-control" placeholder="Search payments..."
                                               v-model="searchQuery" @input="performSearch">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search-stats mt-2" v-if="searchActive">
                            Showing @{{ displayedPayments.length }} of @{{ totalPaymentCount }} payments
                            <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="loading" class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading payment data...</p>
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
                                        <th>Invoice ID</th>
                                        <th>Payment Date</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Branch</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(payment, index) in displayedPayments" :key="payment.id">
                                        <td @click="viewPaymentDetails(payment)" style="cursor: pointer;">@{{ payment.id }}</td>
                                        <td @click="viewPaymentDetails(payment)" style="cursor: pointer;" v-html="highlightText(payment.invoice_id ? payment.invoice_id.toString() : 'N/A')"></td>
                                        <td @click="viewPaymentDetails(payment)" style="cursor: pointer;">@{{ formatDate(payment.payment_date) }}</td>
                                        <td @click="viewPaymentDetails(payment)" style="cursor: pointer;">@{{ parseFloat(payment.amount).toFixed(2) }}</td>
                                        <td @click="viewPaymentDetails(payment)" style="cursor: pointer;" v-html="highlightText(payment.paymentMethod ? payment.paymentMethod.name : 'N/A')"></td>
                                        <td @click="viewPaymentDetails(payment)" style="cursor: pointer;" v-html="highlightText(payment.branches ? payment.branches.name : 'N/A')"></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(payment)">
                                                        <i class="fa-solid fa-pen me-2"></i>Edit
                                                    </a>
                                                </div>
                                                <div>
                                                    <a class="action-btn btn-delete" href="#" @click.prevent="deletePayment(payment.id)">
                                                        <i class="fa-solid fa-trash me-2"></i>Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div v-if="displayedPayments.length === 0 && !loading" class="text-center py-4">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No payments found</h5>
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
                                        <span v-if="totalFilteredRecords !== totalPaymentCount">
                                                (filtered from @{{ totalPaymentCount }} total entries)
                                            </span>
                                    </div>
                                    <nav aria-label="Payment pagination">
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
        <div class="offcanvas offcanvas-end" tabindex="-1" id="paymentOffcanvas" aria-labelledby="paymentOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 id="paymentOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Payment</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" v-if="currentPayment">
                <form @submit.prevent="savePayment">
                    <div class="mb-3">
                        <label class="form-label">Invoice *</label>
                        <select class="form-select" v-model="currentPayment.invoice_id" required>
                            <option value="">Select Invoice</option>
                            <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">
                                Invoice #@{{ invoice.id }} - @{{ invoice.customers ? invoice.customers.name : 'N/A' }}
                            </option>
                        </select>
                        <span v-if="formErrors.invoice_id" class="text-danger small">@{{ formErrors.invoice_id }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date *</label>
                        <input type="date" class="form-control" v-model="currentPayment.payment_date" required>
                        <span v-if="formErrors.payment_date" class="text-danger small">@{{ formErrors.payment_date }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" class="form-control" v-model="currentPayment.amount" required min="0" step="0.01">
                        <span v-if="formErrors.amount" class="text-danger small">@{{ formErrors.amount }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method *</label>
                        <select class="form-select" v-model="currentPayment.payment_method_id" required>
                            <option value="">Select Payment Method</option>
                            <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                                @{{ method.name }}
                            </option>
                        </select>
                        <span v-if="formErrors.payment_method_id" class="text-danger small">@{{ formErrors.payment_method_id }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Branch *</label>
                        <select class="form-select" v-model="currentPayment.branch_id" required>
                            <option value="">Select Branch</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                @{{ branch.name }}
                            </option>
                        </select>
                        <span v-if="formErrors.branch_id" class="text-danger small">@{{ formErrors.branch_id }}</span>
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
                    <div v-if="viewPayment">
                        <h5 class="mb-0">Payment #@{{ viewPayment.id }}</h5>
                        <small class="text-white badge bg-success">Payment Record</small>
                    </div>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" v-if="viewPayment">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Payment ID</div>
                        <div class="fw-semibold">@{{ viewPayment.id }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Invoice ID</div>
                        <div class="fw-semibold">@{{ viewPayment.invoice_id }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Invoice Customer</div>
                        <div class="fw-semibold">@{{ viewPayment.invoices && viewPayment.invoices.customers ? viewPayment.invoices.customers.name : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Payment Date</div>
                        <div class="fw-semibold">@{{ formatDate(viewPayment.payment_date) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Amount</div>
                        <div class="fw-semibold">$@{{ parseFloat(viewPayment.amount).toFixed(2) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Payment Method</div>
                        <div class="fw-semibold">@{{ viewPayment.paymentMethod ? viewPayment.paymentMethod.name : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Branch</div>
                        <div class="fw-semibold">@{{ viewPayment.branches ? viewPayment.branches.name : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Created Date</div>
                        <div class="fw-semibold">@{{ formatDate(viewPayment.created_at) }}</div>
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
                paymentList: [],
                api_url: 'http://127.0.0.1:8000',
                filteredPaymentList: [],
                invoices: [],
                branches: [],
                paymentMethods: [],
                currentPayment: null,
                viewPayment: null,
                isEditing: false,
                loading: true,
                saving: false,
                errorMessage: null,
                searchQuery: '',
                selectedPaymentMethodFilter: '',
                searchDebounceTimer: null,
                currentPage: 1,
                pageSize: 5,
                totalPaymentCount: 0,
                formErrors: {}
            };
        },
        async mounted() {
            console.log('Mounting Vue app...');
            try {
                await Promise.all([
                    this.loadPayments(),
                    this.loadInvoices(),
                    this.loadBranches(),
                    this.loadPaymentMethods()
                ]);
                console.log('Payment List:', this.paymentList);
            } catch (error) {
                console.error('Error during initialization:', error);
                this.errorMessage = 'Failed to initialize application. Details: ' + (error.message || 'Unknown error');
            }
        },
        computed: {
            searchActive() {
                return this.searchQuery.trim() !== '' || this.selectedPaymentMethodFilter !== '';
            },
            totalFilteredRecords() {
                return this.filteredPaymentList.length;
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
            displayedPayments() {
                const start = (this.currentPage - 1) * this.pageSize;
                const end = start + this.pageSize;
                return this.filteredPaymentList.slice(start, end);
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
            filteredPaymentList() {
                this.currentPage = 1;
            },
            searchQuery() {
                this.performSearch();
            },
            selectedPaymentMethodFilter() {
                this.performSearch();
            }
        },
        methods: {
            async loadPayments() {
                try {
                    this.loading = true;
                    this.errorMessage = null;
                    console.log('Fetching payment data...');
                    const response = await axios.get(`${this.api_url}/api/payments`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.paymentList = Array.isArray(response.data.data) ? response.data.data : [];
                    this.totalPaymentCount = this.paymentList.length;
                    this.filteredPaymentList = [...this.paymentList];
                } catch (error) {
                    console.error('Error loading payments:', error.response ? error.response.data : error.message);
                    this.errorMessage = this.getErrorMessage(error, 'Failed to load payment data');
                    this.paymentList = [];
                    this.filteredPaymentList = [];
                } finally {
                    this.loading = false;
                }
            },
            async loadInvoices() {
                try {
                    const response = await axios.get(`${this.api_url}/api/invoices`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.invoices = Array.isArray(response.data.data) ? response.data.data : [];
                } catch (error) {
                    console.error('Error loading invoices:', error);
                    this.invoices = [];
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
                let filtered = [...this.paymentList];
                if (this.searchQuery.trim()) {
                    const query = this.searchQuery.toLowerCase().trim();
                    filtered = filtered.filter(payment =>
                        (payment.id && payment.id.toString().includes(query)) ||
                        (payment.invoice_id && payment.invoice_id.toString().includes(query)) ||
                        (payment.paymentMethod && payment.paymentMethod.name && payment.paymentMethod.name.toLowerCase().includes(query)) ||
                        (payment.branches && payment.branches.name && payment.branches.name.toLowerCase().includes(query)) ||
                        (payment.amount && payment.amount.toString().includes(query))
                    );
                }
                if (this.selectedPaymentMethodFilter) {
                    filtered = filtered.filter(payment => payment.payment_method_id == this.selectedPaymentMethodFilter);
                }
                this.filteredPaymentList = filtered;
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
                this.currentPayment = {
                    invoice_id: '',
                    payment_date: '',
                    amount: 0,
                    payment_method_id: '',
                    branch_id: ''
                };
                this.formErrors = {};
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('paymentOffcanvas'));
                offcanvas.show();
            },
            openEditModal(payment) {
                this.isEditing = true;
                this.currentPayment = {
                    id: payment.id,
                    invoice_id: payment.invoice_id,
                    payment_date: payment.payment_date,
                    amount: payment.amount,
                    payment_method_id: payment.payment_method_id,
                    branch_id: payment.branch_id
                };
                this.formErrors = {};
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('paymentOffcanvas'));
                offcanvas.show();
            },
            viewPaymentDetails(payment) {
                this.viewPayment = payment;
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewOffcanvas'));
                offcanvas.show();
            },
            async savePayment() {
                try {
                    this.saving = true;
                    this.formErrors = {};

                    const paymentData = {
                        invoice_id: this.currentPayment.invoice_id,
                        payment_date: this.currentPayment.payment_date,
                        amount: parseFloat(this.currentPayment.amount),
                        payment_method_id: this.currentPayment.payment_method_id,
                        branch_id: this.currentPayment.branch_id
                    };

                    let response;
                    if (this.isEditing) {
                        response = await axios.put(`${this.api_url}/api/payments/${this.currentPayment.id}`, paymentData, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                    } else {
                        response = await axios.post(`${this.api_url}/api/payments`, paymentData, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                    }

                    await this.loadPayments();

                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('paymentOffcanvas'));
                    offcanvas.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: `Payment ${this.isEditing ? 'updated' : 'created'} successfully`,
                        timer: 3000,
                        showConfirmButton: false
                    });

                } catch (error) {
                    console.error('Error saving payment:', error);

                    if (error.response && error.response.status === 422) {
                        this.formErrors = error.response.data.errors || {};
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: this.getErrorMessage(error, `Failed to ${this.isEditing ? 'update' : 'create'} payment`)
                    });
                } finally {
                    this.saving = false;
                }
            },
            async deletePayment(paymentId) {
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
                        await axios.delete(`${this.api_url}/api/payments/${paymentId}`, {
                            headers: { Authorization: `Bearer ${token}` }
                        });

                        await this.loadPayments();

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Payment has been deleted successfully.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                } catch (error) {
                    console.error('Error deleting payment:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: this.getErrorMessage(error, 'Failed to delete payment')
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
