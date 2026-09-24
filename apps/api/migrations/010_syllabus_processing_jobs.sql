CREATE TABLE syllabus_processing_jobs (
  id CHAR(36) PRIMARY KEY,
  syllabus_id CHAR(36) NOT NULL,
  document_sha256 CHAR(64) NOT NULL,
  status ENUM('PENDING','PROCESSING','COMPLETED','FAILED') NOT NULL DEFAULT 'PENDING',
  progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
  error_message VARCHAR(500) NULL,
  created_at DATETIME NOT NULL,
  started_at DATETIME NULL,
  finished_at DATETIME NULL,
  CONSTRAINT fk_syllabus_processing_job_syllabus FOREIGN KEY (syllabus_id) REFERENCES syllabi(id),
  INDEX idx_syllabus_processing_job_next (status, created_at),
  INDEX idx_syllabus_processing_job_syllabus (syllabus_id, created_at)
);
