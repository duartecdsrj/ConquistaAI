CREATE TABLE user_question_interactions (
  user_id CHAR(36) NOT NULL,
  question_id CHAR(36) NOT NULL,
  favorite BOOLEAN NOT NULL DEFAULT FALSE,
  review_later BOOLEAN NOT NULL DEFAULT FALSE,
  not_mastered BOOLEAN NOT NULL DEFAULT FALSE,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (user_id, question_id),
  CONSTRAINT fk_question_interaction_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_interaction_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  INDEX idx_question_interaction_filters (user_id, favorite, review_later, not_mastered, updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE question_notes (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  question_id CHAR(36) NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_question_note_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_note_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  UNIQUE KEY uq_question_note_owner (user_id, question_id),
  INDEX idx_question_note_question_user (question_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE question_comments (
  id CHAR(36) PRIMARY KEY,
  question_id CHAR(36) NOT NULL,
  author_user_id CHAR(36) NOT NULL,
  parent_id CHAR(36) NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_question_comment_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_comment_author FOREIGN KEY (author_user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_comment_parent FOREIGN KEY (parent_id) REFERENCES question_comments(id) ON DELETE SET NULL,
  INDEX idx_question_comment_thread (question_id, parent_id, created_at),
  INDEX idx_question_comment_author (author_user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE question_problem_reports (
  id CHAR(36) PRIMARY KEY,
  question_id CHAR(36) NOT NULL,
  reported_by CHAR(36) NOT NULL,
  category ENUM('CONTENT', 'ANSWER_KEY', 'IMAGE', 'DUPLICATE', 'OTHER') NOT NULL,
  description TEXT NOT NULL,
  status ENUM('OPEN', 'TRIAGED', 'RESOLVED', 'REJECTED') NOT NULL DEFAULT 'OPEN',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_question_problem_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_problem_reporter FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_question_problem_owner (reported_by, status, created_at),
  INDEX idx_question_problem_triage (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE question_problem_report_events (
  id CHAR(36) PRIMARY KEY,
  report_id CHAR(36) NOT NULL,
  actor_user_id CHAR(36) NOT NULL,
  event_type ENUM('CREATED', 'STATUS_CHANGED') NOT NULL,
  previous_status ENUM('OPEN', 'TRIAGED', 'RESOLVED', 'REJECTED') NULL,
  next_status ENUM('OPEN', 'TRIAGED', 'RESOLVED', 'REJECTED') NULL,
  occurred_at DATETIME NOT NULL,
  CONSTRAINT fk_question_problem_event_report FOREIGN KEY (report_id) REFERENCES question_problem_reports(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_problem_event_actor FOREIGN KEY (actor_user_id) REFERENCES users(id),
  INDEX idx_question_problem_event_history (report_id, occurred_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE learning_events (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  question_id CHAR(36) NOT NULL,
  attempt_id CHAR(36) NULL,
  taxonomy_subject_id CHAR(36) NULL,
  type ENUM('ANSWER_COMPLETED', 'NOT_MASTERED_ENABLED', 'NOT_MASTERED_DISABLED') NOT NULL,
  outcome ENUM('CORRECT', 'INCORRECT') NULL,
  elapsed_seconds INT UNSIGNED NULL,
  origin VARCHAR(64) NULL,
  payload JSON NOT NULL,
  occurred_at DATETIME NOT NULL,
  CONSTRAINT fk_learning_event_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_learning_event_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  CONSTRAINT fk_learning_event_attempt FOREIGN KEY (attempt_id) REFERENCES attempts(id) ON DELETE SET NULL,
  CONSTRAINT fk_learning_event_subject FOREIGN KEY (taxonomy_subject_id) REFERENCES taxonomy_subjects(id) ON DELETE SET NULL,
  INDEX idx_learning_event_user_subject_time (user_id, taxonomy_subject_id, occurred_at),
  INDEX idx_learning_event_user_question_time (user_id, question_id, occurred_at),
  INDEX idx_learning_event_attempt (attempt_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE question_explanation_executions (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  question_id CHAR(36) NOT NULL,
  attempt_id CHAR(36) NULL,
  algorithm_version VARCHAR(64) NOT NULL,
  safety_mode ENUM('CONCEPTUAL_ONLY', 'POST_ANSWER') NOT NULL,
  status ENUM('PENDING', 'PROCESSING', 'COMPLETED', 'FAILED') NOT NULL DEFAULT 'PENDING',
  retry_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
  provider VARCHAR(100) NULL,
  model VARCHAR(190) NULL,
  token_count INT UNSIGNED NULL,
  duration_milliseconds INT UNSIGNED NULL,
  result JSON NULL,
  error_code VARCHAR(64) NULL,
  error_message VARCHAR(500) NULL,
  requested_at DATETIME NOT NULL,
  started_at DATETIME NULL,
  completed_at DATETIME NULL,
  CONSTRAINT fk_question_explanation_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_explanation_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_explanation_attempt FOREIGN KEY (attempt_id) REFERENCES attempts(id) ON DELETE SET NULL,
  UNIQUE KEY uq_question_explanation_request (user_id, question_id, attempt_id, algorithm_version),
  INDEX idx_question_explanation_claim (status, requested_at),
  INDEX idx_question_explanation_owner (user_id, question_id, requested_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Rollback: DROP TABLE question_explanation_executions, learning_events, question_problem_report_events,
-- question_problem_reports, question_comments, question_notes, user_question_interactions;
