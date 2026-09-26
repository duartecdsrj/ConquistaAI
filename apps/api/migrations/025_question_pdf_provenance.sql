ALTER TABLE questions
  ADD COLUMN source_pdf_job_id CHAR(36) NULL AFTER reference_url,
  ADD COLUMN source_pdf_pages JSON NULL AFTER source_pdf_job_id,
  ADD CONSTRAINT fk_question_source_pdf_job FOREIGN KEY (source_pdf_job_id) REFERENCES question_pdf_import_jobs(id) ON DELETE SET NULL;
