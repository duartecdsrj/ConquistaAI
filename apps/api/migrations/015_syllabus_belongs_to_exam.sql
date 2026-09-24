ALTER TABLE syllabi
  ADD COLUMN exam_id CHAR(36) NULL AFTER id;

UPDATE syllabi AS syllabus
JOIN exam_positions AS position ON position.id = syllabus.exam_position_id
SET syllabus.exam_id = position.exam_id;

ALTER TABLE syllabi
  MODIFY COLUMN exam_id CHAR(36) NOT NULL,
  ADD CONSTRAINT fk_syllabus_exam FOREIGN KEY (exam_id) REFERENCES exams(id),
  ADD KEY idx_syllabus_exam (exam_id),
  DROP FOREIGN KEY fk_syllabus_position,
  DROP COLUMN exam_position_id;
