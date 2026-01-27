CREATE TABLE exam_marks (
    application_id VARCHAR(50) PRIMARY KEY,
    subject1 INT NOT NULL,
    subject2 INT NOT NULL,
    subject3 INT NOT NULL,
    total INT NOT NULL,
    result ENUM('PASS', 'FAIL') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);