SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_DATE',''));

CREATE TABLE user_voice (id INT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, voice VARCHAR(255) NOT NULL, used INT DEFAULT 0 NOT NULL, created_at DATETIME(6) DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME(6) DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX user_voice_user_id_voice_uindex (user_id, voice), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE learn_notification_personal (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, alarm DATETIME(6) NOT NULL COMMENT '(DC2Type:datetime)', message TEXT NOT NULL, timezone VARCHAR(255) NOT NULL, created_at DATETIME(6) DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME(6) DEFAULT CURRENT_TIMESTAMP, INDEX learn_notification_personal_user_id_index (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB;
DROP INDEX `repeat` ON training;
     DROP INDEX training_word_type_collection_id_uindex ON training;
ALTER TABLE training DROP word, DROP translate, DROP voice, CHANGE `repeat` next DATETIME(6) DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE training ADD CONSTRAINT FK_D5128A8FE357438D FOREIGN KEY (word_id) REFERENCES word (id);
CREATE INDEX training_next_index ON training (next);
     CREATE UNIQUE INDEX training_word_type_collection_id_uindex ON training (word_id, type, collection_id, user_id);
ALTER TABLE poll CHANGE type type CHAR(255) DEFAULT NULL COMMENT 'Poll type, currently can be “regular” or “quiz”';
