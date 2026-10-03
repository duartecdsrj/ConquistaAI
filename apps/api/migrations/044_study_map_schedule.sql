CREATE TABLE study_map_schedule_items (
  user_id CHAR(36) NOT NULL,
  exam_id CHAR(36) NOT NULL,
  taxonomy_subject_id CHAR(36) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  status ENUM('PLANNED','STUDIED','COMPLETED') NOT NULL DEFAULT 'PLANNED',
  completed_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (user_id, exam_id, taxonomy_subject_id),
  CONSTRAINT fk_study_map_schedule_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_study_map_schedule_exam FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
  CONSTRAINT fk_study_map_schedule_subject FOREIGN KEY (taxonomy_subject_id) REFERENCES taxonomy_subjects(id) ON DELETE CASCADE,
  INDEX idx_study_map_schedule_timeline (user_id, exam_id, start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE study_map_schedule_dependencies (
  user_id CHAR(36) NOT NULL,
  exam_id CHAR(36) NOT NULL,
  taxonomy_subject_id CHAR(36) NOT NULL,
  predecessor_subject_id CHAR(36) NOT NULL,
  PRIMARY KEY (user_id, exam_id, taxonomy_subject_id, predecessor_subject_id),
  CONSTRAINT fk_study_map_dependency_item FOREIGN KEY (user_id, exam_id, taxonomy_subject_id)
    REFERENCES study_map_schedule_items(user_id, exam_id, taxonomy_subject_id) ON DELETE CASCADE,
  CONSTRAINT fk_study_map_dependency_predecessor FOREIGN KEY (user_id, exam_id, predecessor_subject_id)
    REFERENCES study_map_schedule_items(user_id, exam_id, taxonomy_subject_id) ON DELETE CASCADE,
  INDEX idx_study_map_dependency_predecessor (user_id, exam_id, predecessor_subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
