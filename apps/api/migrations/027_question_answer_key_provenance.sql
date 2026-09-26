ALTER TABLE questions ADD COLUMN answer_key_source VARCHAR(16) NULL AFTER correct_option_id;
UPDATE questions SET answer_key_source = 'OFFICIAL' WHERE correct_option_id IS NOT NULL AND answer_key_source IS NULL;
