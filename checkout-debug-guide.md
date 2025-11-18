# Checkout Form Debugging Guide

## Quick Debug Steps:

1. **Open Browser Developer Tools (F12)**
2. **Go to Console Tab** 
3. **Navigate to checkout page**
4. **Look for these debug messages:**
   - "Debug: Checkout form loaded"
   - "Debug: Document ready"
   - "Debug: Initializing Google Maps"

## If You See JavaScript Errors:

### Common Error Types:
- `intlTelInput is not a function` → Phone input library issue
- `google is not defined` → Google Maps API loading issue  
- `Cannot read property of undefined` → Variable initialization issue

### Immediate Fixes:

1. **Disable Google Maps temporarily:**
   ```html
   <!-- Comment out this line in checkout.blade.php -->
   <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=..."></script> -->
   ```

2. **Test phone input separately:**
   - Try entering phone number without selecting address
   - Check if country code dropdown works

3. **Check network tab:**
   - Look for failed API requests
   - Verify all JS/CSS files load successfully

## Form Field Behavior to Test:

1. **Phone Number Field:**
   - Enter a number → Check if it stays
   - Change country → Check if number persists
   - Submit form with validation error → Check if field clears

2. **Address Selection:**
   - Select an address radio button
   - Check if selection persists after other form interactions

## Quick Temporary Fix:

If issues persist, add this to checkout page temporarily:

```javascript
<script>
$(document).ready(function() {
    // Disable all form resets
    $('form').on('reset', function(e) {
        e.preventDefault();
        console.log('Form reset prevented');
    });
    
    // Log all input changes
    $('input').on('input change', function() {
        console.log('Field changed:', this.name, this.value);
    });
});
</script>
```