ALTER TABLE study_map_schedule_items
  ADD COLUMN estimated_minutes INT UNSIGNED NULL AFTER end_date;

CREATE TABLE study_map_settings (
  user_id CHAR(36) NOT NULL,
  exam_id CHAR(36) NOT NULL,
  exam_date DATE NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (user_id, exam_id),
  CONSTRAINT fk_study_map_settings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_study_map_settings_exam FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
