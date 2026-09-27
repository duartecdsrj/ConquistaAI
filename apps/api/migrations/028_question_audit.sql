CREATE TABLE IF NOT EXISTS question_audit_runs (
  id CHAR(36) PRIMARY KEY,
  algorithm_version VARCHAR(64) NOT NULL,
  status ENUM('PENDING','PROCESSING','COMPLETED','FAILED') NOT NULL,
  scope VARCHAR(32) NOT NULL DEFAULT 'PUBLISHED',
  summary JSON NOT NULL,
  error_message VARCHAR(500) NULL,
  started_at DATETIME NULL,
  finished_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  INDEX idx_question_audit_runs_status (status, created_at)
);

CREATE TABLE question_audit_findings (
  id CHAR(36) PRIMARY KEY,
  audit_run_id CHAR(36) NOT NULL,
  question_id CHAR(36) NOT NULL,
  source_pdf_job_id CHAR(36) NULL,
  source_page INT NULL,
  code VARCHAR(64) NOT NULL,
  confidence ENUM('HIGH','MEDIUM','LOW') NOT NULL,
  status ENUM('PROCESSADO_AUTOMATICAMENTE','REQUER_REVISAO','IMAGEM_NAO_LOCALIZADA','POSSIVEL_QUESTAO_MESCLADA','POSSIVEL_DUPLICATA','EXTRACAO_INCOMPLETA') NOT NULL,
  message VARCHAR(500) NOT NULL,
  structure_before JSON NULL,
  structure_after JSON NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_question_audit_finding_run FOREIGN KEY (audit_run_id) REFERENCES question_audit_runs(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_audit_finding_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  INDEX idx_question_audit_findings_run (audit_run_id, status),
  UNIQUE KEY uq_question_audit_finding (audit_run_id, question_id, code)
);
