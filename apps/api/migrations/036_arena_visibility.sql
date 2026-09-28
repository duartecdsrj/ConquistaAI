ALTER TABLE arena_duels ADD COLUMN visibility ENUM('PUBLIC','PRIVATE') NOT NULL DEFAULT 'PRIVATE' AFTER code, ADD INDEX idx_arena_duels_visibility_status_created (visibility, status, created_at);
-- Rollback: ALTER TABLE arena_duels DROP INDEX idx_arena_duels_visibility_status_created, DROP COLUMN visibility;
