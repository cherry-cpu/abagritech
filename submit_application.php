<?php
// CORS Headers - Allow cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include configuration file
require_once 'config.php';
//require_once 'generate_pdf.php';
//require_once 'send_whatsapp.php';

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed. Please contact administrator.'
    ]);
    error_log('Database connection error: ' . $e->getMessage());
    exit;
}
error_log('aa==='.$_SERVER["REQUEST_METHOD"]);
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $full_name = $_POST['full_name'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $father_name = $_POST['father_name'] ?? '';
    $aadhar = $_POST['aadhar'] ?? '';
    $caste = $_POST['caste'] ?? '';
    $address = $_POST['address'] ?? '';
    
    // Educational qualifications
    $ssc_year = $_POST['ssc_year'] ?? null;
    $ssc_percentage = $_POST['ssc_percentage'] ?? null;
    $inter_year = $_POST['inter_year'] ?? null;
    $inter_percentage = $_POST['inter_percentage'] ?? null;
    $degree_year = $_POST['degree_year'] ?? null;
    $degree_percentage = $_POST['degree_percentage'] ?? null;
    
    // Application details
    $position = $_POST['position'] ?? '';
    $exam_center = $_POST['exam_center'] ?? '';
    $transaction_id = $_POST['transaction_id'] ?? '';
    
    // Handle file uploads
    $photo_path = '';
    $signature_path = '';
    $upload_errors = [];
    
    // Create upload directory if it doesn't exist
    // Normalize UPLOAD_DIR path (replace forward slashes with directory separator)
    $normalized_upload_dir = str_replace('/', DIRECTORY_SEPARATOR, UPLOAD_DIR);
    $upload_dir = __DIR__ . DIRECTORY_SEPARATOR . $normalized_upload_dir;
    // Ensure trailing directory separator
    if (substr($upload_dir, -1) !== DIRECTORY_SEPARATOR) {
        $upload_dir .= DIRECTORY_SEPARATOR;
    }
    
    error_log('Upload directory path: ' . $upload_dir);
    
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $upload_errors[] = 'Failed to create upload directory';
            error_log('Failed to create upload directory: ' . $upload_dir);
        } else {
            error_log('Successfully created upload directory: ' . $upload_dir);
        }
    }
    
    if (!is_writable($upload_dir)) {
        $upload_errors[] = 'Upload directory is not writable';
        error_log('Upload directory is not writable: ' . $upload_dir);
        error_log('Directory permissions: ' . substr(sprintf('%o', fileperms($upload_dir)), -4));
    } else {
        error_log('Upload directory is writable: ' . $upload_dir);
    }
    // Upload photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo_name = time() . '_photo_' . basename($_FILES['photo']['name']);
        $photo_target = $upload_dir .'photos/'. $photo_name; // filesystem path
        $photo_db_path = UPLOAD_DIR.'photos/' . $photo_name; // path stored in DB
        // Validate file type
        $photo_type = strtolower(pathinfo($photo_target, PATHINFO_EXTENSION));
        if (in_array($photo_type, ALLOWED_IMAGE_TYPES)) {
            // Validate file size
            if ($_FILES['photo']['size'] <= MAX_FILE_SIZE) {
                // Check if temp file exists
                if (!file_exists($_FILES['photo']['tmp_name'])) {
                    $upload_errors[] = 'Photo temp file does not exist';
                    error_log('Photo temp file does not exist: ' . $_FILES['photo']['tmp_name']);
                } else if (!is_uploaded_file($_FILES['photo']['tmp_name'])) {
                    $upload_errors[] = 'Photo file is not a valid uploaded file';
                    error_log('Photo file is not a valid uploaded file: ' . $_FILES['photo']['tmp_name']);
                } else if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_target)) {
                    $photo_path = $photo_db_path;
                    error_log('Photo uploaded successfully to: ' . $photo_target);
                } else {
                    $upload_errors[] = 'Failed to move photo file';
                    error_log('Failed to move photo file. Source: ' . $_FILES['photo']['tmp_name'] . ' Target: ' . $photo_target);
                    error_log('Source file exists: ' . (file_exists($_FILES['photo']['tmp_name']) ? 'yes' : 'no'));
                    error_log('Source file readable: ' . (is_readable($_FILES['photo']['tmp_name']) ? 'yes' : 'no'));
                    error_log('Target directory exists: ' . (file_exists($upload_dir) ? 'yes' : 'no'));
                    error_log('Target directory writable: ' . (is_writable($upload_dir) ? 'yes' : 'no'));
                    error_log('Target file exists: ' . (file_exists($photo_target) ? 'yes' : 'no'));
                }
            } else {
                $upload_errors[] = 'Photo file size exceeds maximum allowed size';
                error_log('Photo file size: ' . $_FILES['photo']['size'] . ' bytes, Max: ' . MAX_FILE_SIZE . ' bytes');
            }
        } else {
            $upload_errors[] = 'Invalid photo file type: ' . $photo_type;
            error_log('Invalid photo file type: ' . $photo_type);
        }
    } else if (isset($_FILES['photo'])) {
        $upload_errors[] = 'Photo upload error code: ' . $_FILES['photo']['error'];
        error_log('Photo upload error code: ' . $_FILES['photo']['error']);
    }
    
    // Upload signature
    if (isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
        $signature_name = time() . '_signature_' . basename($_FILES['signature']['name']);
        $signature_target = $upload_dir ."signature/". $signature_name; // filesystem path
        $signature_db_path = UPLOAD_DIR ."signature/". $signature_name; // path stored in DB
        
        // Validate file type
        $signature_type = strtolower(pathinfo($signature_target, PATHINFO_EXTENSION));
        if (in_array($signature_type, ALLOWED_IMAGE_TYPES)) {
            // Validate file size
            if ($_FILES['signature']['size'] <= MAX_FILE_SIZE) {
                // Check if temp file exists
                if (!file_exists($_FILES['signature']['tmp_name'])) {
                    $upload_errors[] = 'Signature temp file does not exist';
                    error_log('Signature temp file does not exist: ' . $_FILES['signature']['tmp_name']);
                } else if (!is_uploaded_file($_FILES['signature']['tmp_name'])) {
                    $upload_errors[] = 'Signature file is not a valid uploaded file';
                    error_log('Signature file is not a valid uploaded file: ' . $_FILES['signature']['tmp_name']);
                } else if (move_uploaded_file($_FILES['signature']['tmp_name'], $signature_target)) {
                    $signature_path = $signature_db_path;
                    error_log('Signature uploaded successfully to: ' . $signature_target);
                } else {
                    $upload_errors[] = 'Failed to move signature file';
                    error_log('Failed to move signature file. Source: ' . $_FILES['signature']['tmp_name'] . ' Target: ' . $signature_target);
                    error_log('Source file exists: ' . (file_exists($_FILES['signature']['tmp_name']) ? 'yes' : 'no'));
                    error_log('Source file readable: ' . (is_readable($_FILES['signature']['tmp_name']) ? 'yes' : 'no'));
                    error_log('Target directory exists: ' . (file_exists($upload_dir) ? 'yes' : 'no'));
                    error_log('Target directory writable: ' . (is_writable($upload_dir) ? 'yes' : 'no'));
                    error_log('Target file exists: ' . (file_exists($signature_target) ? 'yes' : 'no'));
                }
            } else {
                $upload_errors[] = 'Signature file size exceeds maximum allowed size';
                error_log('Signature file size: ' . $_FILES['signature']['size'] . ' bytes, Max: ' . MAX_FILE_SIZE . ' bytes');
            }
        } else {
            $upload_errors[] = 'Invalid signature file type: ' . $signature_type;
            error_log('Invalid signature file type: ' . $signature_type);
        }
    } else if (isset($_FILES['signature'])) {
        $upload_errors[] = 'Signature upload error code: ' . $_FILES['signature']['error'];
        error_log('Signature upload error code: ' . $_FILES['signature']['error']);
    }
    
    // Log upload errors if any
    if (!empty($upload_errors)) {
        error_log('Upload errors: ' . implode(', ', $upload_errors));
    }
    
    // Generate application ID
    $application_id = strtoupper(substr($position, 0, 4)) . '-' . date('Ymd') . '-' . rand(1000, 9999);
    
    // Calculate age from date of birth
    $dob = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    
    // Generate PDF from form data and images
    $pdf_path = '';
    if (!empty($photo_path) || !empty($signature_path)) {
        try {
            $application_data = [
                'application_id' => $application_id,
                'full_name' => $full_name,
                'date_of_birth' => $date_of_birth,
                'age' => $age,
                'gender' => $gender,
                'email' => $email,
                'phone' => $phone,
                'father_name' => $father_name,
                'aadhar' => $aadhar,
                'caste' => $caste,
                'address' => $address,
                'ssc_year' => $ssc_year,
                'ssc_percentage' => $ssc_percentage,
                'inter_year' => $inter_year,
                'inter_percentage' => $inter_percentage,
                'degree_year' => $degree_year,
                'degree_percentage' => $degree_percentage,
                'position' => $position,
                'exam_center' => $exam_center,
                'transaction_id' => $transaction_id
            ];
            
           /* $pdf_path =generateApplicationPDF($application_data, $photo_path, $signature_path, $upload_dir);
            if ($pdf_path) {
                error_log('PDF generated successfully: ' . $pdf_path);
                
                // Send PDF via WhatsApp if enabled
                if (defined('WHATSAPP_ENABLED') && WHATSAPP_ENABLED && !empty($phone)) {
                    try {
                        $whatsapp_result = sendPDFViaWhatsApp($phone, $pdf_path, $application_id, $full_name);
                        if ($whatsapp_result['success']) {
                            error_log('WhatsApp message sent successfully to: ' . $phone);
                        } else {
                            error_log('WhatsApp sending failed: ' . $whatsapp_result['message']);
                            // Don't fail the application if WhatsApp fails, just log it
                        }
                    } catch (Exception $e) {
                        error_log('Error sending WhatsApp message: ' . $e->getMessage());
                        // Don't fail the application if WhatsApp fails, just log it
                    }
                }
            } else {
                error_log('PDF generation failed');
            }*/
        } catch (Exception $e) {
            error_log('Error generating PDF: ' . $e->getMessage());
            $upload_errors[] = 'Failed to generate PDF: ' . $e->getMessage();
        }
    }
    
    // Insert into database
    try {
        $sql = "INSERT INTO exam_applications (
            application_id, full_name, date_of_birth, age, gender, email, phone, 
            father_name, aadhar, caste, address,
            ssc_year, ssc_percentage, inter_year, inter_percentage, 
            degree_year, degree_percentage,
            position, exam_center, transaction_id,
            photo_path, signature_path, pdf_path,
            created_at, status
        ) VALUES (
            :application_id, :full_name, :date_of_birth, :age, :gender, :email, :phone,
            :father_name, :aadhar, :caste, :address,
            :ssc_year, :ssc_percentage, :inter_year, :inter_percentage,
            :degree_year, :degree_percentage,
            :position, :exam_center, :transaction_id,
            :photo_path, :signature_path, :pdf_path,
            NOW(), 'pending'
        )";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':application_id', $application_id);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':father_name', $father_name);
        $stmt->bindParam(':aadhar', $aadhar);
        $stmt->bindParam(':caste', $caste);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':ssc_year', $ssc_year);
        $stmt->bindParam(':ssc_percentage', $ssc_percentage);
        $stmt->bindParam(':inter_year', $inter_year);
        $stmt->bindParam(':inter_percentage', $inter_percentage);
        $stmt->bindParam(':degree_year', $degree_year);
        $stmt->bindParam(':degree_percentage', $degree_percentage);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':exam_center', $exam_center);
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->bindParam(':photo_path', $photo_path);
        $stmt->bindParam(':signature_path', $signature_path);
        $stmt->bindParam(':pdf_path', $pdf_path);
        
        $stmt->execute();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'application_id' => $application_id,
            'message' => 'Application submitted successfully!'
        ]);
        
    } catch(PDOException $e) {
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error saving application. Please try again or contact support.'
        ]);
        // Log error (don't expose database errors to users)
        error_log('Database error: ' . $e->getMessage());
    }
    
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>

