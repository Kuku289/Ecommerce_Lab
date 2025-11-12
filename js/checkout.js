$(document).ready(function() {
    
    /**
     * Simulate Payment Button
     */
    $('#simulate-payment-btn').on('click', function(e) {
        e.preventDefault();
        
        // Show payment modal
        $('#paymentModal').modal('show');
    });
    
    /**
     * Confirm Payment
     */
    $('#confirm-payment-btn').on('click', function() {
        // Disable button to prevent double submission
        $(this).prop('disabled', true).text('Processing...');
        
        // Process checkout
        $.ajax({
            url: '../actions/process_checkout_action.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Hide payment modal
                    $('#paymentModal').modal('hide');
                    
                    // Show success message
                    $('#success-invoice').text(response.invoice_no);
                    $('#success-amount').text('GH₵' + response.total_amount);
                    $('#successModal').modal('show');
                    
                    // Redirect after 3 seconds
                    setTimeout(function() {
                        window.location.href = '../index.php';
                    }, 3000);
                } else {
                    alert('Error: ' + response.message);
                    $('#confirm-payment-btn').prop('disabled', false).text('Yes, I\'ve Paid');
                }
            },
            error: function() {
                alert('An error occurred during checkout');
                $('#confirm-payment-btn').prop('disabled', false).text('Yes, I\'ve Paid');
            }
        });
    });
    
    /**
     * Cancel Payment
     */
    $('#cancel-payment-btn').on('click', function() {
        $('#paymentModal').modal('hide');
    });
});