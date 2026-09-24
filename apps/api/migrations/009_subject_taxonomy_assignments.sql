CREATE TABLE subject_taxonomy_assignments (
  subject_id CHAR(36) NOT NULL,
  taxonomy_subject_id CHAR(36) NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (subject_id, taxonomy_subject_id),
  CONSTRAINT fk_subject_taxonomy_assignment_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  CONSTRAINT fk_subject_taxonomy_assignment_taxonomy_subject FOREIGN KEY (taxonomy_subject_id) REFERENCES taxonomy_subjects(id),
  INDEX idx_subject_taxonomy_assignment_taxonomy (taxonomy_subject_id, subject_id)
);
