// Debug script for checkout form issues
console.log('Debug: Checkout form loaded');

$(document).ready(function() {
    console.log('Debug: Document ready');
    
    // Monitor form field changes
    $('#number').on('input', function() {
        console.log('Debug: Phone number changed to:', $(this).val());
    });
    
    $('input[name="address"]').on('change', function() {
        console.log('Debug: Address selection changed to:', $(this).val());
    });
    
    // Monitor form submission
    $('#payment-form').on('submit', function(e) {
        console.log('Debug: Form submission attempt');
        console.log('Debug: Phone value:', $('#number').val());
        console.log('Debug: Country code:', $('#code').val());
        console.log('Debug: Selected address:', $('input[name="address"]:checked').val());
    });
    
    // Monitor any field resets
    $('input').on('reset', function() {
        console.log('Debug: Field reset detected on:', this.name);
    });
});