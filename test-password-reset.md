# Test Password Reset Functionality

## Current Status: ❌ NOT WORKING
**Reason**: No email configuration set up

## Steps to Test After Configuration:

1. **Go to**: https://foodbank.dev.platco.ai/password/reset
2. **Enter email**: muhammadab321@gmail.com
3. **Click**: "Send Password Reset Link"
4. **Expected**: Email sent confirmation
5. **Check**: Email inbox for reset link

## Quick Fix Commands:

```bash
# Test current mail config
php artisan tinker --execute="
echo 'Mail Test:';
try {
    Mail::raw('Test email', function(\$message) {
        \$message->to('test@example.com')->subject('Test');
    });
    echo 'Email sent successfully';
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage();
}
"
```

## Configuration Needed:
- MAIL_HOST (e.g., smtp.gmail.com)
- MAIL_PORT (e.g., 587)
- MAIL_USERNAME (your email)
- MAIL_PASSWORD (app password)
- MAIL_ENCRYPTION (tls/ssl)

## Alternative Solutions:
1. Use log driver for testing: `MAIL_MAILER=log`
2. Use Mailgun service
3. Use SendGrid SMTP
4. Configure Gmail SMTP