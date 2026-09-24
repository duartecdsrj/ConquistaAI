ALTER TABLE syllabi
  ADD COLUMN document_path VARCHAR(1024) NULL AFTER source_url,
  ADD COLUMN document_sha256 CHAR(64) NULL AFTER document_path,
  ADD COLUMN document_original_name VARCHAR(255) NULL AFTER document_sha256,
  ADD COLUMN document_mime_type VARCHAR(100) NULL AFTER document_original_name,
  ADD COLUMN document_size BIGINT NULL AFTER document_mime_type,
  ADD KEY idx_syllabus_document_sha256 (document_sha256);
