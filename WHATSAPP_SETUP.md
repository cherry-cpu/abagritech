# WhatsApp Integration Setup Guide

This guide will help you set up WhatsApp messaging to send PDF applications to users' mobile numbers.

## Overview

After an application is submitted and PDF is generated, the system automatically sends the PDF via WhatsApp to the applicant's mobile number.

## Supported Providers

1. **Twilio** (Recommended for production)
2. **ChatAPI**
3. **Wati.io**
4. **Gupshup**
5. **Generic HTTP API** (Custom endpoints)

## Setup Instructions

### Option 1: Twilio WhatsApp API (Recommended)

#### Step 1: Create Twilio Account
1. Visit [https://www.twilio.com](https://www.twilio.com)
2. Sign up for a free account
3. Complete account verification

#### Step 2: Get WhatsApp Sandbox Access
1. Log in to Twilio Console
2. Go to **Messaging** → **Try it out** → **Send a WhatsApp message**
3. Follow instructions to join the sandbox (send code to Twilio number)
4. Note your **Account SID** and **Auth Token**

#### Step 3: Get WhatsApp Number
1. Go to **Phone Numbers** → **Manage** → **Buy a number**
2. Select a number with WhatsApp capability
3. Or use the sandbox number for testing

#### Step 4: Configure in config.php
```php
define('WHATSAPP_PROVIDER', 'twilio');
define('WHATSAPP_API_KEY', 'your_account_sid');
define('WHATSAPP_API_SECRET', 'your_auth_token');
define('WHATSAPP_FROM_NUMBER', '14155238886'); // Your Twilio WhatsApp number
define('WHATSAPP_ENABLED', true);
```

**Note:** For production, you need to:
- Request WhatsApp Business API access from Twilio
- Complete business verification
- Get approved WhatsApp Business number

---

### Option 2: ChatAPI

#### Step 1: Create ChatAPI Account
1. Visit [https://chat-api.com](https://chat-api.com)
2. Sign up and create an instance
3. Get your **Instance ID** and **API Token**

#### Step 2: Configure in config.php
```php
define('WHATSAPP_PROVIDER', 'chatapi');
define('WHATSAPP_API_URL', 'https://eu.chat-api.com/instance12345');
define('WHATSAPP_API_KEY', 'your_api_token');
define('WHATSAPP_ENABLED', true);
```

---

### Option 3: Wati.io

#### Step 1: Create Wati Account
1. Visit [https://wati.io](https://wati.io)
2. Sign up for an account
3. Get your **API Token** and **API URL**

#### Step 2: Configure in config.php
```php
define('WHATSAPP_PROVIDER', 'wati');
define('WHATSAPP_API_URL', 'https://api.wati.io');
define('WHATSAPP_API_KEY', 'your_wati_api_token');
define('WHATSAPP_ENABLED', true);
```

---

### Option 4: Gupshup

#### Step 1: Create Gupshup Account
1. Visit [https://www.gupshup.io](https://www.gupshup.io)
2. Sign up and create an app
3. Get your **API Key** and **App Name**

#### Step 2: Configure in config.php
```php
define('WHATSAPP_PROVIDER', 'gupshup');
define('WHATSAPP_API_KEY', 'your_gupshup_api_key');
define('WHATSAPP_API_SECRET', 'your_app_name');
define('WHATSAPP_ENABLED', true);
```

---

### Option 5: Generic HTTP API

If you have a custom WhatsApp API endpoint:

```php
define('WHATSAPP_PROVIDER', 'generic');
define('WHATSAPP_API_URL', 'https://your-api-endpoint.com/send');
define('WHATSAPP_API_KEY', 'your_api_key');
define('WHATSAPP_ENABLED', true);
```

Your API should accept:
- **Method:** POST
- **Headers:** 
  - `Content-Type: application/json`
  - `Authorization: Bearer {api_key}` (if using API key)
- **Body:**
  ```json
  {
    "phone": "919876543210",
    "message": "Your message text",
    "file": {
      "name": "application.pdf",
      "content": "base64_encoded_pdf",
      "type": "application/pdf"
    }
  }
  ```

---

## Phone Number Format

The system automatically handles phone number formatting:
- Removes spaces, dashes, and special characters
- Adds country code if missing (defaults to +91 for India)
- Formats as: `{country_code}{number}` (e.g., `919876543210`)

**Examples:**
- `9876543210` → `919876543210` (India)
- `+91 98765 43210` → `919876543210`
- `+1 234 567 8900` → `12345678900` (US)

---

## Testing

### Test WhatsApp Sending

1. **Enable WhatsApp:**
   ```php
   define('WHATSAPP_ENABLED', true);
   ```

2. **Submit a test application** with a valid phone number

3. **Check PHP error logs** for WhatsApp sending status:
   ```
   WhatsApp message sent successfully to: 919876543210
   ```

4. **Check your phone** for the WhatsApp message with PDF attachment

### Disable WhatsApp (for testing)

```php
define('WHATSAPP_ENABLED', false);
```

---

## Troubleshooting

### WhatsApp Not Sending

1. **Check Configuration:**
   - Verify all API credentials are correct
   - Ensure `WHATSAPP_ENABLED` is set to `true`
   - Check provider name matches your setup

2. **Check Error Logs:**
   - Look for WhatsApp-related errors in PHP error logs
   - Common errors:
     - "WhatsApp API not configured"
     - "Missing configuration"
     - API-specific error messages

3. **Verify Phone Number:**
   - Ensure phone number is in correct format
   - Check that country code is included
   - Verify the number is registered on WhatsApp

4. **Test API Connection:**
   - Use API provider's dashboard to test sending
   - Verify API credentials are valid
   - Check API rate limits

### Common Issues

**Issue:** "WhatsApp API not configured"
- **Solution:** Add API credentials to `config.php`

**Issue:** "PDF file not found"
- **Solution:** Ensure PDF generation is working correctly
- Check that `uploads/` directory exists and is writable

**Issue:** "Failed to send WhatsApp message"
- **Solution:** 
  - Verify API credentials
  - Check API provider status
  - Ensure phone number format is correct
  - Check API rate limits

**Issue:** Message sent but PDF not received
- **Solution:**
  - Some providers require PDF to be hosted on public URL
  - Upload PDF to a public location first
  - Use the public URL in the message

---

## Message Format

The WhatsApp message sent includes:
```
Dear [Full Name],

Your exam application has been submitted successfully!

Application ID: [APPLICATION_ID]
Please find your application PDF attached.

Thank you for applying!
Aakasha Bindu Agritech
```

---

## Security Notes

1. **Never commit API credentials** to version control
2. **Use environment variables** for sensitive data in production
3. **Enable HTTPS** for API communications
4. **Validate phone numbers** before sending
5. **Implement rate limiting** to prevent abuse
6. **Log all WhatsApp activities** for audit purposes

---

## Cost Considerations

- **Twilio:** Pay-per-message pricing (varies by country)
- **ChatAPI:** Subscription-based plans
- **Wati.io:** Subscription-based plans
- **Gupshup:** Pay-per-message pricing

Check each provider's pricing before choosing.

---

## Production Checklist

- [ ] WhatsApp API account created and verified
- [ ] API credentials configured in `config.php`
- [ ] Phone number format tested
- [ ] PDF generation working correctly
- [ ] WhatsApp sending tested with real numbers
- [ ] Error handling implemented
- [ ] Logging configured
- [ ] Rate limiting considered
- [ ] Privacy policy updated (if required)

---

## Support

For provider-specific help:
- **Twilio:** [https://support.twilio.com](https://support.twilio.com)
- **ChatAPI:** [https://chat-api.com/docs](https://chat-api.com/docs)
- **Wati.io:** [https://wati.io/support](https://wati.io/support)
- **Gupshup:** [https://docs.gupshup.io](https://docs.gupshup.io)

