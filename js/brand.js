/**
 * Brand Management JavaScript
 * File: js/brand.js
 */

$(document).ready(function() {
    loadBrands();
});

// Add Brand Form Submission
$('#addBrandForm').on('submit', function(e) {
    e.preventDefault();
    
    const brandName = $('#brandName').val().trim();
    const catId = $('#categorySelect').val();
    
    if (!catId || catId === '') {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Input',
            text: 'Please select a category'
        });
        return;
    }
    
    if (!brandName || brandName.length < 2) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Input',
            text: 'Brand name must be at least 2 characters'
        });
        return;
    }
    
    Swal.fire({
        title: 'Adding Brand...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../actions/add_brand_action.php',
        type: 'POST',
        data: {
            brand_name: brandName,
            cat_id: catId
        },
        dataType: 'json',
        success: function(response) {
            console.log('Add brand response:', response);
            
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 2000
                });
                
                $('#brandName').val('');
                $('#categorySelect').val('');
                loadBrands();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to add brand'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while adding the brand.'
            });
        }
    });
});

function loadBrands() {
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Load brands response:', response);
            
            if (response.status === 'success') {
                displayBrands(response.brands);
            } else {
                $('#brandsTable').html(
                    '<div class="alert alert-warning">' +
                    '<i class="fas fa-exclamation-triangle"></i> ' + 
                    (response.message || 'Failed to load brands') +
                    '</div>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Load brands error:', error);
            $('#brandsTable').html(
                '<div class="alert alert-danger">' +
                '<i class="fas fa-times-circle"></i> Error loading brands' +
                '</div>'
            );
        }
    });
}

function displayBrands(brands) {
    if (!brands || brands.length === 0) {
        $('#brandsTable').html(
            '<div class="alert alert-info">' +
            '<i class="fas fa-info-circle"></i> No brands found. Add your first brand!' +
            '</div>'
        );
        return;
    }
    
    let groupedBrands = {};
    brands.forEach(brand => {
        const catName = brand.cat_name || 'Uncategorized';
        if (!groupedBrands[catName]) {
            groupedBrands[catName] = [];
        }
        groupedBrands[catName].push(brand);
    });
    
    let tableHTML = '<div class="accordion" id="brandsAccordion">';
    let accordionIndex = 0;
    
    for (let catName in groupedBrands) {
        const catBrands = groupedBrands[catName];
        const collapseId = 'collapse' + accordionIndex;
        const headingId = 'heading' + accordionIndex;
        
        tableHTML += `
            <div class="accordion-item">
                <h2 class="accordion-header" id="${headingId}">
                    <button class="accordion-button ${accordionIndex > 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                        <i class="fas fa-tag me-2"></i> ${escapeHtml(catName)} 
                        <span class="badge bg-secondary ms-2">${catBrands.length}</span>
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse ${accordionIndex === 0 ? 'show' : ''}" data-bs-parent="#brandsAccordion">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Brand Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
        `;
        
        catBrands.forEach((brand, index) => {
            const brandData = JSON.stringify(brand).replace(/"/g, '&quot;');
            tableHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${escapeHtml(brand.brand_name)}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick='editBrand(${brandData})'>
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteBrand(${brand.brand_id}, '${escapeHtml(brand.brand_name).replace(/'/g, "\\'")}')">
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
                    </div>
                </div>
            </div>
        `;
        accordionIndex++;
    }
    
    tableHTML += '</div>';
    $('#brandsTable').html(tableHTML);
}

function editBrand(brand) {
    $('#editBrandId').val(brand.brand_id);
    $('#editBrandName').val(brand.brand_name);
    $('#editCategoryName').val(brand.cat_name);
    
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

function updateBrand() {
    const brandId = $('#editBrandId').val();
    const brandName = $('#editBrandName').val().trim();
    
    if (!brandName || brandName.length < 2) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Input',
            text: 'Brand name must be at least 2 characters'
        });
        return;
    }
    
    Swal.fire({
        title: 'Updating Brand...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../actions/update_brand_action.php',
        type: 'POST',
        data: {
            brand_id: brandId,
            brand_name: brandName
        },
        dataType: 'json',
        success: function(response) {
            console.log('Update brand response:', response);
            
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 2000
                });
                
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                loadBrands();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to update brand'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Update error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while updating the brand'
            });
        }
    });
}

function deleteBrand(brandId, brandName) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to delete "' + brandName + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting Brand...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '../actions/delete_brand_action.php',
                type: 'POST',
                data: {
                    brand_id: brandId
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Delete brand response:', response);
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            timer: 2000
                        });
                        
                        loadBrands();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to delete brand'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while deleting the brand'
                    });
                }
            });
        }
    });
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}