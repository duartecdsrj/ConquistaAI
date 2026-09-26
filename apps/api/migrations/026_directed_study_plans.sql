CREATE TABLE directed_study_plans (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  exam_id CHAR(36) NOT NULL,
  position_id CHAR(36) NOT NULL,
  name VARCHAR(190) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY uq_directed_study_plan_scope (user_id, exam_id, position_id),
  CONSTRAINT fk_directed_study_plan_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_directed_study_plan_exam FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
  CONSTRAINT fk_directed_study_plan_position FOREIGN KEY (position_id) REFERENCES exam_positions(id) ON DELETE CASCADE
);
