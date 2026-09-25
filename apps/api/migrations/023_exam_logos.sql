ALTER TABLE exams ADD COLUMN institution_logo_url VARCHAR(1024) NULL AFTER year;
ALTER TABLE exams ADD COLUMN organizer_logo_url VARCHAR(1024) NULL AFTER institution_logo_url;
