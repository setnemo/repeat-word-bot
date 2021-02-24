
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
#
# INSERT INTO `repeat`.training_save (id, user_id, word, type, `status`, `repeat`, used) VALUES (15, 281861745, 'grandfather', 'ToEnglish', 'second',  '2022-02-17 19:12:07', 0);
# INSERT INTO `repeat`.training_save (id, user_id, word, type, `status`, `repeat`, used) VALUES (16, 281861745, 'granddaughter', 'ToEnglish', 'second',  '2022-02-17 19:13:29', 0);
# INSERT INTO `repeat`.training_save (id, user_id, word, type, `status`, `repeat`, used) VALUES (17, 281861745, 'grandson', 'ToEnglish', 'never',  '2022-02-17 19:34:49', 0);
# INSERT INTO `repeat`.training_save (id, user_id, word, type, `status`, `repeat`, used) VALUES (18, 281861745, 'marriage', 'FromEnglish', 'never',  '2021-02-23 20:13:28', 0);


delete from `repeat`.collection where 1;

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (1, 'Популряность 12/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (2, 'Популряность 12/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (3, 'Популряность 12/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (4, 'Популряность 11/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (5, 'Популряность 11/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (6, 'Популряность 11/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (7, 'Популряность 10/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (8, 'Популряность 10/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (9, 'Популряность 10/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (10, 'Популряность 9/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (11, 'Популряность 9/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (12, 'Популряность 9/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (13, 'Популряность 8/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (14, 'Популряность 8/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (15, 'Популряность 8/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (16, 'Популряность 7/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (17, 'Популряность 7/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (18, 'Популряность 7/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (19, 'Популряность 6/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (20, 'Популряность 6/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (21, 'Популряность 6/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (22, 'Популряность 5/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (23, 'Популряность 5/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (24, 'Популряность 5/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (25, 'Популряность 4/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (26, 'Популряность 4/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (27, 'Популряность 4/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (28, 'Популряность 3/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (29, 'Популряность 3/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (30, 'Популряность 3/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (31, 'Популряность 2/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (32, 'Популряность 2/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (33, 'Популряность 2/12 Часть 3', '2021-02-24 10:25:23', 0);

INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (34, 'Популряность 1/12 Часть 1', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (35, 'Популряность 1/12 Часть 2', '2021-02-24 10:25:23', 0);
INSERT INTO `repeat`.collection (id, name, created_at, public) VALUES (36, 'Популряность 1/12 Часть 3', '2021-02-24 10:25:23', 0);
