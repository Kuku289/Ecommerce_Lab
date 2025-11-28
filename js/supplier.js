$(document).ready(function() {
    
    /**
     * Add Supplier Form
     */
    $('#add-supplier-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '../actions/add_supplier_action.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('✓ ' + response.message);
                    location.reload();
                } else {
                    alert('✗ ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while adding supplier');
            }
        });
    });
    
    /**
     * Verify Supplier
     */
    $('.verify-supplier-btn').on('click', function() {
        const supplierId = $(this).data('supplier-id');
        $('#verify-supplier-id').val(supplierId);
        $('#verifyModal').modal('show');
    });
    
    /**
     * Submit Verification
     */
    $('#verify-supplier-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '../actions/verify_supplier_action.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('✓ ' + response.message);
                    $('#verifyModal').modal('hide');
                    location.reload();
                } else {
                    alert('✗ ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while verifying supplier');
            }
        });
    });
    
    /**
     * Upload Certification
     */
    $('#certification-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '../actions/upload_certification_action.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('✓ ' + response.message);
                    location.reload();
                } else {
                    alert('✗ ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while uploading certification');
            }
        });
    });
    
    /**
     * Delete Supplier
     */
    $('.delete-supplier-btn').on('click', function() {
        if (!confirm('Are you sure you want to delete this supplier? This will remove all associated data.')) {
            return;
        }
        
        const supplierId = $(this).data('supplier-id');
        
        $.ajax({
            url: '../actions/delete_supplier_action.php',
            type: 'POST',
            data: { supplier_id: supplierId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('✓ ' + response.message);
                    location.reload();
                } else {
                    alert('✗ ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting supplier');
            }
        });
    });
});