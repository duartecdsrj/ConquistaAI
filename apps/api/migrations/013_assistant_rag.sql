CREATE TABLE assistant_conversations (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  syllabus_id CHAR(36) NOT NULL,
  title VARCHAR(160) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_assistant_conversation_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_assistant_conversation_syllabus FOREIGN KEY (syllabus_id) REFERENCES syllabi(id),
  INDEX idx_assistant_conversation_user (user_id, updated_at)
);
CREATE TABLE assistant_messages (
  id CHAR(36) PRIMARY KEY,
  conversation_id CHAR(36) NOT NULL,
  role ENUM('USER','ASSISTANT') NOT NULL,
  content TEXT NOT NULL,
  provider VARCHAR(80) NULL,
  model VARCHAR(120) NULL,
  evidence_json JSON NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_assistant_message_conversation FOREIGN KEY (conversation_id) REFERENCES assistant_conversations(id) ON DELETE CASCADE,
  INDEX idx_assistant_message_conversation (conversation_id, created_at)
);
