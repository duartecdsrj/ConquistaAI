CREATE TABLE roles (
  id CHAR(36) PRIMARY KEY,
  code VARCHAR(32) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL
);
CREATE TABLE users (
  id CHAR(36) PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('ACTIVE','BLOCKED') NOT NULL DEFAULT 'ACTIVE',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
);
CREATE TABLE user_roles (
  user_id CHAR(36) NOT NULL,
  role_id CHAR(36) NOT NULL,
  PRIMARY KEY (user_id, role_id),
  CONSTRAINT fk_user_role_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_user_role_role FOREIGN KEY (role_id) REFERENCES roles(id)
);
CREATE TABLE auth_sessions (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  refresh_token_hash CHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  revoked_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_session_user FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_session_user (user_id),
  INDEX idx_session_expiry (expires_at)
);
CREATE TABLE auth_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id CHAR(36) NULL,
  event VARCHAR(64) NOT NULL,
  ip_hash CHAR(64) NOT NULL,
  occurred_at DATETIME NOT NULL,
  INDEX idx_auth_event_user_time (user_id, occurred_at)
);
CREATE TABLE exams (
  id CHAR(36) PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  organizer VARCHAR(190) NULL,
  year SMALLINT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
);
CREATE TABLE exam_positions (
  id CHAR(36) PRIMARY KEY,
  exam_id CHAR(36) NOT NULL,
  name VARCHAR(190) NOT NULL,
  emphasis VARCHAR(190) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_position_exam FOREIGN KEY (exam_id) REFERENCES exams(id)
);
CREATE TABLE syllabi (
  id CHAR(36) PRIMARY KEY,
  exam_position_id CHAR(36) NOT NULL,
  name VARCHAR(190) NOT NULL,
  published_at DATE NULL,
  source_url VARCHAR(2048) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_syllabus_position FOREIGN KEY (exam_position_id) REFERENCES exam_positions(id)
);
CREATE TABLE subjects (
  id CHAR(36) PRIMARY KEY,
  syllabus_id CHAR(36) NOT NULL,
  parent_id CHAR(36) NULL,
  name VARCHAR(190) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_subject_syllabus FOREIGN KEY (syllabus_id) REFERENCES syllabi(id),
  CONSTRAINT fk_subject_parent FOREIGN KEY (parent_id) REFERENCES subjects(id),
  INDEX idx_subject_syllabus_parent (syllabus_id, parent_id)
);
CREATE TABLE tags (
  id CHAR(36) PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL
);
CREATE TABLE questions (
  id CHAR(36) PRIMARY KEY,
  syllabus_id CHAR(36) NOT NULL,
  statement TEXT NOT NULL,
  difficulty ENUM('EASY','MEDIUM','HARD') NOT NULL,
  board VARCHAR(190) NULL,
  exam_year SMALLINT NULL,
  source VARCHAR(255) NULL,
  reference_url VARCHAR(2048) NULL,
  origin ENUM('EXAM','ORIGINAL','AI') NOT NULL DEFAULT 'EXAM',
  status ENUM('DRAFT','REVIEW','PUBLISHED','VOID') NOT NULL DEFAULT 'DRAFT',
  created_by CHAR(36) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_question_syllabus FOREIGN KEY (syllabus_id) REFERENCES syllabi(id),
  CONSTRAINT fk_question_creator FOREIGN KEY (created_by) REFERENCES users(id),
  INDEX idx_questions_filters (syllabus_id, status, difficulty, board, exam_year)
);
CREATE TABLE question_options (
  id CHAR(36) PRIMARY KEY,
  question_id CHAR(36) NOT NULL,
  label CHAR(1) NOT NULL,
  content TEXT NOT NULL,
  image_path VARCHAR(1024) NULL,
  sort_order TINYINT NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_option_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  UNIQUE KEY uq_question_option_label (question_id, label)
);
CREATE TABLE question_subjects (
  question_id CHAR(36) NOT NULL,
  subject_id CHAR(36) NOT NULL,
  PRIMARY KEY (question_id, subject_id),
  CONSTRAINT fk_question_subject_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_subject_subject FOREIGN KEY (subject_id) REFERENCES subjects(id),
  INDEX idx_question_subject_subject (subject_id, question_id)
);
CREATE TABLE question_tags (
  question_id CHAR(36) NOT NULL,
  tag_id CHAR(36) NOT NULL,
  PRIMARY KEY (question_id, tag_id),
  CONSTRAINT fk_question_tag_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_tag_tag FOREIGN KEY (tag_id) REFERENCES tags(id)
);
CREATE TABLE question_imports (
  id CHAR(36) PRIMARY KEY,
  created_by CHAR(36) NOT NULL,
  format ENUM('JSON','CSV') NOT NULL,
  filename VARCHAR(255) NOT NULL,
  status ENUM('VALIDATED','COMMITTED','FAILED') NOT NULL,
  totals JSON NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_import_user FOREIGN KEY (created_by) REFERENCES users(id)
);
CREATE TABLE question_import_rows (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  import_id CHAR(36) NOT NULL,
  row_number INT NOT NULL,
  payload JSON NOT NULL,
  status ENUM('VALID','INVALID','DUPLICATE') NOT NULL,
  errors JSON NOT NULL,
  question_id CHAR(36) NULL,
  CONSTRAINT fk_import_row_import FOREIGN KEY (import_id) REFERENCES question_imports(id) ON DELETE CASCADE,
  INDEX idx_import_row_import (import_id, status)
);
CREATE TABLE notebooks (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  name VARCHAR(190) NOT NULL,
  type ENUM('PRACTICE','SIMULATION') NOT NULL DEFAULT 'PRACTICE',
  mode ENUM('STUDY','EXAM') NOT NULL DEFAULT 'STUDY',
  status ENUM('DRAFT','IN_PROGRESS','FINISHED') NOT NULL DEFAULT 'DRAFT',
  filters JSON NOT NULL,
  duration_seconds INT NULL,
  started_at DATETIME NULL,
  finished_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_notebook_user FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_notebook_user (user_id, status)
);
CREATE TABLE notebook_questions (
  notebook_id CHAR(36) NOT NULL,
  question_id CHAR(36) NOT NULL,
  position INT NOT NULL,
  PRIMARY KEY (notebook_id, question_id),
  UNIQUE KEY uq_notebook_position (notebook_id, position),
  CONSTRAINT fk_notebook_question_notebook FOREIGN KEY (notebook_id) REFERENCES notebooks(id) ON DELETE CASCADE,
  CONSTRAINT fk_notebook_question_question FOREIGN KEY (question_id) REFERENCES questions(id)
);
CREATE TABLE attempts (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  notebook_id CHAR(36) NOT NULL,
  question_id CHAR(36) NOT NULL,
  number INT NOT NULL,
  started_at DATETIME NOT NULL,
  completed_at DATETIME NULL,
  context ENUM('STUDY','EXAM','REVIEW') NOT NULL,
  final_answer_id CHAR(36) NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_attempt_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_attempt_notebook FOREIGN KEY (notebook_id) REFERENCES notebooks(id),
  CONSTRAINT fk_attempt_question FOREIGN KEY (question_id) REFERENCES questions(id),
  INDEX idx_attempt_user_completed (user_id, completed_at),
  INDEX idx_attempt_question (question_id)
);
CREATE TABLE answers (
  id CHAR(36) PRIMARY KEY,
  attempt_id CHAR(36) NOT NULL,
  option_id CHAR(36) NULL,
  sequence INT NOT NULL,
  submitted_at DATETIME NOT NULL,
  elapsed_seconds INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_answer_attempt FOREIGN KEY (attempt_id) REFERENCES attempts(id),
  CONSTRAINT fk_answer_option FOREIGN KEY (option_id) REFERENCES question_options(id),
  UNIQUE KEY uq_answer_sequence (attempt_id, sequence),
  INDEX idx_answer_attempt (attempt_id, sequence)
);
CREATE TABLE reviews (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  question_id CHAR(36) NOT NULL,
  status ENUM('PENDING','COMPLETED') NOT NULL DEFAULT 'PENDING',
  next_review_at DATETIME NOT NULL,
  review_count INT NOT NULL DEFAULT 0,
  last_review_at DATETIME NULL,
  manual_marked BOOLEAN NOT NULL DEFAULT FALSE,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_review_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_review_question FOREIGN KEY (question_id) REFERENCES questions(id),
  UNIQUE KEY uq_review_user_question (user_id, question_id),
  INDEX idx_review_due (user_id, status, next_review_at)
);
INSERT IGNORE INTO roles (id, code, created_at) VALUES
  ('00000000-0000-0000-0000-000000000001', 'ADMIN', UTC_TIMESTAMP()),
  ('00000000-0000-0000-0000-000000000002', 'USER', UTC_TIMESTAMP());
