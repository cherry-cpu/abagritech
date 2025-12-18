-- Add pdf_path column to exam_applications table
-- Run this SQL script to add PDF path support

ALTER TABLE exam_applications 
ADD COLUMN pdf_path VARCHAR(500) NULL AFTER signature_path;

-- Add index for faster queries
CREATE INDEX idx_pdf_path ON exam_applications(pdf_path);

