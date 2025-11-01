// Validate product form
function validateProductForm() {
    const category = document.getElementById('product_cat').value;
    const brand = document.getElementById('product_brand').value;
    const title = document.getElementById('product_title').value.trim();
    const price = document.getElementById('product_price').value;
    const description = document.getElementById('product_desc').value.trim();
    const keywords = document.getElementById('product_keywords').value.trim();

    if (!category || category === '') {
        showMessage('Please select a category', 'error');
        return false;
    }

    if (!brand || brand === '') {
        showMessage('Please select a brand', 'error');
        return false;
    }

    if (title === '') {
        showMessage('Product title is required', 'error');
        return false;
    }

    if (title.length < 3) {
        showMessage('Product title must be at least 3 characters', 'error');
        return false;
    }

    if (price === '' || isNaN(price) || parseFloat(price) <= 0) {
        showMessage('Please enter a valid price greater than 0', 'error');
        return false;
    }

    if (description === '') {
        showMessage('Product description is required', 'error');
        return false;
    }

    if (description.length < 10) {
        showMessage('Product description must be at least 10 characters', 'error');
        return false;
    }

    if (keywords === '') {
        showMessage('Product keywords are required', 'error');
        return false;
    }

    return true;
}

// Add product
function addProduct(event) {
    event.preventDefault();

    if (!validateProductForm()) {
        return;
    }

    const formData = new FormData(document.getElementById('productForm'));

    fetch('../actions/add_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            document.getElementById('productForm').reset();
            // Reload products
            setTimeout(() => {
                loadProducts();
            }, 1500);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while adding the product', 'error');
    });
}

// Update product
function updateProduct(event) {
    event.preventDefault();

    if (!validateProductForm()) {
        return;
    }

    const formData = new FormData(document.getElementById('productForm'));

    fetch('../actions/update_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            // Reload products
            setTimeout(() => {
                loadProducts();
                resetForm();
            }, 1500);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while updating the product', 'error');
    });
}

// Delete product
function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);

    fetch('../actions/delete_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            // Reload products
            setTimeout(() => {
                loadProducts();
            }, 1500);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while deleting the product', 'error');
    });
}

// ⭐ UPDATED: Upload product image function
function uploadProductImage(productId) {
    const fileInput = document.getElementById('product_image_file');
    const file = fileInput.files[0];

    // Validate file selection
    if (!file) {
        showMessage('Please select an image file first', 'error');
        return;
    }

    // Validate product ID
    if (!productId || productId == 0) {
        showMessage('Please save the product first before uploading an image', 'error');
        return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showMessage('Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed', 'error');
        return;
    }

    // Validate file size (5MB)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        showMessage('Image size must be less than 5MB', 'error');
        return;
    }

    // Create FormData
    const formData = new FormData();
    formData.append('product_image', file);
    formData.append('product_id', productId);

    // Show loading state
    const uploadBtn = event.target;
    const originalText = uploadBtn.innerHTML;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    uploadBtn.disabled = true;

    // Send AJAX request
    fetch('../actions/upload_product_image_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            
            // Update hidden input
            document.getElementById('product_image').value = data.image_path;
            
            // Display preview with success styling
            displayImagePreview(data.image_path, true);
            
            // Reload products table
            loadProducts();
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while uploading the image', 'error');
    })
    .finally(() => {
        // Restore button state
        uploadBtn.innerHTML = originalText;
        uploadBtn.disabled = false;
    });
}

// ⭐ UPDATED: Display image preview
function displayImagePreview(imagePath, isUploaded = false) {
    const preview = document.getElementById('imagePreview');
    if (preview) {
        if (isUploaded) {
            // Show with success message if just uploaded
            preview.innerHTML = `
                <div class="mt-2">
                    <p class="text-success"><i class="fas fa-check-circle"></i> Image uploaded successfully!</p>
                    <img src="../${imagePath}" alt="Product Image" 
                         style="max-width: 200px; border-radius: 5px; border: 2px solid #28a745;">
                </div>
            `;
        } else {
            // Show normal preview for existing images
            preview.innerHTML = `
                <img src="../${imagePath}" alt="Product Image" 
                     style="max-width: 200px; max-height: 200px; margin-top: 10px; border-radius: 5px; border: 2px solid #dee2e6;">
            `;
        }
    }
}

// Load products
function loadProducts() {
    fetch('../actions/fetch_product_action.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayProducts(data.data);
        } else {
            console.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while loading products', 'error');
    });
}

// Display products in table
function displayProducts(products) {
    const tbody = document.getElementById('productsTableBody');
    
    if (!tbody) return;
    
    tbody.innerHTML = '';

    if (products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No products found</td></tr>';
        return;
    }

    products.forEach(product => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${product.product_id}</td>
            <td>${product.cat_name || 'N/A'}</td>
            <td>${product.brand_name || 'N/A'}</td>
            <td>${product.product_title}</td>
            <td>GH₵${parseFloat(product.product_price).toFixed(2)}</td>
            <td>
                ${product.product_image ? 
                    `<img src="../${product.product_image}" alt="${product.product_title}" class="product-image">` : 
                    'No image'}
            </td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editProduct(${product.product_id})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.product_id})" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Edit product - load product data
function editProduct(productId) {
    fetch(`../actions/get_product_action.php?product_id=${productId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateEditForm(data.data);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while loading product details', 'error');
    });
}

// Populate edit form
function populateEditForm(product) {
    document.getElementById('product_id').value = product.product_id;
    document.getElementById('product_cat').value = product.product_cat;
    document.getElementById('product_brand').value = product.product_brand;
    document.getElementById('product_title').value = product.product_title;
    document.getElementById('product_price').value = product.product_price;
    document.getElementById('product_desc').value = product.product_desc;
    document.getElementById('product_keywords').value = product.product_keywords;
    document.getElementById('product_image').value = product.product_image || '';
    
    // Display image preview if exists
    if (product.product_image) {
        displayImagePreview(product.product_image);
    }
    
    // Change form submit to update
    const form = document.getElementById('productForm');
    form.onsubmit = updateProduct;
    
    // Change button text
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Product';
    }
    
    // Change form title
    const formTitle = document.getElementById('formTitle');
    if (formTitle) {
        formTitle.textContent = 'Edit Product';
    }
    
    // Scroll to form
    document.getElementById('productForm').scrollIntoView({ behavior: 'smooth' });
}

// Reset form
function resetForm() {
    document.getElementById('productForm').reset();
    document.getElementById('product_id').value = '';
    document.getElementById('product_image').value = '';
    document.getElementById('imagePreview').innerHTML = '';
    
    // Reset form to add mode
    document.getElementById('productForm').onsubmit = addProduct;
    
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Add Product';
    }
    
    const formTitle = document.getElementById('formTitle');
    if (formTitle) {
        formTitle.textContent = 'Add New Product';
    }
}

// Show message modal/popup
function showMessage(message, type) {
    // Check if showMessage is defined elsewhere (in the HTML), if not use alert
    if (typeof window.showMessage === 'undefined') {
        if (type === 'success') {
            alert('✓ ' + message);
        } else {
            alert('✗ ' + message);
        }
    }
}

// ⭐ NEW: Preview image when file is selected (before upload)
function previewSelectedImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = `
                <div class="mt-2">
                    <p class="text-muted"><small>Preview (not yet uploaded):</small></p>
                    <img src="${e.target.result}" alt="Preview" 
                         style="max-width: 200px; border-radius: 5px; border: 2px solid #dee2e6;">
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
}

// Load products on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('productsTableBody')) {
        loadProducts();
    }
    
    // Add event listener for file input preview
    const fileInput = document.getElementById('product_image_file');
    if (fileInput) {
        fileInput.addEventListener('change', previewSelectedImage);
    }
});