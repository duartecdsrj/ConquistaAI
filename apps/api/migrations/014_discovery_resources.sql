CREATE TABLE discovery_resources (
 id CHAR(36) PRIMARY KEY,
 resource_type ENUM('EXAM','ANSWER_KEY') NOT NULL,
 title VARCHAR(255) NOT NULL,
 source_url VARCHAR(700) NOT NULL,
 provider VARCHAR(80) NOT NULL,
 query_text VARCHAR(255) NOT NULL,
 discovered_at DATETIME NOT NULL,
 UNIQUE KEY uq_discovery_resource_source (source_url),
 INDEX idx_discovery_resource_type_date (resource_type, discovered_at)
);
