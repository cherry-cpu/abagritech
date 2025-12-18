# Razorpay Payment Gateway Setup Guide

## Overview
The exam application form now includes Razorpay payment gateway integration for processing exam fees (₹1,200/-).

## Setup Instructions

### 1. Create Razorpay Account
- Visit [https://razorpay.com](https://razorpay.com)
- Sign up for a Razorpay account
- Complete the KYC verification process

### 2. Get API Keys
- Log in to your Razorpay Dashboard
- Go to Settings → API Keys
- Generate Test Keys (for testing) or Live Keys (for production)
- Copy your **Key ID** and **Key Secret**

### 3. Update Configuration
Open `exam_application.html` and update the following:

```javascript
// Line ~310
const RAZORPAY_KEY_ID = 'rzp_test_XXXXXXXXXXXXX'; // Replace with your Key ID
```

**Note:** For production, replace test keys with live keys.

### 4. Backend Setup (Recommended)
For production use, you should:
- Create a backend API endpoint to create orders
- Verify payment signatures on the server
- Store payment records in database
- Set up webhooks for payment notifications

### 5. Test Payment Flow
1. Fill in the application form (Name, Email, Phone, Position)
2. Click "Pay ₹1,200/- Now" button
3. Complete payment using Razorpay test cards:
   - Card Number: 4111 1111 1111 1111
   - CVV: Any 3 digits
   - Expiry: Any future date
   - Name: Any name
4. After successful payment, transaction ID will be auto-filled
5. Submit the application form

## Payment Flow
1. User fills required fields (Name, Email, Phone, Position)
2. User clicks payment button
3. Razorpay checkout opens
4. User completes payment
5. Transaction ID is auto-filled in the form
6. Submit button is enabled
7. User can submit the application

## Security Notes
- Never expose your Key Secret in frontend code
- Always verify payments on the backend
- Use HTTPS in production
- Implement proper error handling
- Set up webhooks for payment status updates

## Support
For Razorpay integration help, visit:
- Documentation: https://razorpay.com/docs/
- Support: support@razorpay.com

