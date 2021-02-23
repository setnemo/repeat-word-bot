alter table training
    add rating int null after collection_id;
alter table word
    add rating int null after collection_id;

CREATE TABLE `training_save` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) NOT NULL,
    `word` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `status` varchar(255) NOT NULL,
    `repeat` timestamp NOT NULL,
    `used` int(1) DEFAULT '0',
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `save_training_user_id_word_uindex` (`user_id`,`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO training_save (user_id, word, `type`, `status`, `repeat`) select user_id, word, `type`, `status`, `repeat` from training where status != 'first';

CREATE TABLE `rating` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `public` int(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;

alter table training drop key training_word_type_collection_id_uindex;

alter table training drop column collection_id;


alter table training
    add constraint training_word_type_rating_uindex
        unique (word, type, rating);


INSERT INTO `repeat`.training_save (id, user_id, word, type, `status`, `repeat`, used) VALUES (15, 281861745, 'grandfather', 'ToEnglish', 'second',  '2022-02-17 19:12:07', 0);
INSERT INTO `repeat`.training_save (id, user_id, word, type, `status`, `repeat`, used) VALUES (16, 281861745, 'granddaughter', 'ToEnglish', 'second',  '2022-02-17 19:13:29', 0);
INSERT INTO `repeat`.training_save (id, user_id, word, type, `status`, `repeat`, used) VALUES (17, 281861745, 'grandson', 'ToEnglish', 'never',  '2022-02-17 19:34:49', 0);
INSERT INTO `repeat`.training_save (id, user_id, word, type, `status`, `repeat`, used) VALUES (18, 281861745, 'marriage', 'FromEnglish', 'never',  '2021-02-23 20:13:28', 0);

