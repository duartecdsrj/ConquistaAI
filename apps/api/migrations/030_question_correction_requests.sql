CREATE TABLE question_correction_requests (
    id VARCHAR(36) NOT NULL PRIMARY KEY,
    question_id VARCHAR(36) NOT NULL,
    requested_by VARCHAR(36) NOT NULL,
    instruction TEXT NOT NULL,
    status VARCHAR(16) NOT NULL,
    original_snapshot JSON NOT NULL,
    proposal JSON NULL,
    error_message VARCHAR(500) NULL,
    created_at DATETIME NOT NULL,
    started_at DATETIME NULL,
    finished_at DATETIME NULL,
    approved_by VARCHAR(36) NULL,
    approved_at DATETIME NULL,
    INDEX idx_question_correction_pending (status, created_at),
    INDEX idx_question_correction_visibility (question_id, requested_by, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
