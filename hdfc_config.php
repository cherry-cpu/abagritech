<?php
// HDFC Payment Gateway Configuration
// Replace these values with your actual credentials provided by HDFC

define('HDFC_MERCHANT_ID', 'YOUR_MERCHANT_ID');
define('HDFC_ACCESS_CODE', 'YOUR_ACCESS_CODE');
define('HDFC_WORKING_KEY', 'YOUR_WORKING_KEY');

// URLs
// Update these with your live domain URLs
define('HDFC_REDIRECT_URL', 'http://localhost/httpsabagritech/hdfc_response.php');
define('HDFC_CANCEL_URL', 'http://localhost/httpsabagritech/hdfc_response.php');

// Transaction URL (Test or Production)
// Test URL: https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction
// Production URL: https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction
// Note: HDFC often uses CCAvenue as the backend provider. Verify the correct URL from your integration kit.
define('HDFC_TRANSACTION_URL', 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction');

?>
