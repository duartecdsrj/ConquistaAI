ALTER TABLE question_pdf_import_jobs
  ADD COLUMN algorithm_version VARCHAR(64) NOT NULL DEFAULT 'legacy' AFTER document_original_name,
  ADD COLUMN reprocess_of_job_id CHAR(36) NULL AFTER algorithm_version,
  ADD CONSTRAINT fk_question_pdf_reprocess_origin FOREIGN KEY (reprocess_of_job_id) REFERENCES question_pdf_import_jobs(id) ON DELETE SET NULL,
  ADD INDEX idx_question_pdf_reprocess_version (document_sha256, algorithm_version);
