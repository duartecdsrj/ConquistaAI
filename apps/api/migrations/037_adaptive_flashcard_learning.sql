CREATE TABLE flashcards (
  id CHAR(36) PRIMARY KEY,
  primary_taxonomy_subject_id CHAR(36) NOT NULL,
  type ENUM('BASIC') NOT NULL DEFAULT 'BASIC',
  front TEXT NOT NULL,
  back TEXT NOT NULL,
  fingerprint_version SMALLINT UNSIGNED NOT NULL,
  fingerprint CHAR(64) NOT NULL,
  source ENUM('SYSTEM','NOTEBOOK_ANALYSIS','EDITORIAL') NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_flashcard_primary_subject FOREIGN KEY (primary_taxonomy_subject_id) REFERENCES taxonomy_subjects(id),
  UNIQUE KEY uq_flashcard_fingerprint (primary_taxonomy_subject_id, type, fingerprint_version, fingerprint),
  INDEX idx_flashcard_primary_subject (primary_taxonomy_subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE flashcard_taxonomy_subjects (
  flashcard_id CHAR(36) NOT NULL,
  taxonomy_subject_id CHAR(36) NOT NULL,
  PRIMARY KEY (flashcard_id, taxonomy_subject_id),
  CONSTRAINT fk_flashcard_taxonomy_card FOREIGN KEY (flashcard_id) REFERENCES flashcards(id) ON DELETE CASCADE,
  CONSTRAINT fk_flashcard_taxonomy_subject FOREIGN KEY (taxonomy_subject_id) REFERENCES taxonomy_subjects(id),
  INDEX idx_flashcard_taxonomy_subject (taxonomy_subject_id, flashcard_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE review_sessions (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  kind ENUM('DAILY','QUICK') NOT NULL,
  status ENUM('ACTIVE','COMPLETED','ABANDONED') NOT NULL DEFAULT 'ACTIVE',
  requested_limit SMALLINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL,
  completed_at DATETIME NULL,
  CONSTRAINT fk_review_session_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_review_session_resume (user_id, kind, status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE review_session_cards (
  session_id CHAR(36) NOT NULL,
  flashcard_id CHAR(36) NOT NULL,
  position SMALLINT UNSIGNED NOT NULL,
  reviewed_at DATETIME NULL,
  PRIMARY KEY (session_id, flashcard_id),
  UNIQUE KEY uq_review_session_card_position (session_id, position),
  CONSTRAINT fk_review_session_card_session FOREIGN KEY (session_id) REFERENCES review_sessions(id) ON DELETE CASCADE,
  CONSTRAINT fk_review_session_card_flashcard FOREIGN KEY (flashcard_id) REFERENCES flashcards(id),
  INDEX idx_review_session_card_pending (session_id, reviewed_at, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE user_flashcard_progress (
  user_id CHAR(36) NOT NULL,
  flashcard_id CHAR(36) NOT NULL,
  due_at DATETIME NOT NULL,
  interval_days SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  ease_factor DECIMAL(5,3) NOT NULL DEFAULT 2.500,
  repetitions INT UNSIGNED NOT NULL DEFAULT 0,
  lapses INT UNSIGNED NOT NULL DEFAULT 0,
  last_reviewed_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (user_id, flashcard_id),
  CONSTRAINT fk_flashcard_progress_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_flashcard_progress_card FOREIGN KEY (flashcard_id) REFERENCES flashcards(id) ON DELETE CASCADE,
  INDEX idx_flashcard_progress_queue (user_id, due_at, flashcard_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE flashcard_reviews (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  flashcard_id CHAR(36) NOT NULL,
  session_id CHAR(36) NULL,
  rating ENUM('AGAIN','HARD','GOOD','EASY') NOT NULL,
  previous_state JSON NOT NULL,
  next_state JSON NOT NULL,
  reviewed_at DATETIME NOT NULL,
  CONSTRAINT fk_flashcard_review_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_flashcard_review_card FOREIGN KEY (flashcard_id) REFERENCES flashcards(id),
  CONSTRAINT fk_flashcard_review_session FOREIGN KEY (session_id) REFERENCES review_sessions(id) ON DELETE SET NULL,
  UNIQUE KEY uq_flashcard_review_session_card (session_id, flashcard_id),
  INDEX idx_flashcard_review_history (user_id, flashcard_id, reviewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE user_concept_mastery (
  user_id CHAR(36) NOT NULL,
  taxonomy_subject_id CHAR(36) NOT NULL,
  mastery_score DECIMAL(5,2) NULL,
  confidence ENUM('INSUFFICIENT','LOW','MEDIUM','HIGH') NOT NULL DEFAULT 'INSUFFICIENT',
  evidence_count INT UNSIGNED NOT NULL DEFAULT 0,
  last_evidence_at DATETIME NULL,
  signals JSON NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (user_id, taxonomy_subject_id),
  CONSTRAINT fk_concept_mastery_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_concept_mastery_subject FOREIGN KEY (taxonomy_subject_id) REFERENCES taxonomy_subjects(id),
  INDEX idx_concept_mastery_user_confidence (user_id, confidence, updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE notebook_analysis_executions (
  id CHAR(36) PRIMARY KEY,
  notebook_id CHAR(36) NOT NULL,
  algorithm_version VARCHAR(64) NOT NULL,
  status ENUM('PENDING','PROCESSING','COMPLETED','FAILED') NOT NULL DEFAULT 'PENDING',
  retry_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
  provider VARCHAR(100) NULL,
  model VARCHAR(190) NULL,
  token_count INT UNSIGNED NULL,
  duration_milliseconds INT UNSIGNED NULL,
  summary JSON NULL,
  error_code VARCHAR(64) NULL,
  error_message VARCHAR(500) NULL,
  requested_at DATETIME NOT NULL,
  started_at DATETIME NULL,
  completed_at DATETIME NULL,
  CONSTRAINT fk_notebook_analysis_notebook FOREIGN KEY (notebook_id) REFERENCES notebooks(id) ON DELETE CASCADE,
  UNIQUE KEY uq_notebook_analysis_version (notebook_id, algorithm_version),
  INDEX idx_notebook_analysis_status (status, requested_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE notebook_analysis_actions (
  id CHAR(36) PRIMARY KEY,
  execution_id CHAR(36) NOT NULL,
  question_id CHAR(36) NULL,
  taxonomy_subject_id CHAR(36) NULL,
  flashcard_id CHAR(36) NULL,
  type ENUM('CREATE_OR_REUSE_CARD','ADVANCE_CARD','UPDATE_MASTERY','NO_ACTION') NOT NULL,
  reason VARCHAR(500) NOT NULL,
  confidence DECIMAL(4,3) NOT NULL,
  payload JSON NOT NULL,
  applied_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_notebook_analysis_action_execution FOREIGN KEY (execution_id) REFERENCES notebook_analysis_executions(id) ON DELETE CASCADE,
  CONSTRAINT fk_notebook_analysis_action_question FOREIGN KEY (question_id) REFERENCES questions(id),
  CONSTRAINT fk_notebook_analysis_action_subject FOREIGN KEY (taxonomy_subject_id) REFERENCES taxonomy_subjects(id),
  CONSTRAINT fk_notebook_analysis_action_card FOREIGN KEY (flashcard_id) REFERENCES flashcards(id),
  INDEX idx_notebook_analysis_action_execution (execution_id, created_at),
  INDEX idx_notebook_analysis_action_subject (taxonomy_subject_id, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Rollback (somente após desabilitar Review e preservar/exportar histórico imutável):
-- DROP TABLE notebook_analysis_actions, notebook_analysis_executions, flashcard_reviews,
--   user_concept_mastery, user_flashcard_progress, review_session_cards, review_sessions,
--   flashcard_taxonomy_subjects, flashcards;
