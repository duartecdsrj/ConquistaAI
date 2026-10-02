ALTER TABLE review_sessions ADD COLUMN current_position SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER requested_limit;
