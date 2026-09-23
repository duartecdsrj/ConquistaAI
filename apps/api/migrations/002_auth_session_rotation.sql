ALTER TABLE auth_sessions
  ADD COLUMN family_id CHAR(36) NOT NULL AFTER user_id,
  ADD COLUMN device_name VARCHAR(255) NULL AFTER family_id,
  ADD INDEX idx_session_family (family_id);
