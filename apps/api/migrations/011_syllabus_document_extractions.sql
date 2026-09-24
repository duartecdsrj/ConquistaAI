CREATE TABLE syllabus_document_extractions (
  id CHAR(36) PRIMARY KEY,
  syllabus_id CHAR(36) NOT NULL,
  document_sha256 CHAR(64) NOT NULL,
  page_number INT UNSIGNED NOT NULL,
  text_content LONGTEXT NOT NULL,
  start_offset INT UNSIGNED NOT NULL,
  end_offset INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_syllabus_document_extraction_syllabus FOREIGN KEY (syllabus_id) REFERENCES syllabi(id),
  UNIQUE KEY uq_syllabus_document_extraction_page (document_sha256, page_number),
  INDEX idx_syllabus_document_extraction_syllabus (syllabus_id, document_sha256, page_number)
);
