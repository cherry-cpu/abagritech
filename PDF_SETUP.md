# PDF Generation Setup Guide

This guide will help you set up PDF generation for exam applications.

## Requirements

- PHP 7.4 or higher
- TCPDF library (recommended) or FPDF library

## Installation Steps

### Option 1: TCPDF (Recommended)

1. **Download TCPDF**
   - Visit: https://github.com/tecnickcom/TCPDF
   - Download the latest release ZIP file
   - Or use Git: `git clone https://github.com/tecnickcom/TCPDF.git`

2. **Extract to Project Root**
   - Extract the TCPDF folder to your project root directory
   - The structure should be: `your_project/tcpdf/`
   - Make sure `tcpdf.php` exists at: `your_project/tcpdf/tcpdf.php`

3. **Verify Installation**
   - Check that the folder structure is correct:
     ```
     your_project/
     ├── tcpdf/
     │   ├── tcpdf.php
     │   ├── fonts/
     │   └── ...
     ├── submit_application.php
     ├── generate_pdf.php
     └── ...
     ```

### Option 2: Using Composer (Alternative)

If you're using Composer:

```bash
composer require tecnickcom/tcpdf
```

Then update `generate_pdf.php` to use the Composer autoloader:

```php
require_once __DIR__ . '/vendor/autoload.php';
use TCPDF;
```

## Database Setup

Run the SQL migration to add the `pdf_path` column:

```bash
mysql -u your_username -p your_database < add_pdf_column.sql
```

Or manually execute:

```sql
ALTER TABLE exam_applications 
ADD COLUMN pdf_path VARCHAR(500) NULL AFTER signature_path;

CREATE INDEX idx_pdf_path ON exam_applications(pdf_path);
```

## How It Works

1. When a user submits an application form with photos and signatures
2. The files are uploaded to the `uploads/` directory
3. A PDF is automatically generated containing:
   - All form data (personal info, education, application details)
   - Embedded photo image
   - Embedded signature image
4. The PDF is saved to the `uploads/` directory
5. The PDF path is stored in the database

## PDF File Naming

PDFs are named using the format: `{APPLICATION_ID}_application.pdf`

Example: `MAFO-20241215-1234_application.pdf`

## Troubleshooting

### PDF Not Generating

1. **Check Error Logs**
   - Check PHP error logs for detailed error messages
   - Look for messages about TCPDF library not found

2. **Verify TCPDF Installation**
   - Ensure `tcpdf/tcpdf.php` exists
   - Check file permissions on the tcpdf folder

3. **Check Upload Directory**
   - Ensure `uploads/` directory exists and is writable
   - Check directory permissions: `chmod 755 uploads` (Linux/Mac)

4. **Verify Image Paths**
   - Ensure photo and signature files are successfully uploaded before PDF generation
   - Check that image paths are correct

### Common Issues

- **"TCPDF library not found"**: Download and extract TCPDF to the `tcpdf/` folder
- **"Permission denied"**: Check write permissions on the uploads directory
- **"Image not found"**: Verify that photo and signature files were uploaded successfully
- **"PDF is empty"**: Check that form data is being passed correctly to the PDF generator

## Testing

After setup, test the PDF generation by:

1. Submitting a test application form
2. Checking the `uploads/` directory for the generated PDF
3. Verifying the PDF contains all form data and images
4. Checking the database to confirm `pdf_path` is stored

## File Structure

```
your_project/
├── tcpdf/                    # TCPDF library (download separately)
│   ├── tcpdf.php
│   └── ...
├── uploads/                   # Upload directory
│   ├── {timestamp}_photo_*.jpg
│   ├── {timestamp}_signature_*.jpg
│   └── {APPLICATION_ID}_application.pdf
├── submit_application.php    # Main submission handler
├── generate_pdf.php          # PDF generation functions
├── config.php                # Configuration
└── add_pdf_column.sql        # Database migration
```

## Notes

- PDFs are generated server-side after file uploads
- The PDF generation process is logged in PHP error logs
- If TCPDF is not available, a text file placeholder is created (check logs)
- PDF files are stored in the same `uploads/` directory as images

