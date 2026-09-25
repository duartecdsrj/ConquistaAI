CREATE TABLE position_taxonomy_subjects (
    position_id CHAR(36) NOT NULL,
    taxonomy_subject_id CHAR(36) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (position_id, taxonomy_subject_id),
    CONSTRAINT fk_position_taxonomy_position FOREIGN KEY (position_id) REFERENCES exam_positions(id) ON DELETE CASCADE,
    CONSTRAINT fk_position_taxonomy_subject FOREIGN KEY (taxonomy_subject_id) REFERENCES taxonomy_subjects(id) ON DELETE CASCADE
);
