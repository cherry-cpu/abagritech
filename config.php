<?php
// Database configuration file
// Update these values according to your database setup

define('DB_HOST', 'ls-09293e762cbb8c1e5c806444a82e3a4fe2f22b56.cfiikwus2tht.ap-south-1.rds.amazonaws.com');
define('DB_NAME', 'dbmaster');
define('DB_USER', 'dbmasteruser');
define('DB_PASS', 'Bu:ws6j,sFpdH~%WF<0kHYc3)x2Z[<T<');
define('DB_CHARSET', 'utf8mb4');

// Upload directory configuration
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 104857600);// 1 MB in bytes
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png','jpeg']);

// Application settings
define('EXAM_FEE', 1200);
define('RAZORPAY_KEY_ID', 'rzp_live_RrvL9p6OF2hGP1'); // Replace with your Razorpay Key ID
define('RAZORPAY_KEY_SECRET', 'OK5bMD066Z4PCeLdc4YxoW8U'); // Replace with your Razorpay Key Secret (keep secret!)

// Timezone
date_default_timezone_set('Asia/Kolkata');

// WhatsApp API Configuration
// Provider options: 'twilio', 'chatapi', 'wati', 'gupshup', or 'generic'
define('WHATSAPP_PROVIDER', 'twilio'); // Change to your preferred provider

// Twilio Configuration (if using Twilio)
define('WHATSAPP_API_KEY', ''); // Twilio Account SID
define('WHATSAPP_API_SECRET', ''); // Twilio Auth Token
define('WHATSAPP_FROM_NUMBER', ''); // Your Twilio WhatsApp number (e.g., 14155238886)

// ChatAPI Configuration (if using ChatAPI)
// define('WHATSAPP_API_URL', 'https://eu.chat-api.com/instance12345');
// define('WHATSAPP_API_KEY', 'your_chatapi_token');

// Wati.io Configuration (if using Wati)
// define('WHATSAPP_API_URL', 'https://api.wati.io');
// define('WHATSAPP_API_KEY', 'your_wati_api_token');

// Gupshup Configuration (if using Gupshup)
// define('WHATSAPP_API_KEY', 'your_gupshup_api_key');
// define('WHATSAPP_API_SECRET', 'your_gupshup_app_name');

// Generic API Configuration (if using custom API)
// define('WHATSAPP_API_URL', 'https://your-api-endpoint.com/send');
// define('WHATSAPP_API_KEY', 'your_api_key');

// Enable/Disable WhatsApp sending
define('WHATSAPP_ENABLED', true); // Set to false to disable WhatsApp sending
