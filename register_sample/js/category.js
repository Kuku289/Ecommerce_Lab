$(document).ready(function() {
    loadCategories();
    
    // Add category form submission
    $('#addCategoryForm').submit(function(e) {
        e.preventDefault();
        
        var categoryName = $('#categoryName').val().trim();
        
        if (categoryName == '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Category name is required!'
            });
            return;
        }
        
        if (categoryName.length < 2 || categoryName.length > 100) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Category name must be between 2-100 characters!'
            });
            return;
        }
        
        $.ajax({
            url: '../actions/add_category_action.php',
            type: 'POST',
            data: {
                category_name: categoryName
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    $('#categoryName').val('');
                    loadCategories();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });
});

// Load categories function
function loadCategories() {
    $.ajax({
        url: '../actions/fetch_category_action.php',
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                displayCategories(response.categories);
            } else {
                $('#categoriesTable').html('<div class="alert alert-warning">No categories found.</div>');
            }
        },
        error: function() {
            $('#categoriesTable').html('<div class="alert alert-danger">Failed to load categories.</div>');
        }
    });
}

// Display categories in table
function displayCategories(categories) {
    if (categories.length === 0) {
        $('#categoriesTable').html('<div class="alert alert-info">No categories found. Add your first category above.</div>');
        return;
    }
    
    var html = '<table class="table table-striped table-hover">';
    html += '<thead><tr><th>ID</th><th>Category Name</th><th>Actions</th></tr></thead>';
    html += '<tbody>';
    
    categories.forEach(function(category) {
        html += '<tr>';
        html += '<td>' + category.cat_id + '</td>';
        html += '<td>' + category.cat_name + '</td>';
        html += '<td>';
        html += '<button class="btn btn-sm btn-warning me-2" onclick="editCategory(' + category.cat_id + ', \'' + category.cat_name + '\')">';
        html += '<i class="fas fa-edit"></i> Edit</button>';
        html += '<button class="btn btn-sm btn-danger" onclick="deleteCategory(' + category.cat_id + ', \'' + category.cat_name + '\')">';
        html += '<i class="fas fa-trash"></i> Delete</button>';
        html += '</td>';
        html += '</tr>';
    });
    
    html += '</tbody></table>';
    $('#categoriesTable').html(html);
}

// Edit category function
function editCategory(catId, catName) {
    $('#editCatId').val(catId);
    $('#editCategoryName').val(catName);
    $('#editModal').modal('show');
}

// Update category function
function updateCategory() {
    var catId = $('#editCatId').val();
    var categoryName = $('#editCategoryName').val().trim();
    
    if (categoryName == '') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Category name is required!'
        });
        return;
    }
    
    if (categoryName.length < 2 || categoryName.length > 100) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Category name must be between 2-100 characters!'
        });
        return;
    }
    
    $.ajax({
        url: '../actions/update_category_action.php',
        type: 'POST',
        data: {
            cat_id: catId,
            category_name: categoryName
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#editModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                });
                loadCategories();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred. Please try again.'
            });
        }
    });
}

// Delete category function
function deleteCategory(catId, catName) {
    Swal.fire({
        title: 'Delete Category',
        text: 'Are you sure you want to delete "' + catName + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../actions/delete_category_action.php',
                type: 'POST',
                data: {
                    cat_id: catId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: response.message
                        });
                        loadCategories();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.'
                    });
                }
            });
        }
    });
}