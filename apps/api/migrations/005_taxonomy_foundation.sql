CREATE TABLE taxonomy_subjects (
  id CHAR(36) PRIMARY KEY,
  parent_id CHAR(36) NULL,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL,
  description TEXT NULL,
  level INT NOT NULL DEFAULT 0,
  active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_taxonomy_subject_parent FOREIGN KEY (parent_id) REFERENCES taxonomy_subjects(id),
  UNIQUE KEY uq_taxonomy_subject_parent_slug (parent_id, slug),
  INDEX idx_taxonomy_subject_parent (parent_id),
  INDEX idx_taxonomy_subject_active (active)
);

CREATE TABLE taxonomy_subject_aliases (
  id CHAR(36) PRIMARY KEY,
  subject_id CHAR(36) NOT NULL,
  alias VARCHAR(190) NOT NULL,
  normalized_alias VARCHAR(190) NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_taxonomy_alias_subject FOREIGN KEY (subject_id) REFERENCES taxonomy_subjects(id) ON DELETE CASCADE,
  UNIQUE KEY uq_taxonomy_alias_normalized (normalized_alias),
  INDEX idx_taxonomy_alias_subject (subject_id)
);

CREATE TABLE taxonomy_subject_merges (
  id CHAR(36) PRIMARY KEY,
  source_subject_id CHAR(36) NOT NULL,
  target_subject_id CHAR(36) NOT NULL,
  merged_by CHAR(36) NOT NULL,
  reason VARCHAR(1000) NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_taxonomy_merge_source FOREIGN KEY (source_subject_id) REFERENCES taxonomy_subjects(id),
  CONSTRAINT fk_taxonomy_merge_target FOREIGN KEY (target_subject_id) REFERENCES taxonomy_subjects(id),
  CONSTRAINT fk_taxonomy_merge_user FOREIGN KEY (merged_by) REFERENCES users(id),
  INDEX idx_taxonomy_merge_source (source_subject_id),
  INDEX idx_taxonomy_merge_target (target_subject_id)
);
