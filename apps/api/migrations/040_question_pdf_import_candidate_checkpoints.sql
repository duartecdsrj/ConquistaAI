CREATE TABLE question_pdf_import_candidate_checkpoints (
  id CHAR(36) PRIMARY KEY,
  job_id CHAR(36) NOT NULL,
  candidate_fingerprint CHAR(64) NOT NULL,
  position_index INT UNSIGNED NOT NULL,
  status ENUM("PENDING","PROCESSING","COMPLETED","FAILED") NOT NULL DEFAULT "PENDING",
  retry_count INT UNSIGNED NOT NULL DEFAULT 0,
  next_attempt_at DATETIME NULL,
  lease_started_at DATETIME NULL,
  error_message VARCHAR(500) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_question_pdf_candidate_checkpoint_job FOREIGN KEY (job_id) REFERENCES question_pdf_import_jobs(id) ON DELETE CASCADE,
  UNIQUE KEY uq_question_pdf_candidate_checkpoint (job_id, candidate_fingerprint),
  INDEX idx_question_pdf_candidate_claim (status, next_attempt_at, lease_started_at),
  INDEX idx_question_pdf_candidate_job (job_id, position_index)
);
