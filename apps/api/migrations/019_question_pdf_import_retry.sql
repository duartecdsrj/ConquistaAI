ALTER TABLE question_pdf_import_jobs
    ADD COLUMN processed_chunks INT NOT NULL DEFAULT 0 AFTER candidate_pages,
    ADD COLUMN retry_count INT NOT NULL DEFAULT 0 AFTER processed_chunks,
    ADD COLUMN next_attempt_at DATETIME NULL AFTER retry_count;
