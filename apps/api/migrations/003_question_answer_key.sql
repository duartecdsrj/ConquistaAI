ALTER TABLE questions
  ADD COLUMN correct_option_id CHAR(36) NULL AFTER status,
  ADD CONSTRAINT fk_question_correct_option
    FOREIGN KEY (correct_option_id) REFERENCES question_options(id);
