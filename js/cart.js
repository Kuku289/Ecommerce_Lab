$(document).ready(function() {
    
    /**
     * Update cart item quantity
     */
    $('.quantity-input').on('change', function() {
        const productId = $(this).data('product-id');
        const quantity = parseInt($(this).val());
        
        if (quantity <= 0) {
            alert('Quantity must be greater than 0');
            $(this).val(1);
            return;
        }
        
        updateCartQuantity(productId, quantity);
    });
    
    /**
     * Remove item from cart
     */
    $('.remove-btn').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to remove this item?')) {
            return;
        }
        
        const productId = $(this).data('product-id');
        removeFromCart(productId);
    });
    
    /**
     * Empty cart
     */
    $('#empty-cart-btn').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to empty your cart?')) {
            return;
        }
        
        emptyCart();
    });
    
    /**
     * Function to update quantity
     */
    function updateCartQuantity(productId, quantity) {
        $.ajax({
            url: '../actions/update_quantity_action.php',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Reload page to show updated totals
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while updating cart');
            }
        });
    }
    
    /**
     * Function to remove item from cart
     */
    function removeFromCart(productId) {
        $.ajax({
            url: '../actions/remove_from_cart_action.php',
            type: 'POST',
            data: {
                product_id: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Reload page to show updated cart
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while removing item');
            }
        });
    }
    
    /**
     * Function to empty cart
     */
    function emptyCart() {
        $.ajax({
            url: '../actions/empty_cart_action.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Reload page to show empty cart
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while emptying cart');
            }
        });
    }
});

/**
 * Add to cart function (for product pages)
 */
function addToCart(productId, quantity = 1) {
    $.ajax({
        url: '../actions/add_to_cart_action.php',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('✓ ' + response.message);
                // Update cart count if element exists
                if ($('#cart-count').length) {
                    $('#cart-count').text(response.cart_count);
                }
            } else {
                alert('✗ ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while adding to cart');
        }
    });
}