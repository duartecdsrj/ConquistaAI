ALTER TABLE user_flashcard_progress
  ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 1 AFTER flashcard_id,
  ADD INDEX idx_flashcard_progress_active_queue (user_id, active, due_at, flashcard_id);
