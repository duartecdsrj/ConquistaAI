CREATE TABLE study_goals (
  user_id CHAR(36) NOT NULL PRIMARY KEY,
  weekly_question_goal SMALLINT UNSIGNED NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_study_goals_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
