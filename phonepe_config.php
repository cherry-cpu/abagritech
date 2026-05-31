<?php
// PhonePe Payment Gateway – Standard Checkout (live credentials from PhonePe Business Dashboard)

define('PHONEPE_CLIENT_ID', 'SU2605111719535628960899');
define('PHONEPE_CLIENT_VERSION', '1');
define('PHONEPE_CLIENT_SECRET', '9829f1b9-1c5b-499d-ac66-c63fcd7d7ab7');

// Backward-compatible alias used in older files
define('PHONEPE_CLIENT_CREDENTIALS', PHONEPE_CLIENT_SECRET);

define('PHONEPE_ENV', 'PRODUCTION'); // PRODUCTION or UAT (sandbox)

define('PHONEPE_TRANSACTION_NOTE', 'Exam Application Fee');
define('PHONEPE_REDIRECT_URL', 'https://abagritech.com/phonepe_response.php');

// Expected exam fee in paisa (₹1,200)
define('PHONEPE_EXAM_FEE_PAISA', 120000);
