<?php
/**
 * Export Exam Applications Data
 * Supports CSV and Excel formats
 */

require_once 'check_auth.php';
requireLogin(); // Require user to be logged in

require_once 'config.php';

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get export format (csv or excel)
$format = isset($_GET['format']) ? strtolower($_GET['format']) : 'csv';

// Get filter parameters (optional)
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$position_filter = isset($_GET['position']) ? $_GET['position'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build query with filters
$sql = "SELECT 
    application_id,
    full_name,
    date_of_birth,
    age,
    gender,
    email,
    phone,
    father_name,
    aadhar,
    caste,
    address,
    ssc_year,
    ssc_percentage,
    inter_year,
    inter_percentage,
    degree_year,
    degree_percentage,
    position,
    exam_center,
    transaction_id,
    photo_path,
    signature_path,
    pdf_path,
    status,
    created_at,
    updated_at
    FROM exam_applications WHERE 1=1";

$params = [];

// Apply filters
if (!empty($status_filter)) {
    $sql .= " AND status = :status";
    $params[':status'] = $status_filter;
}

if (!empty($position_filter)) {
    $sql .= " AND position = :position";
    $params[':position'] = $position_filter;
}

if (!empty($date_from)) {
    $sql .= " AND DATE(created_at) >= :date_from";
    $params[':date_from'] = $date_from;
}

if (!empty($date_to)) {
    $sql .= " AND DATE(created_at) <= :date_to";
    $params[':date_to'] = $date_to;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define column headers
$headers = [
    'Application ID',
    'Full Name',
    'Date of Birth',
    'Age',
    'Gender',
    'Email',
    'Phone',
    'Father Name',
    'Aadhar Number',
    'Caste',
    'Address',
    'SSC Year',
    'SSC Percentage',
    'Intermediate Year',
    'Intermediate Percentage',
    'Degree Year',
    'Degree Percentage',
    'Position',
    'Exam Center',
    'Transaction ID',
    'Photo Path',
    'Signature Path',
    'PDF Path',
    'Status',
    'Created At',
    'Updated At'
];

// Generate filename with timestamp
$filename = 'exam_applications_' . date('Y-m-d_His') . '.' . $format;

// Export based on format
if ($format === 'csv') {
    exportToCSV($applications, $headers, $filename);
} elseif ($format === 'excel' || $format === 'xlsx') {
    exportToExcel($applications, $headers, $filename);
} else {
    // Default to CSV
    exportToCSV($applications, $headers, $filename);
}

/**
 * Export to CSV format
 */
function exportToCSV($data, $headers, $filename) {
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8 (helps Excel recognize encoding)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Write headers
    fputcsv($output, $headers);
    
    // Write data rows
    foreach ($data as $row) {
        $csv_row = [];
        foreach ($headers as $index => $header) {
            // Map headers to database columns
            $column_map = [
                'Application ID' => 'application_id',
                'Full Name' => 'full_name',
                'Date of Birth' => 'date_of_birth',
                'Age' => 'age',
                'Gender' => 'gender',
                'Email' => 'email',
                'Phone' => 'phone',
                'Father Name' => 'father_name',
                'Aadhar Number' => 'aadhar',
                'Caste' => 'caste',
                'Address' => 'address',
                'SSC Year' => 'ssc_year',
                'SSC Percentage' => 'ssc_percentage',
                'Intermediate Year' => 'inter_year',
                'Intermediate Percentage' => 'inter_percentage',
                'Degree Year' => 'degree_year',
                'Degree Percentage' => 'degree_percentage',
                'Position' => 'position',
                'Exam Center' => 'exam_center',
                'Transaction ID' => 'transaction_id',
                'Photo Path' => 'photo_path',
                'Signature Path' => 'signature_path',
                'PDF Path' => 'pdf_path',
                'Status' => 'status',
                'Created At' => 'created_at',
                'Updated At' => 'updated_at'
            ];
            
            $column = $column_map[$header] ?? '';
            $value = $row[$column] ?? '';
            
            // Format dates
            if (in_array($header, ['Created At', 'Updated At', 'Date of Birth']) && !empty($value)) {
                $value = date('Y-m-d H:i:s', strtotime($value));
            }
            
            $csv_row[] = $value;
        }
        fputcsv($output, $csv_row);
    }
    
    fclose($output);
    exit;
}

/**
 * Export to Excel format (XLSX)
 * Note: Requires PhpSpreadsheet library for proper Excel export
 * Falls back to CSV if library not available
 */
function exportToExcel($data, $headers, $filename) {
    // Check if PhpSpreadsheet is available
    if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        // Use PhpSpreadsheet for proper Excel export
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }
        
        // Set data
        $row = 2;
        foreach ($data as $app) {
            $col = 'A';
            $column_map = [
                'Application ID' => 'application_id',
                'Full Name' => 'full_name',
                'Date of Birth' => 'date_of_birth',
                'Age' => 'age',
                'Gender' => 'gender',
                'Email' => 'email',
                'Phone' => 'phone',
                'Father Name' => 'father_name',
                'Aadhar Number' => 'aadhar',
                'Caste' => 'caste',
                'Address' => 'address',
                'SSC Year' => 'ssc_year',
                'SSC Percentage' => 'ssc_percentage',
                'Intermediate Year' => 'inter_year',
                'Intermediate Percentage' => 'inter_percentage',
                'Degree Year' => 'degree_year',
                'Degree Percentage' => 'degree_percentage',
                'Position' => 'position',
                'Exam Center' => 'exam_center',
                'Transaction ID' => 'transaction_id',
                'Photo Path' => 'photo_path',
                'Signature Path' => 'signature_path',
                'PDF Path' => 'pdf_path',
                'Status' => 'status',
                'Created At' => 'created_at',
                'Updated At' => 'updated_at'
            ];
            
            foreach ($headers as $header) {
                $column = $column_map[$header] ?? '';
                $value = $app[$column] ?? '';
                
                // Format dates
                if (in_array($header, ['Created At', 'Updated At', 'Date of Birth']) && !empty($value)) {
                    $value = date('Y-m-d H:i:s', strtotime($value));
                }
                
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', $col) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Set headers and output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } else {
        // Fallback to CSV if PhpSpreadsheet not available
        error_log('PhpSpreadsheet library not found. Falling back to CSV format.');
        exportToCSV($data, $headers, str_replace('.xlsx', '.csv', $filename));
    }
}

