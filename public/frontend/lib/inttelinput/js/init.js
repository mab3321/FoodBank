
$(function () {
    var country_code_name = '';
    
    // Store initial values to prevent reset
    var initialPhoneValue = $('#number').val();
    var initialCountryCode = $('#code').val();
    var initialCountryName = $('#code_name').val();
    
    $('#number').intlTelInput({
        autoHideDialCode: true,
        autoPlaceholder: "ON",
        dropdownContainer: document.body,
        formatOnDisplay: true,
        initialCountry: initialCountryName || country_code_name || 'us',
        placeholderNumberType: "MOBILE",
        preferredCountries: ['us','gb','in'],
        separateDialCode: true
    });
    
    // Restore initial values if they exist
    if (initialPhoneValue) {
        $('#number').val(initialPhoneValue);
    }
    if (initialCountryCode) {
        $('#code').val(initialCountryCode);
    }
});


$("#number").on('countrychange', function(e, countryData) {
    try {
        var selectedData = $("#number").intlTelInput("getSelectedCountryData");
        if (selectedData) {
            var code = selectedData.dialCode;
            var code_name = selectedData.iso2;
            
            $("#code").val(code);
            $("#code_name").val(code_name);
            
            console.log('Country changed to:', code_name, 'Code:', code);
        }
    } catch (error) {
        console.error('Error in country change handler:', error);
    }
});
