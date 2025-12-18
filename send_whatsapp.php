<?php
/**
 * WhatsApp Message Sending Helper
 * Supports multiple WhatsApp API providers
 */

/**
 * Send PDF via WhatsApp to mobile number
 * 
 * @param string $phone_number Phone number with country code (e.g., 919876543210)
 * @param string $pdf_path Full path to PDF file
 * @param string $application_id Application ID for message
 * @param string $full_name Applicant's full name
 * @return array ['success' => bool, 'message' => string]
 */
function sendPDFViaWhatsApp($phone_number, $pdf_path, $application_id, $full_name) {
    // Get WhatsApp configuration from config
    $whatsapp_provider = defined('WHATSAPP_PROVIDER') ? WHATSAPP_PROVIDER : 'twilio';
    $whatsapp_api_key = defined('WHATSAPP_API_KEY') ? WHATSAPP_API_KEY : '';
    $whatsapp_api_secret = defined('WHATSAPP_API_SECRET') ? WHATSAPP_API_SECRET : '';
    $whatsapp_from_number = defined('WHATSAPP_FROM_NUMBER') ? WHATSAPP_FROM_NUMBER : '';
    $whatsapp_api_url = defined('WHATSAPP_API_URL') ? WHATSAPP_API_URL : '';
    
    // Normalize phone number (remove +, spaces, dashes)
    $phone_number = preg_replace('/[^0-9]/', '', $phone_number);
    
    // Ensure phone number has country code (default to India +91 if not present)
    if (strlen($phone_number) == 10) {
        $phone_number = '91' . $phone_number; // Add India country code
    }
    
    // Check if PDF file exists
    $full_pdf_path = __DIR__ . DIRECTORY_SEPARATOR . $pdf_path;
    if (!file_exists($full_pdf_path)) {
        error_log('PDF file not found: ' . $full_pdf_path);
        return [
            'success' => false,
            'message' => 'PDF file not found'
        ];
    }
    
    // Prepare message
    $message = "Dear " . $full_name . ",\n\n";
    $message .= "Your exam application has been submitted successfully!\n\n";
    $message .= "Application ID: " . $application_id . "\n";
    $message .= "Please find your application PDF attached.\n\n";
    $message .= "Thank you for applying!\n";
    $message .= "Aakasha Bindu Agritech";
    
    // Send based on provider
    switch (strtolower($whatsapp_provider)) {
        case 'twilio':
            return sendViaTwilio($phone_number, $full_pdf_path, $message, $whatsapp_api_key, $whatsapp_api_secret, $whatsapp_from_number);
        
        case 'chatapi':
            return sendViaChatAPI($phone_number, $full_pdf_path, $message, $whatsapp_api_url, $whatsapp_api_key);
        
        case 'wati':
            return sendViaWati($phone_number, $full_pdf_path, $message, $whatsapp_api_url, $whatsapp_api_key);
        
        case 'gupshup':
            return sendViaGupshup($phone_number, $full_pdf_path, $message, $whatsapp_api_key, $whatsapp_api_secret);
        
        default:
            // Try generic HTTP API
            return sendViaGenericAPI($phone_number, $full_pdf_path, $message, $whatsapp_api_url, $whatsapp_api_key);
    }
}

/**
 * Send via Twilio WhatsApp API
 */
function sendViaTwilio($phone_number, $pdf_path, $message, $api_key, $api_secret, $from_number) {
    if (empty($api_key) || empty($api_secret) || empty($from_number)) {
        error_log('Twilio WhatsApp: Missing configuration');
        return ['success' => false, 'message' => 'WhatsApp API not configured'];
    }
    
    $url = "https://api.twilio.com/2010-04-01/Accounts/{$api_key}/Messages.json";
    
    // Format phone number for Twilio (whatsapp:+919876543210)
    $to = 'whatsapp:+' . $phone_number;
    $from = 'whatsapp:+' . $from_number;
    
    // Read PDF file
    $pdf_content = file_get_contents($pdf_path);
    $pdf_base64 = base64_encode($pdf_content);
    
    $post_data = [
        'From' => $from,
        'To' => $to,
        'Body' => $message
    ];
    
    // For media, use Twilio's Media URL or upload to a public URL first
    // Note: Twilio requires media to be hosted on a publicly accessible URL
    // You'll need to upload the PDF to a public location first
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $api_key . ':' . $api_secret);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 201) {
        error_log('WhatsApp message sent successfully via Twilio to: ' . $phone_number);
        return ['success' => true, 'message' => 'WhatsApp message sent successfully'];
    } else {
        error_log('Twilio WhatsApp error: ' . $response);
        return ['success' => false, 'message' => 'Failed to send WhatsApp message'];
    }
}

/**
 * Send via ChatAPI
 */
function sendViaChatAPI($phone_number, $pdf_path, $message, $api_url, $api_key) {
    if (empty($api_url) || empty($api_key)) {
        error_log('ChatAPI: Missing configuration');
        return ['success' => false, 'message' => 'WhatsApp API not configured'];
    }
    
    $url = rtrim($api_url, '/') . '/sendFile';
    
    // Read PDF file
    $pdf_content = file_get_contents($pdf_path);
    $pdf_base64 = base64_encode($pdf_content);
    $pdf_filename = basename($pdf_path);
    
    $post_data = [
        'phone' => $phone_number,
        'body' => $message,
        'filename' => $pdf_filename,
        'document' => 'data:application/pdf;base64,' . $pdf_base64
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $result = json_decode($response, true);
        if (isset($result['sent'])) {
            error_log('WhatsApp message sent successfully via ChatAPI to: ' . $phone_number);
            return ['success' => true, 'message' => 'WhatsApp message sent successfully'];
        }
    }
    
    error_log('ChatAPI WhatsApp error: ' . $response);
    return ['success' => false, 'message' => 'Failed to send WhatsApp message'];
}

/**
 * Send via Wati.io
 */
function sendViaWati($phone_number, $pdf_path, $message, $api_url, $api_key) {
    if (empty($api_url) || empty($api_key)) {
        error_log('Wati: Missing configuration');
        return ['success' => false, 'message' => 'WhatsApp API not configured'];
    }
    
    $url = rtrim($api_url, '/') . '/api/v1/sendSessionFile/' . $phone_number;
    
    // Read PDF file
    $pdf_content = file_get_contents($pdf_path);
    $pdf_base64 = base64_encode($pdf_content);
    $pdf_filename = basename($pdf_path);
    
    $post_data = [
        'caption' => $message,
        'filename' => $pdf_filename
    ];
    
    // For Wati, you may need to upload file separately or use multipart form data
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . $api_key
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        error_log('WhatsApp message sent successfully via Wati to: ' . $phone_number);
        return ['success' => true, 'message' => 'WhatsApp message sent successfully'];
    }
    
    error_log('Wati WhatsApp error: ' . $response);
    return ['success' => false, 'message' => 'Failed to send WhatsApp message'];
}

/**
 * Send via Gupshup
 */
function sendViaGupshup($phone_number, $pdf_path, $message, $api_key, $api_secret) {
    if (empty($api_key) || empty($api_secret)) {
        error_log('Gupshup: Missing configuration');
        return ['success' => false, 'message' => 'WhatsApp API not configured'];
    }
    
    // Gupshup API endpoint
    $url = 'https://api.gupshup.io/sm/api/v1/msg';
    
    // Read PDF file and convert to base64
    $pdf_content = file_get_contents($pdf_path);
    $pdf_base64 = base64_encode($pdf_content);
    $pdf_filename = basename($pdf_path);
    
    $post_data = [
        'channel' => 'whatsapp',
        'source' => '917834811114', // Your Gupshup WhatsApp number
        'destination' => $phone_number,
        'message' => json_encode([
            'type' => 'file',
            'url' => 'data:application/pdf;base64,' . $pdf_base64,
            'filename' => $pdf_filename,
            'caption' => $message
        ]),
        'src.name' => 'AakashaBinduAgritech'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $api_key,
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        error_log('WhatsApp message sent successfully via Gupshup to: ' . $phone_number);
        return ['success' => true, 'message' => 'WhatsApp message sent successfully'];
    }
    
    error_log('Gupshup WhatsApp error: ' . $response);
    return ['success' => false, 'message' => 'Failed to send WhatsApp message'];
}

/**
 * Generic HTTP API handler
 */
function sendViaGenericAPI($phone_number, $pdf_path, $message, $api_url, $api_key) {
    if (empty($api_url)) {
        error_log('Generic WhatsApp API: Missing API URL');
        return ['success' => false, 'message' => 'WhatsApp API not configured'];
    }
    
    // Read PDF file
    $pdf_content = file_get_contents($pdf_path);
    $pdf_base64 = base64_encode($pdf_content);
    $pdf_filename = basename($pdf_path);
    
    $post_data = [
        'phone' => $phone_number,
        'message' => $message,
        'file' => [
            'name' => $pdf_filename,
            'content' => $pdf_base64,
            'type' => 'application/pdf'
        ]
    ];
    
    $headers = ['Content-Type: application/json'];
    if (!empty($api_key)) {
        $headers[] = 'Authorization: Bearer ' . $api_key;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code >= 200 && $http_code < 300) {
        error_log('WhatsApp message sent successfully via Generic API to: ' . $phone_number);
        return ['success' => true, 'message' => 'WhatsApp message sent successfully'];
    }
    
    error_log('Generic WhatsApp API error: ' . $response);
    return ['success' => false, 'message' => 'Failed to send WhatsApp message'];
}

