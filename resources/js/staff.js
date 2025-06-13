
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
    searchFilter: 'all',
    selectedPositionFilter: '',
    searchDebounceTimer: null,

    // Pagination
    currentPage: 1,
    pageSize: 10,
    totalStaffCount: 0
};
},

    async mounted() {
    await this.loadStaff();
    await this.loadBranches();
},

    computed: {
    availablePositions() {
    const positions = [...new Set(this.staffList.map(staff => staff.position))];
    return positions.filter(position => position).sort();
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

    // Show up to 5 page numbers
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);

    // Adjust start if we're near the end
    if (endPage - startPage < 4) {
    startPage = Math.max(1, endPage - 4);
}

    for (let i = startPage; i <= endPage; i++) {
    pages.push(i);
}

    return pages;
},

    // New computed property for file preview
    filePreviewUrl() {
    if (this.selectedFile) {
    return URL.createObjectURL(this.selectedFile);
}
    return null;
}
},

    watch: {
    filteredStaffList() {
    // Reset to first page when filter changes
    this.currentPage = 1;
}
},

    methods: {
    // Load staff data from API
    async loadStaff() {
    try {
    this.loading = true;
    this.errorMessage = null;
    const response = await axios.get('/api/staff');
    this.staffList = response.data.data || response.data;
    this.totalStaffCount = this.staffList.length;
    this.filteredStaffList = [...this.staffList];
} catch (error) {
    console.error('Error loading staff:', error);
    this.errorMessage = 'Failed to load staff data. Please try again.';
    this.staffList = [];
    this.filteredStaffList = [];
} finally {
    this.loading = false;
}
},

    // Load branches data from API
    async loadBranches() {
    try {
    const response = await axios.get('/api/branches');
    this.branches = response.data.data || response.data;
} catch (error) {
    console.error('Error loading branches:', error);
    this.branches = [];
}
},

    // Search functionality
    performSearch() {
    // Clear existing timer
    if (this.searchDebounceTimer) {
    clearTimeout(this.searchDebounceTimer);
}

    // Debounce search for better performance
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
    switch (this.searchFilter) {
    case 'name':
    return staff.name && staff.name.toLowerCase().includes(query);
    case 'email':
    return staff.email && staff.email.toLowerCase().includes(query);
    case 'position':
    return staff.position && staff.position.toLowerCase().includes(query);
    case 'phone':
    return staff.phone && staff.phone.includes(query);
    case 'branch':
    return staff.branches && staff.branches.name && staff.branches.name.toLowerCase().includes(query);
    default: // 'all'
    return (staff.name && staff.name.toLowerCase().includes(query)) ||
    (staff.email && staff.email.toLowerCase().includes(query)) ||
    (staff.position && staff.position.toLowerCase().includes(query)) ||
    (staff.phone && staff.phone.includes(query)) ||
    (staff.branches && staff.branches.name && staff.branches.name.toLowerCase().includes(query));
}
});
}

    // Apply position filter
    if (this.selectedPositionFilter) {
    filtered = filtered.filter(staff => staff.position === this.selectedPositionFilter);
}

    this.filteredStaffList = filtered;
},

    clearSearch() {
    this.searchQuery = '';
    this.searchFilter = 'all';
    this.selectedPositionFilter = '';
    this.filteredStaffList = [...this.staffList];
},

    highlightText(text) {
    if (!this.searchQuery.trim() || !text) return text;

    const query = this.searchQuery.trim();
    const regex = new RegExp(`(${this.escapeRegExp(query)})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
},

    // Helper method to escape special regex characters
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
    this.currentPage = 1; // Reset to first page
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

    const offcanvas = new bootstrap.Offcanvas(document.getElementById('staffOffcanvas'));
    offcanvas.show();
},

    openEditModal(staff) {
    this.isEditing = true;
    this.currentStaff = {
    id: staff.id,
    name: staff.name,
    gender: staff.gender,
    email: staff.email,
    phone: staff.phone,
    current_address: staff.current_address,
    position: staff.position,
    salary: staff.salary,
    branches_id: staff.branches_id,
    profile: staff.profile
};
    this.selectedFile = null;

    const offcanvas = new bootstrap.Offcanvas(document.getElementById('staffOffcanvas'));
    offcanvas.show();
},

    viewStaffDetails(staff) {
    this.viewStaff = staff;
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewOffcanvas'));
    offcanvas.show();
},

    // Updated file handling with preview functionality
    handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
    // Validate file size (2MB = 2 * 1024 * 1024 bytes)
    const maxSize = 2 * 1024 * 1024;
    if (file.size > maxSize) {
    Swal.fire({
    icon: 'error',
    title: 'File Too Large',
    text: 'Please select a file smaller than 2MB.',
    confirmButtonColor: '#dc3545'
});
    event.target.value = ''; // Clear the input
    return;
}

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
    if (!allowedTypes.includes(file.type)) {
    Swal.fire({
    icon: 'error',
    title: 'Invalid File Type',
    text: 'Please select a valid image file (JPEG, PNG, JPG, GIF, SVG).',
    confirmButtonColor: '#dc3545'
});
    event.target.value = ''; // Clear the input
    return;
}

    this.selectedFile = file;
}
},

    // New method to clear selected file
    clearSelectedFile() {
    this.selectedFile = null;
    if (this.$refs.profileInput) {
    this.$refs.profileInput.value = '';
}
},

    // Save staff (Add/Edit)
    async saveStaff() {
    try {
    this.saving = true;

    // Create FormData for file upload
    const formData = new FormData();

    // Only append non-empty values
    Object.keys(this.currentStaff).forEach(key => {
    if (this.currentStaff[key] !== null &&
    this.currentStaff[key] !== '' &&
    this.currentStaff[key] !== undefined) {
    formData.append(key, this.currentStaff[key]);
}
});

    // Handle file upload properly
    if (this.selectedFile) {
    formData.append('profile', this.selectedFile);
}

    let response;
    if (this.isEditing) {
    // For updates, use POST with _method override
    formData.append('_method', 'PUT');

    // Make sure to include ID for proper identification
    if (!formData.has('id')) {
    formData.append('id', this.currentStaff.id);
}

    response = await axios.post(`/api/staff/${this.currentStaff.id}`, formData, {
    headers: {
    'Content-Type': 'multipart/form-data',
    'Accept': 'application/json'
}
});
} else {
    response = await axios.post('/api/staff', formData, {
    headers: {
    'Content-Type': 'multipart/form-data',
    'Accept': 'application/json'
}
});
}

    // Close modal first
    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('staffOffcanvas'));
    if (offcanvas) {
    offcanvas.hide();
}

    // Show SweetAlert success message
    await Swal.fire({
    icon: 'success',
    title: this.isEditing ? 'Updated!' : 'Added!',
    text: this.isEditing ? 'Staff updated successfully!' : 'Staff added successfully!',
    confirmButtonColor: '#198754',
    timer: 2000,
    timerProgressBar: true,
    showConfirmButton: false
});

    // Reset form and reload data
    this.resetForm();
    await this.loadStaff();

} catch (error) {
    console.error('Save staff error:', error);

    let errorMessage = 'An error occurred. Please try again.';

    if (error.response && error.response.data) {
    if (error.response.data.errors) {
    // Handle Laravel validation errors
    const errors = Object.values(error.response.data.errors).flat();
    errorMessage = errors.join(', ');
} else if (error.response.data.message) {
    errorMessage = error.response.data.message;
}
}

    // Show SweetAlert error message
    Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: errorMessage,
    confirmButtonColor: '#dc3545'
});
} finally {
    this.saving = false;
}
},

    // Updated resetForm method with file clearing
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
    this.isEditing = false;

    // Reset file input
    if (this.$refs.profileInput) {
    this.$refs.profileInput.value = '';
}
},

    // Delete staff
    async deleteStaff(staffId) {
    const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#1572e8',   // blue
    cancelButtonColor: '#6861ce',    // gray
    confirmButtonText: 'Yes'
});

    if (!result.isConfirmed) return;

    try {
    await axios.delete(`/api/staff/${staffId}`);

    // Reload data
    await this.loadStaff();

    Swal.fire({
    icon: 'success',
    title: 'Deleted!',
    text: 'Staff member has been deleted successfully.',
    confirmButtonColor: '#198754', // green
    timer: 2000,
    timerProgressBar: true,
    showConfirmButton: false
});

} catch (error) {
    console.error('Error deleting staff:', error);
    Swal.fire({
    icon: 'error',
    title: 'Failed!',
    text: 'Failed to delete staff. Please try again.',
    confirmButtonColor: '#dc3545'
});
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

    showNotification(message, type = 'info') {
    // Updated to use SweetAlert for all notifications
    const config = {
    text: message,
    timer: 2000,
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

    // Refresh data
    async refreshData() {
    await this.loadStaff();
    await this.loadBranches();
}
}
}).mount('#app');
