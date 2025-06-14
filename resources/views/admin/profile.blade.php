<script>
    // Ensure CSRF token is set
    if (document.querySelector('meta[name="csrf-token"]')) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    const { createApp } = Vue;

    createApp({
        delimiters: ['[[', ']]'],

        data() {
            return {
                staffList: [],
                filteredStaffList: [],
                branches: [],
                currentStaff: null,
                viewStaff: null,
                isEditing: false,
                loading: true,
                saving: false,
                errorMessage: null,
                selectedFile: null,

                // Search functionality
                searchQuery: '',
                searchFilter: 'name',
                selectedPositionFilter: '',
                searchDebounceTimer: null,

                // Pagination
                currentPage: 1,
                pageSize: 10,
                totalStaffCount: 0,
                profileImageSrc: null,
                isDragOver: false,

                // Form validation
                formErrors: {}
            };
        },

        async mounted() {
            try {
                await Promise.all([
                    this.loadStaff(),
                    this.loadBranches()
                ]);
            } catch (error) {
                console.error('Error during initialization:', error);
                this.showNotification('Failed to initialize application', 'error');
            }
        },

        computed: {
            availablePositions() {
                const positions = [...new Set(this.staffList.map(staff => staff.position))];
                return positions.filter(position => position && position.trim()).sort();
            },

            searchActive() {
                return this.searchQuery.trim() !== '' || this.selectedPositionFilter !== '';
            },

            totalFilteredRecords() {
                return this.filteredStaffList.length;
            },

            totalPages() {
                return Math.ceil(this.totalFilteredRecords / this.pageSize);
            },

            startRecord() {
                if (this.totalFilteredRecords === 0) return 0;
                return (this.currentPage - 1) * this.pageSize + 1;
            },

            endRecord() {
                const end = this.currentPage * this.pageSize;
                return Math.min(end, this.totalFilteredRecords);
            },

            displayedStaff() {
                const start = (this.currentPage - 1) * this.pageSize;
                const end = start + this.pageSize;
                return this.filteredStaffList.slice(start, end);
            },

            visiblePages() {
                const pages = [];
                const totalPages = this.totalPages;
                const currentPage = this.currentPage;

                if (totalPages <= 5) {
                    for (let i = 1; i <= totalPages; i++) {
                        pages.push(i);
                    }
                } else {
                    let startPage = Math.max(1, currentPage - 2);
                    let endPage = Math.min(totalPages, startPage + 4);

                    if (endPage - startPage < 4) {
                        startPage = Math.max(1, endPage - 4);
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        pages.push(i);
                    }
                }

                return pages;
            },

            filePreviewUrl() {
                if (this.selectedFile) {
                    return URL.createObjectURL(this.selectedFile);
                }
                return null;
            },

            isFormValid() {
                return this.currentStaff &&
                    this.currentStaff.name &&
                    this.currentStaff.gender &&
                    this.currentStaff.email &&
                    this.currentStaff.phone &&
                    this.currentStaff.current_address &&
                    this.currentStaff.position &&
                    this.currentStaff.salary &&
                    this.currentStaff.branches_id;
            }
        },

        watch: {
            filteredStaffList() {
                this.currentPage = 1;
            },

            searchQuery() {
                this.performSearch();
            },

            selectedPositionFilter() {
                this.performSearch();
            }
        },

        methods: {
            // Load staff data from API
            async loadStaff() {
                try {
                    this.loading = true;
                    this.errorMessage = null;

                    const response = await axios.get('/api/staff');
                    const data = response.data;

                    this.staffList = Array.isArray(data) ? data : (data.data || []);
                    this.totalStaffCount = this.staffList.length;
                    this.filteredStaffList = [...this.staffList];

                } catch (error) {
                    console.error('Error loading staff:', error);
                    this.errorMessage = this.getErrorMessage(error, 'Failed to load staff data');
                    this.staffList = [];
                    this.filteredStaffList = [];
                    this.showNotification(this.errorMessage, 'error');
                } finally {
                    this.loading = false;
                }
            },

            // Load branches data from API
            async loadBranches() {
                try {
                    const response = await axios.get('/api/branches');
                    const data = response.data;
                    this.branches = Array.isArray(data) ? data : (data.data || []);
                } catch (error) {
                    console.error('Error loading branches:', error);
                    this.branches = [];
                    this.showNotification('Failed to load branches', 'error');
                }
            },

            // Search functionality with debouncing
            performSearch() {
                if (this.searchDebounceTimer) {
                    clearTimeout(this.searchDebounceTimer);
                }

                this.searchDebounceTimer = setTimeout(() => {
                    this.executeSearch();
                }, 300);
            },

            executeSearch() {
                let filtered = [...this.staffList];

                // Apply text search
                if (this.searchQuery.trim()) {
                    const query = this.searchQuery.toLowerCase().trim();
                    filtered = filtered.filter(staff => {
                        if (!staff) return false;

                        switch (this.searchFilter) {
                            case 'name':
                                return staff.name && staff.name.toLowerCase().includes(query);
                            case 'email':
                                return staff.email && staff.email.toLowerCase().includes(query);
                            case 'position':
                                return staff.position && staff.position.toLowerCase().includes(query);
                            case 'phone':
                                return staff.phone && staff.phone.toString().includes(query);
                            case 'branch':
                                return staff.branches && staff.branches.name &&
                                    staff.branches.name.toLowerCase().includes(query);
                            default: // 'all'
                                return (staff.name && staff.name.toLowerCase().includes(query)) ||
                                    (staff.email && staff.email.toLowerCase().includes(query)) ||
                                    (staff.position && staff.position.toLowerCase().includes(query)) ||
                                    (staff.phone && staff.phone.toString().includes(query)) ||
                                    (staff.branches && staff.branches.name &&
                                        staff.branches.name.toLowerCase().includes(query));
                        }
                    });
                }

                // Apply position filter
                if (this.selectedPositionFilter) {
                    filtered = filtered.filter(staff =>
                        staff && staff.position === this.selectedPositionFilter
                    );
                }

                this.filteredStaffList = filtered;
            },

            clearSearch() {
                this.searchQuery = '';
                this.searchFilter = 'name';
                this.selectedPositionFilter = '';
                this.filteredStaffList = [...this.staffList];
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

            // Pagination methods
            goToPage(page) {
                if (page >= 1 && page <= this.totalPages) {
                    this.currentPage = page;
                }
            },

            changePageSize() {
                this.currentPage = 1;
            },

            // Modal methods
            openAddModal() {
                this.isEditing = false;
                this.currentStaff = {
                    name: '',
                    gender: '',
                    email: '',
                    phone: '',
                    current_address: '',
                    position: '',
                    salary: '',
                    branches_id: ''
                };
                this.selectedFile = null;
                this.profileImageSrc = null;
                this.formErrors = {};

                this.showOffcanvas('staffOffcanvas');
            },

            openEditModal(staff) {
                if (!staff) return;

                this.isEditing = true;
                this.currentStaff = {
                    id: staff.id,
                    name: staff.name || '',
                    gender: staff.gender || '',
                    email: staff.email || '',
                    phone: staff.phone || '',
                    current_address: staff.current_address || '',
                    position: staff.position || '',
                    salary: staff.salary || '',
                    branches_id: staff.branches_id || '',
                    profile: staff.profile || null
                };

                this.selectedFile = null;
                this.profileImageSrc = staff.profile ? `/storage/${staff.profile}` : null;
                this.formErrors = {};

                this.showOffcanvas('staffOffcanvas');
            },

            viewStaffDetails(staff) {
                if (!staff) return;

                this.viewStaff = staff;
                this.showOffcanvas('viewOffcanvas');
            },

            // File handling methods
            handleFileUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.processFile(file);
                }
            },

            processFile(file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    this.showNotification('Please select a valid image file (JPG, JPEG, or PNG)', 'error');
                    return;
                }

                // Validate file size (2MB limit)
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    this.showNotification('File size must be less than 2MB', 'error');
                    return;
                }

                this.selectedFile = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.profileImageSrc = e.target.result;
                };
                reader.onerror = () => {
                    this.showNotification('Error reading file', 'error');
                };
                reader.readAsDataURL(file);
            },

            clearSelectedFile() {
                this.selectedFile = null;
                this.profileImageSrc = this.isEditing && this.currentStaff.profile ?
                    `/storage/${this.currentStaff.profile}` : null;

                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            },

            triggerFileInput() {
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.click();
                }
            },

            removePhoto() {
                this.profileImageSrc = null;
                this.selectedFile = null;
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            },

            // Drag and drop handlers
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

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    this.processFile(files[0]);
                }
            },

            // Form validation
            validateForm() {
                this.formErrors = {};

                if (!this.currentStaff.name || this.currentStaff.name.trim().length === 0) {
                    this.formErrors.name = 'Name is required';
                } else if (this.currentStaff.name.length > 50) {
                    this.formErrors.name = 'Name cannot exceed 50 characters';
                }

                if (!this.currentStaff.gender) {
                    this.formErrors.gender = 'Gender is required';
                }

                if (!this.currentStaff.email) {
                    this.formErrors.email = 'Email is required';
                } else if (!this.isValidEmail(this.currentStaff.email)) {
                    this.formErrors.email = 'Please enter a valid email address';
                }

                if (!this.currentStaff.phone) {
                    this.formErrors.phone = 'Phone is required';
                }

                if (!this.currentStaff.current_address || this.currentStaff.current_address.trim().length === 0) {
                    this.formErrors.current_address = 'Address is required';
                } else if (this.currentStaff.current_address.length > 100) {
                    this.formErrors.current_address = 'Address cannot exceed 100 characters';
                }

                if (!this.currentStaff.position || this.currentStaff.position.trim().length === 0) {
                    this.formErrors.position = 'Position is required';
                } else if (this.currentStaff.position.length > 100) {
                    this.formErrors.position = 'Position cannot exceed 100 characters';
                }

                if (!this.currentStaff.salary || this.currentStaff.salary <= 0) {
                    this.formErrors.salary = 'Valid salary is required';
                }

                if (!this.currentStaff.branches_id) {
                    this.formErrors.branches_id = 'Branch selection is required';
                }

                return Object.keys(this.formErrors).length === 0;
            },

            isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },

            // Save staff (Add/Edit)
            async saveStaff() {
                if (!this.validateForm()) {
                    this.showNotification('Please fix all form errors before submitting', 'error');
                    return;
                }

                try {
                    this.saving = true;

                    const formData = new FormData();

                    // Append all staff data
                    Object.keys(this.currentStaff).forEach(key => {
                        const value = this.currentStaff[key];
                        if (value !== null && value !== '' && value !== undefined) {
                            formData.append(key, value);
                        }
                    });

                    // Handle file upload
                    if (this.selectedFile) {
                        formData.append('profile', this.selectedFile);
                    }

                    let response;
                    const config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        }
                    };

                    if (this.isEditing) {
                        formData.append('_method', 'PUT');
                        response = await axios.post(`/api/staff/${this.currentStaff.id}`, formData, config);
                    } else {
                        response = await axios.post('/api/staff', formData, config);
                    }

                    // Close modal
                    this.hideOffcanvas('staffOffcanvas');

                    // Show success message
                    this.showNotification(
                        this.isEditing ? 'Staff updated successfully!' : 'Staff added successfully!',
                        'success'
                    );

                    // Reset form and reload data
                    this.resetForm();
                    await this.loadStaff();

                } catch (error) {
                    console.error('Save staff error:', error);
                    const errorMessage = this.getErrorMessage(error, 'Failed to save staff');
                    this.showNotification(errorMessage, 'error');
                } finally {
                    this.saving = false;
                }
            },

            // Reset form
            resetForm() {
                this.currentStaff = {
                    name: '',
                    gender: '',
                    email: '',
                    phone: '',
                    current_address: '',
                    position: '',
                    salary: '',
                    branches_id: ''
                };
                this.selectedFile = null;
                this.profileImageSrc = null;
                this.isEditing = false;
                this.formErrors = {};

                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            },

            // Delete staff
            async deleteStaff(staffId) {
                if (!staffId) return;

                try {
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    });

                    if (!result.isConfirmed) return;

                    await axios.delete(`/api/staff/${staffId}`);
                    await this.loadStaff();

                    this.showNotification('Staff member deleted successfully!', 'success');

                } catch (error) {
                    console.error('Error deleting staff:', error);
                    this.showNotification('Failed to delete staff member', 'error');
                }
            },

            // Utility methods
            formatDate(dateString) {
                if (!dateString) return 'N/A';

                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return 'N/A';

                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                } catch (error) {
                    console.error('Error formatting date:', error);
                    return 'N/A';
                }
            },

            formatCurrency(amount) {
                if (!amount) return '$0.00';

                try {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(amount);
                } catch (error) {
                    return `$${amount}`;
                }
            },

            getErrorMessage(error, defaultMessage = 'An error occurred') {
                if (error.response && error.response.data) {
                    if (error.response.data.errors) {
                        const errors = Object.values(error.response.data.errors).flat();
                        return errors.join(', ');
                    } else if (error.response.data.message) {
                        return error.response.data.message;
                    }
                }
                return defaultMessage;
            },

            showNotification(message, type = 'info') {
                const config = {
                    text: message,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                };

                switch (type) {
                    case 'success':
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            confirmButtonColor: '#198754',
                            ...config
                        });
                        break;
                    case 'error':
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            confirmButtonColor: '#dc3545',
                            timer: null,
                            showConfirmButton: true,
                            timerProgressBar: false,
                            text: message
                        });
                        break;
                    case 'warning':
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            confirmButtonColor: '#ffc107',
                            text: message
                        });
                        break;
                    default:
                        Swal.fire({
                            icon: 'info',
                            title: 'Info',
                            confirmButtonColor: '#0dcaf0',
                            text: message
                        });
                        break;
                }
            },

            // Bootstrap offcanvas helpers
            showOffcanvas(elementId) {
                try {
                    const element = document.getElementById(elementId);
                    if (element) {
                        const offcanvas = new bootstrap.Offcanvas(element);
                        offcanvas.show();
                    }
                } catch (error) {
                    console.error('Error showing offcanvas:', error);
                }
            },

            hideOffcanvas(elementId) {
                try {
                    const element = document.getElementById(elementId);
                    if (element) {
                        const offcanvas = bootstrap.Offcanvas.getInstance(element);
                        if (offcanvas) {
                            offcanvas.hide();
                        }
                    }
                } catch (error) {
                    console.error('Error hiding offcanvas:', error);
                }
            },

            // Refresh data
            async refreshData() {
                try {
                    await Promise.all([
                        this.loadStaff(),
                        this.loadBranches()
                    ]);
                    this.showNotification('Data refreshed successfully!', 'success');
                } catch (error) {
                    this.showNotification('Failed to refresh data', 'error');
                }
            }
        },

        // Cleanup
        beforeUnmount() {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }
        }
    }).mount('#app');
</script>


