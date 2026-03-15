<?php
// HDFC Payment Gateway Configuration
// Replace these values with your actual credentials provided by HDFC

define('CCAVENUE_MERCHANT_ID', 'YOUR_MERCHANT_ID');
define('CCAVENUE_ACCESS_CODE', 'YOUR_ACCESS_CODE');
define('CCAVENUE_WORKING_KEY', 'YOUR_WORKING_KEY');

// URLs
// Update these with your live domain URLs
define('CCAVENUE_REDIRECT_URL', 'http://abagritech/ccavenue_response.php');
define('CCAVENUE_CANCEL_URL', 'http://httpsabagritech/ccavenue_response.php');

// Transaction URL (Test or Production)
// Test URL: https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction
// Production URL: https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction
// Note: HDFC often uses CCAvenue as the backend provider. Verify the correct URL from your integration kit.
define('CCAVENUE_TRANSACTION_URL', 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction');

?>
