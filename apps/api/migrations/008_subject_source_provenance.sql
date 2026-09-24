ALTER TABLE subjects
  ADD COLUMN source_excerpt TEXT NULL AFTER name,
  ADD COLUMN source_page INT NULL AFTER source_excerpt,
  ADD COLUMN source_start_offset INT NULL AFTER source_page,
  ADD COLUMN source_end_offset INT NULL AFTER source_start_offset;
