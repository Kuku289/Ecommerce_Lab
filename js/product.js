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
                closeModal();
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

// Upload product image
function uploadProductImage(productId) {
    const fileInput = document.getElementById('product_image_file');
    const file = fileInput.files[0];

    if (!file) {
        showMessage('Please select an image to upload', 'error');
        return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        showMessage('Only JPG, PNG, and GIF images are allowed', 'error');
        return;
    }

    // Validate file size (5MB)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        showMessage('Image size must be less than 5MB', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('product_image', file);
    formData.append('product_id', productId);

    fetch('../actions/upload_product_image_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            // Set the image path in the hidden input
            document.getElementById('product_image').value = data.image_path;
            // Display preview
            displayImagePreview(data.image_path);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while uploading the image', 'error');
    });
}

// Display image preview
function displayImagePreview(imagePath) {
    const preview = document.getElementById('imagePreview');
    if (preview) {
        preview.innerHTML = `<img src="../${imagePath}" alt="Product Image" style="max-width: 200px; max-height: 200px;">`;
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
            <td>$${parseFloat(product.product_price).toFixed(2)}</td>
            <td>
                ${product.product_image ? 
                    `<img src="../${product.product_image}" alt="${product.product_title}" style="max-width: 50px; max-height: 50px;">` : 
                    'No image'}
            </td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editProduct(${product.product_id})">Edit</button>
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
    const submitBtn = document.querySelector('#productForm button[type="submit"]');
    if (submitBtn) {
        submitBtn.textContent = 'Update Product';
    }
    
    // Show modal or scroll to form
    showEditModal();
}

// Show message modal/popup
function showMessage(message, type) {
    // You can customize this based on your UI framework
    // This is a simple alert implementation
    if (type === 'success') {
        alert('✓ ' + message);
    } else {
        alert('✗ ' + message);
    }
}

// Helper functions for modal (implement based on your UI)
function showEditModal() {
    // Implement based on your modal system
    // For example, if using Bootstrap:
    // $('#editModal').modal('show');
}

function closeModal() {
    // Implement based on your modal system
}

// Load products on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('productsTableBody')) {
        loadProducts();
    }
});