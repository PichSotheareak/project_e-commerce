@extends('admin.master')
@section('content')
<div id="app">
    <div class="container-fluid mt-4">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
            <div>
                <h3 class="fw-bold mb-3">Contact Us List</h3>
            </div>
            <div>
                <button class="btn btn-secondary rounded" type="button" @click="openAddModal" title="Add Contact">
                    <i class="fa-solid fa-plus fa-lg"></i>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">Contact Us Management</div>
                            <div class="card-tools">
                                <div class="row justify-content-end align-items-center g-2">
                                    <div class="col-auto">
                                        <input type="text" class="form-control" placeholder="Search contacts..."
                                               v-model="searchQuery" @input="performSearch">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search-stats mt-2" v-if="searchActive">
                            Showing @{{ displayedContacts.length }} of @{{ totalContactCount }} contacts
                            <span v-if="searchQuery">for "@{{ searchQuery }}"</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="loading" class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading contact data...</p>
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
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Message</th>
                                        <th>Contact Date</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(contact, index) in displayedContacts" :key="contact.id">
                                        <td @click="viewContactDetails(contact)" style="cursor: pointer;">@{{ contact.id }}</td>
                                        <td @click="viewContactDetails(contact)" style="cursor: pointer;" v-html="highlightText(getFullName(contact))"></td>
                                        <td @click="viewContactDetails(contact)" style="cursor: pointer;" v-html="highlightText(contact.email || 'N/A')"></td>
                                        <td @click="viewContactDetails(contact)" style="cursor: pointer;" v-html="highlightText(contact.phone || 'N/A')"></td>
                                        <td @click="viewContactDetails(contact)" style="cursor: pointer;">
                                            @{{ contact.message ? (contact.message.length > 30 ? contact.message.substring(0, 30) + '...' : contact.message) : 'N/A' }}
                                        </td>
                                        <td @click="viewContactDetails(contact)" style="cursor: pointer;">@{{ formatDate(contact.created_at) }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <a class="action-btn btn-edit" href="#" @click.prevent="openEditModal(contact)">
                                                        <i class="fa-solid fa-pen me-2"></i>Edit
                                                    </a>
                                                </div>
                                                <div>
                                                    <a class="action-btn btn-delete" href="#" @click.prevent="deleteContact(contact.id)">
                                                        <i class="fa-solid fa-trash me-2"></i>Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div v-if="displayedContacts.length === 0 && !loading" class="text-center py-4">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No contacts found</h5>
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
                                        <span v-if="totalFilteredRecords !== totalContactCount">
                                            (filtered from @{{ totalContactCount }} total entries)
                                        </span>
                                    </div>
                                    <nav aria-label="Contact pagination">
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
        <div class="offcanvas offcanvas-end" tabindex="-1" id="contactOffcanvas" aria-labelledby="contactOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 id="contactOffcanvasLabel">@{{ isEditing ? 'Edit' : 'Add' }} Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" v-if="currentContact">
                <form @submit.prevent="saveContact">
                    <div class="mb-3">
                        <label class="form-label">First Name *</label>
                        <input type="text" class="form-control" v-model="currentContact.first_name" required>
                        <span v-if="formErrors.first_name" class="text-danger small">@{{ formErrors.first_name[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name *</label>
                        <input type="text" class="form-control" v-model="currentContact.last_name" required>
                        <span v-if="formErrors.last_name" class="text-danger small">@{{ formErrors.last_name[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" v-model="currentContact.email" required>
                        <span v-if="formErrors.email" class="text-danger small">@{{ formErrors.email[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" v-model="currentContact.phone">
                        <span v-if="formErrors.phone" class="text-danger small">@{{ formErrors.phone[0] }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message *</label>
                        <textarea class="form-control" v-model="currentContact.message" rows="4" required placeholder="Enter your message"></textarea>
                        <span v-if="formErrors.message" class="text-danger small">@{{ formErrors.message[0] }}</span>
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
                    <div v-if="viewContact">
                        <h5 class="mb-0">Contact #@{{ viewContact.id }}</h5>
                    </div>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" v-if="viewContact">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Full Name</div>
                        <div class="fw-semibold">@{{ getFullName(viewContact) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Email</div>
                        <div class="fw-semibold">@{{ viewContact.email || 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Phone</div>
                        <div class="fw-semibold">@{{ viewContact.phone || 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Message</div>
                        <div class="fw-semibold" style="white-space: pre-wrap;">@{{ viewContact.message || 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Contact Date</div>
                        <div class="fw-semibold">@{{ formatDate(viewContact.created_at) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Last Updated</div>
                        <div class="fw-semibold">@{{ formatDate(viewContact.updated_at) }}</div>
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

<script>
    const token = localStorage.getItem('token');
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                contactList: [],
                api_url: 'http://127.0.0.1:8000',
                filteredContactList: [],
                currentContact: null,
                viewContact: null,
                isEditing: false,
                loading: true,
                saving: false,
                errorMessage: null,
                searchQuery: '',
                searchDebounceTimer: null,
                currentPage: 1,
                pageSize: 5,
                totalContactCount: 0,
                formErrors: {}
            };
        },
        async mounted() {
            try {
                await this.loadContacts();
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
                return this.filteredContactList.length;
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
            displayedContacts() {
                const start = (this.currentPage - 1) * this.pageSize;
                const end = start + this.pageSize;
                return this.filteredContactList.slice(start, end);
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
            filteredContactList() {
                this.currentPage = 1;
            }
        },
        methods: {
            async loadContacts() {
                try {
                    this.loading = true;
                    this.errorMessage = null;
                    const response = await axios.get(`${this.api_url}/api/contactUs`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.contactList = Array.isArray(response.data.data) ? response.data.data : [];
                    this.totalContactCount = this.contactList.length;
                    this.filteredContactList = [...this.contactList];
                } catch (error) {
                    console.error('Error loading contacts:', error);
                    this.errorMessage = this.getErrorMessage(error, 'Failed to load contact data');
                    this.contactList = [];
                    this.filteredContactList = [];
                } finally {
                    this.loading = false;
                }
            },
            performSearch() {
                if (this.searchDebounceTimer) clearTimeout(this.searchDebounceTimer);
                this.searchDebounceTimer = setTimeout(this.executeSearch, 300);
            },
            executeSearch() {
                let filtered = [...this.contactList];

                if (this.searchQuery.trim()) {
                    const query = this.searchQuery.toLowerCase().trim();
                    filtered = filtered.filter(contact =>
                        (contact.first_name && contact.first_name.toLowerCase().includes(query)) ||
                        (contact.last_name && contact.last_name.toLowerCase().includes(query)) ||
                        (contact.email && contact.email.toLowerCase().includes(query)) ||
                        (contact.phone && contact.phone.toLowerCase().includes(query)) ||
                        (contact.message && contact.message.toLowerCase().includes(query)) ||
                        (contact.id && contact.id.toString().includes(query))
                    );
                }

                this.filteredContactList = filtered;
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
            getFullName(contact) {
                if (!contact) return 'N/A';
                const first = contact.first_name || '';
                const last = contact.last_name || '';
                return (first + ' ' + last).trim() || 'N/A';
            },
            goToPage(page) {
                if (page >= 1 && page <= this.totalPages) this.currentPage = page;
            },
            changePageSize() {
                this.currentPage = 1;
            },
            openAddModal() {
                this.isEditing = false;
                this.currentContact = {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    message: ''
                };
                this.formErrors = {};
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('contactOffcanvas'));
                offcanvas.show();
            },
            openEditModal(contact) {
                this.isEditing = true;
                this.currentContact = {
                    id: contact.id,
                    first_name: contact.first_name || '',
                    last_name: contact.last_name || '',
                    email: contact.email || '',
                    phone: contact.phone || '',
                    message: contact.message || ''
                };
                this.formErrors = {};
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('contactOffcanvas'));
                offcanvas.show();
            },
            viewContactDetails(contact) {
                this.viewContact = contact;
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewOffcanvas'));
                offcanvas.show();
            },
            async saveContact() {
                try {
                    this.saving = true;
                    this.formErrors = {};

                    const contactData = {
                        first_name: this.currentContact.first_name,
                        last_name: this.currentContact.last_name,
                        email: this.currentContact.email,
                        phone: this.currentContact.phone,
                        message: this.currentContact.message
                    };

                    let response;
                    if (this.isEditing) {
                        response = await axios.put(`${this.api_url}/api/contactUs/${this.currentContact.id}`, contactData, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                    } else {
                        response = await axios.post(`${this.api_url}/api/contactUs`, contactData, {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                    }

                    await this.loadContacts();
                    this.executeSearch();

                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('contactOffcanvas'));
                    offcanvas.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: this.isEditing ? 'Contact updated successfully!' : 'Contact created successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                } catch (error) {
                    console.error('Error saving contact:', error);

                    if (error.response && error.response.status === 422) {
                        this.formErrors = error.response.data.errors || {};
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: this.getErrorMessage(error, 'Failed to save contact'),
                        confirmButtonText: 'OK'
                    });
                } finally {
                    this.saving = false;
                }
            },
            async deleteContact(contactId) {
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
                        await axios.delete(`${this.api_url}/api/contactUs/${contactId}`, {
                            headers: { Authorization: `Bearer ${token}` }
                        });

                        await this.loadContacts();
                        this.executeSearch();

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Contact has been deleted successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } catch (error) {
                    console.error('Error deleting contact:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: this.getErrorMessage(error, 'Failed to delete contact'),
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
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (error) {
                    return dateString;
                }
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
