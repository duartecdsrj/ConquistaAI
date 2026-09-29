ALTER TABLE question_pdf_import_jobs
  ADD COLUMN analysis_provider VARCHAR(64) NULL AFTER algorithm_version,
  ADD COLUMN analysis_model VARCHAR(128) NULL AFTER analysis_provider,
  ADD COLUMN analysis_schema_version VARCHAR(64) NULL AFTER analysis_model,
  ADD COLUMN analysis_duration_milliseconds INT UNSIGNED NULL AFTER analysis_schema_version,
  ADD COLUMN analysis_input_tokens INT UNSIGNED NULL AFTER analysis_duration_milliseconds,
  ADD COLUMN analysis_output_tokens INT UNSIGNED NULL AFTER analysis_input_tokens,
  ADD COLUMN analysis_total_tokens INT UNSIGNED NULL AFTER analysis_output_tokens,
  ADD COLUMN analysis_cost_usd DECIMAL(12,6) NULL AFTER analysis_total_tokens,
  ADD COLUMN analysis_usage_availability VARCHAR(16) NOT NULL DEFAULT 'UNAVAILABLE' AFTER analysis_cost_usd,
  ADD COLUMN analysis_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER analysis_usage_availability,
  ADD COLUMN answer_key_conflict_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER analysis_count,
  ADD COLUMN image_discrepancy_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER answer_key_conflict_count,
  ADD COLUMN structural_issue_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER image_discrepancy_count;

CREATE TABLE question_import_analyses (
  id CHAR(36) PRIMARY KEY,
  job_id CHAR(36) NOT NULL,
  question_id CHAR(36) NULL,
  candidate_fingerprint CHAR(64) NOT NULL,
  schema_version VARCHAR(64) NOT NULL,
  algorithm_version VARCHAR(64) NOT NULL,
  provider VARCHAR(64) NOT NULL,
  model VARCHAR(128) NULL,
  evidence_pages JSON NOT NULL,
  metadata JSON NOT NULL,
  structure_type VARCHAR(32) NOT NULL,
  answer_key_assessment VARCHAR(32) NOT NULL,
  image_assessment VARCHAR(32) NOT NULL,
  input_tokens INT UNSIGNED NULL,
  output_tokens INT UNSIGNED NULL,
  total_tokens INT UNSIGNED NULL,
  cost_usd DECIMAL(12,6) NULL,
  usage_availability VARCHAR(16) NOT NULL DEFAULT 'UNAVAILABLE',
  duration_milliseconds INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_question_import_analysis_job FOREIGN KEY (job_id) REFERENCES question_pdf_import_jobs(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_import_analysis_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE SET NULL,
  INDEX idx_question_import_analysis_job (job_id, created_at),
  INDEX idx_question_import_analysis_question (question_id, created_at),
  INDEX idx_question_import_analysis_fingerprint (job_id, candidate_fingerprint)
);

CREATE TABLE question_import_analysis_findings (
  id CHAR(36) PRIMARY KEY,
  analysis_id CHAR(36) NOT NULL,
  code VARCHAR(64) NOT NULL,
  severity VARCHAR(16) NOT NULL,
  confidence DECIMAL(5,4) NULL,
  safe_summary VARCHAR(500) NOT NULL,
  evidence_pages JSON NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_question_import_finding_analysis FOREIGN KEY (analysis_id) REFERENCES question_import_analyses(id) ON DELETE CASCADE,
  INDEX idx_question_import_finding_analysis (analysis_id, code),
  INDEX idx_question_import_finding_code (code)
);

CREATE TABLE question_import_image_anchors (
  id CHAR(36) PRIMARY KEY,
  analysis_id CHAR(36) NOT NULL,
  target_kind VARCHAR(16) NOT NULL,
  option_position TINYINT UNSIGNED NULL,
  source_page INT UNSIGNED NOT NULL,
  source_asset_index INT UNSIGNED NULL,
  status VARCHAR(32) NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_question_import_anchor_analysis FOREIGN KEY (analysis_id) REFERENCES question_import_analyses(id) ON DELETE CASCADE,
  INDEX idx_question_import_anchor_analysis (analysis_id, status)
);

CREATE TABLE question_quality_signals (
  question_id CHAR(36) PRIMARY KEY,
  analysis_id CHAR(36) NOT NULL,
  category VARCHAR(64) NOT NULL,
  safe_message VARCHAR(500) NOT NULL,
  detected_at DATETIME NOT NULL,
  CONSTRAINT fk_question_quality_signal_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_quality_signal_analysis FOREIGN KEY (analysis_id) REFERENCES question_import_analyses(id) ON DELETE CASCADE,
  INDEX idx_question_quality_signal_category (category)
);
