CREATE TABLE question_taxonomy_subjects (
  question_id CHAR(36) NOT NULL,
  taxonomy_subject_id CHAR(36) NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (question_id, taxonomy_subject_id),
  CONSTRAINT fk_question_taxonomy_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  CONSTRAINT fk_question_taxonomy_subject FOREIGN KEY (taxonomy_subject_id) REFERENCES taxonomy_subjects(id),
  INDEX idx_question_taxonomy_subject (taxonomy_subject_id, question_id)
);
