ALTER TABLE attempts
  MODIFY notebook_id CHAR(36) NULL,
  ADD COLUMN arena_duel_id CHAR(36) NULL AFTER notebook_id,
  MODIFY context ENUM('STUDY','EXAM','REVIEW','ARENA_DUELO') NOT NULL,
  ADD CONSTRAINT fk_attempt_arena_duel FOREIGN KEY (arena_duel_id) REFERENCES arena_duels(id),
  ADD INDEX idx_attempt_arena_duel (arena_duel_id, question_id);

-- Rollback: remove fk_attempt_arena_duel, idx_attempt_arena_duel and arena_duel_id.
-- Restore notebook_id NOT NULL only after resolving Arena attempts.
