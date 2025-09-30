/**
 * Category Management JavaScript
 * File: js/category.js
 */

// Load categories when page loads
$(document).ready(function() {
    loadCategories();
});

// Add Category Form Submission
$('#addCategoryForm').on('submit', function(e) {
    e.preventDefault();
    
    const categoryName = $('#categoryName').val().trim();
    
    // Validate input
    if (!categoryName || categoryName.length < 2) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Input',
            text: 'Category name must be at least 2 characters'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Adding Category...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send AJAX request
    $.ajax({
        url: '../actions/add_category_action.php',
        type: 'POST',
        data: {
            category_name: categoryName
        },
        dataType: 'json',
        success: function(response) {
            console.log('Add category response:', response);
            
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 2000
                });
                
                // Clear form and reload categories
                $('#categoryName').val('');
                loadCategories();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to add category'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                response: xhr.responseText
            });
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while adding the category. Check console for details.'
            });
        }
    });
});

// Load Categories
function loadCategories() {
    $.ajax({
        url: '../actions/get_categories_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Load categories response:', response);
            
            if (response.status === 'success') {
                displayCategories(response.categories);
            } else {
                $('#categoriesTable').html(
                    '<div class="alert alert-warning">' +
                    '<i class="fas fa-exclamation-triangle"></i> ' + 
                    (response.message || 'Failed to load categories') +
                    '</div>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Load categories error:', error);
            $('#categoriesTable').html(
                '<div class="alert alert-danger">' +
                '<i class="fas fa-times-circle"></i> Error loading categories' +
                '</div>'
            );
        }
    });
}

// Display Categories in Table
function displayCategories(categories) {
    if (!categories || categories.length === 0) {
        $('#categoriesTable').html(
            '<div class="alert alert-info">' +
            '<i class="fas fa-info-circle"></i> No categories found. Add your first category!' +
            '</div>'
        );
        return;
    }
    
    let tableHTML = `
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    categories.forEach((category, index) => {
        tableHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${escapeHtml(category.cat_name)}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    });
    
    tableHTML += `
                </tbody>
            </table>
        </div>
    `;
    
    $('#categoriesTable').html(tableHTML);
}

// Edit Category
function editCategory(catId, catName) {
    $('#editCatId').val(catId);
    $('#editCategoryName').val(catName);
    
    // Show modal
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

// Update Category
function updateCategory() {
    const catId = $('#editCatId').val();
    const categoryName = $('#editCategoryName').val().trim();
    
    // Validate input
    if (!categoryName || categoryName.length < 2) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Input',
            text: 'Category name must be at least 2 characters'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Updating Category...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send AJAX request
    $.ajax({
        url: '../actions/update_category_action.php',
        type: 'POST',
        data: {
            cat_id: catId,
            category_name: categoryName
        },
        dataType: 'json',
        success: function(response) {
            console.log('Update category response:', response);
            
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 2000
                });
                
                // Close modal and reload categories
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                loadCategories();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to update category'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Update error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while updating the category'
            });
        }
    });
}

// Delete Category
function deleteCategory(catId, catName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to delete "${catName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting Category...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request
            $.ajax({
                url: '../actions/delete_category_action.php',
                type: 'POST',
                data: {
                    cat_id: catId
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Delete category response:', response);
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            timer: 2000
                        });
                        
                        // Reload categories
                        loadCategories();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to delete category'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while deleting the category'
                    });
                }
            });
        }
    });
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}