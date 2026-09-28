ALTER TABLE attempts ADD UNIQUE KEY uq_arena_attempt_user_question (arena_duel_id, user_id, question_id);
-- Rollback: ALTER TABLE attempts DROP INDEX uq_arena_attempt_user_question;
