<?php
// define('CCAVENUE_MERCHANT_ID', '4432458');
// define('CCAVENUE_ACCESS_CODE', 'AVSS89NC81BF81SSFB');
// define('CCAVENUE_WORKING_KEY', 'AC9299915B519A84789663C427539A66');
define('CCAVENUE_MERCHANT_ID', '4432458');
define('CCAVENUE_ACCESS_CODE', 'ATSS89NC81BF81SSFB');
define('CCAVENUE_WORKING_KEY', 'AC9299915B519A84789663C427539A66');
// URLs
// Update these with your live domain URLs
define('CCAVENUE_REDIRECT_URL', 'https://abagritech.com/ccavenue_response.php');
define('CCAVENUE_CANCEL_URL', 'https://abagritech.com/ccavenue_response.php');

// Transaction URL (Test or Production)
// Test URL: https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction
// Production URL: https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction
// Note: HDFC often uses CCAvenue as the backend provider. Verify the correct URL from your integration kit.
define('CCAVENUE_TRANSACTION_URL', 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction');
define('CCAVENUE_TRANSACTION_URL_1', 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction');

?>
