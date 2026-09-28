-- Up migration: preserves all existing passwords and status values.
ALTER TABLE users
  MODIFY password_hash VARCHAR(255) NULL,
  MODIFY status ENUM('ACTIVE', 'PENDING_APPROVAL', 'BLOCKED') NOT NULL DEFAULT 'ACTIVE',
  ADD COLUMN avatar_storage_key VARCHAR(255) NULL AFTER status,
  ADD COLUMN avatar_mime_type VARCHAR(100) NULL AFTER avatar_storage_key,
  ADD COLUMN avatar_source ENUM('GOOGLE', 'MANUAL') NULL AFTER avatar_mime_type,
  ADD COLUMN avatar_updated_at DATETIME NULL AFTER avatar_source;

CREATE TABLE user_google_identities (
  user_id CHAR(36) NOT NULL,
  google_email VARCHAR(190) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE KEY uq_user_google_identity_email (google_email),
  CONSTRAINT fk_user_google_identity_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE identity_audit_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  actor_user_id CHAR(36) NULL,
  subject_user_id CHAR(36) NOT NULL,
  event VARCHAR(64) NOT NULL,
  previous_values JSON NULL,
  new_values JSON NULL,
  occurred_at DATETIME NOT NULL,
  CONSTRAINT fk_identity_audit_actor FOREIGN KEY (actor_user_id) REFERENCES users(id),
  CONSTRAINT fk_identity_audit_subject FOREIGN KEY (subject_user_id) REFERENCES users(id),
  INDEX idx_identity_audit_subject_time (subject_user_id, occurred_at),
  INDEX idx_identity_audit_actor_time (actor_user_id, occurred_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Rollback procedure (execute manually only after disabling the Google/avatar features):
-- DROP TABLE identity_audit_events;
-- DROP TABLE user_google_identities;
-- ALTER TABLE users
--   DROP COLUMN avatar_updated_at,
--   DROP COLUMN avatar_source,
--   DROP COLUMN avatar_mime_type,
--   DROP COLUMN avatar_storage_key,
--   MODIFY status ENUM('ACTIVE', 'BLOCKED') NOT NULL DEFAULT 'ACTIVE',
--   MODIFY password_hash VARCHAR(255) NOT NULL;
-- Before restoring NOT NULL password_hash, assign local passwords to every Google-only user;
-- before reducing the status enum, administratively resolve every PENDING_APPROVAL user.
