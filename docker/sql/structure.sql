-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Feb 13, 2021 at 02:15 PM
-- Server version: 5.7.22
-- PHP Version: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Table structure for table `callback_query`
--

CREATE TABLE `callback_query` (
                                  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this query',
                                  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
                                  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
                                  `message_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Unique message identifier',
                                  `inline_message_id` char(255) DEFAULT NULL COMMENT 'Identifier of the message sent via the bot in inline mode, that originated the query',
                                  `chat_instance` char(255) NOT NULL DEFAULT '' COMMENT 'Global identifier, uniquely corresponding to the chat to which the message with the callback button was sent',
                                  `data` char(255) NOT NULL DEFAULT '' COMMENT 'Data associated with the callback button',
                                  `game_short_name` char(255) NOT NULL DEFAULT '' COMMENT 'Short name of a Game to be returned, serves as the unique identifier for the game',
                                  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
                        `id` bigint(20) NOT NULL COMMENT 'Unique identifier for this chat',
                        `type` enum('private','group','supergroup','channel') NOT NULL COMMENT 'Type of chat, can be either private, group, supergroup or channel',
                        `title` char(255) DEFAULT '' COMMENT 'Title, for supergroups, channels and group chats',
                        `username` char(255) DEFAULT NULL COMMENT 'Username, for private chats, supergroups and channels if available',
                        `first_name` char(255) DEFAULT NULL COMMENT 'First name of the other party in a private chat',
                        `last_name` char(255) DEFAULT NULL COMMENT 'Last name of the other party in a private chat',
                        `all_members_are_administrators` tinyint(1) DEFAULT '0' COMMENT 'True if a all members of this group are admins',
                        `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
                        `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update',
                        `old_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier, this is filled when a group is converted to a supergroup'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `chosen_inline_result`
--

CREATE TABLE `chosen_inline_result` (
                                        `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this entry',
                                        `result_id` char(255) NOT NULL DEFAULT '' COMMENT 'The unique identifier for the result that was chosen',
                                        `user_id` bigint(20) DEFAULT NULL COMMENT 'The user that chose the result',
                                        `location` char(255) DEFAULT NULL COMMENT 'Sender location, only for bots that require user location',
                                        `inline_message_id` char(255) DEFAULT NULL COMMENT 'Identifier of the sent inline message',
                                        `query` text NOT NULL COMMENT 'The query that was used to obtain the result',
                                        `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `conversation`
--

CREATE TABLE `conversation` (
                                `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this entry',
                                `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
                                `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique user or chat identifier',
                                `status` enum('active','cancelled','stopped') NOT NULL DEFAULT 'active' COMMENT 'Conversation state',
                                `command` varchar(160) DEFAULT '' COMMENT 'Default command to execute',
                                `notes` text COMMENT 'Data stored from command',
                                `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
                                `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `edited_message`
--

CREATE TABLE `edited_message` (
                                  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this entry',
                                  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
                                  `message_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Unique message identifier',
                                  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
                                  `edit_date` timestamp NULL DEFAULT NULL COMMENT 'Date the message was edited in timestamp format',
                                  `text` text COMMENT 'For text messages, the actual UTF-8 text of the message max message length 4096 char utf8',
                                  `entities` text COMMENT 'For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text',
                                  `caption` text COMMENT 'For message with caption, the actual UTF-8 text of the caption'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `inline_query`
--

CREATE TABLE `inline_query` (
                                `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this query',
                                `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
                                `location` char(255) DEFAULT NULL COMMENT 'Location of the user',
                                `query` text NOT NULL COMMENT 'Text of the query',
                                `offset` char(255) DEFAULT NULL COMMENT 'Offset of the result',
                                `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
                           `chat_id` bigint(20) NOT NULL COMMENT 'Unique chat identifier',
                           `sender_chat_id` bigint(20) DEFAULT NULL COMMENT 'Sender of the message, sent on behalf of a chat',
                           `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique message identifier',
                           `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
                           `date` timestamp NULL DEFAULT NULL COMMENT 'Date the message was sent in timestamp format',
                           `forward_from` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier, sender of the original message',
                           `forward_from_chat` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier, chat the original message belongs to',
                           `forward_from_message_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier of the original message in the channel',
                           `forward_signature` text COMMENT 'For messages forwarded from channels, signature of the post author if present',
                           `forward_sender_name` text COMMENT 'Sender''s name for messages forwarded from users who disallow adding a link to their account in forwarded messages',
                           `forward_date` timestamp NULL DEFAULT NULL COMMENT 'date the original message was sent in timestamp format',
                           `reply_to_chat` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
                           `reply_to_message` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Message that this message is reply to',
                           `via_bot` bigint(20) DEFAULT NULL COMMENT 'Optional. Bot through which the message was sent',
                           `edit_date` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Date the message was last edited in Unix time',
                           `media_group_id` text COMMENT 'The unique identifier of a media message group this message belongs to',
                           `author_signature` text COMMENT 'Signature of the post author for messages in channels',
                           `text` text COMMENT 'For text messages, the actual UTF-8 text of the message max message length 4096 char utf8mb4',
                           `entities` text COMMENT 'For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text',
                           `caption_entities` text COMMENT 'For messages with a caption, special entities like usernames, URLs, bot commands, etc. that appear in the caption',
                           `audio` text COMMENT 'Audio object. Message is an audio file, information about the file',
                           `document` text COMMENT 'Document object. Message is a general file, information about the file',
                           `animation` text COMMENT 'Message is an animation, information about the animation',
                           `game` text COMMENT 'Game object. Message is a game, information about the game',
                           `photo` text COMMENT 'Array of PhotoSize objects. Message is a photo, available sizes of the photo',
                           `sticker` text COMMENT 'Sticker object. Message is a sticker, information about the sticker',
                           `video` text COMMENT 'Video object. Message is a video, information about the video',
                           `voice` text COMMENT 'Voice Object. Message is a Voice, information about the Voice',
                           `video_note` text COMMENT 'VoiceNote Object. Message is a Video Note, information about the Video Note',
                           `caption` text COMMENT 'For message with caption, the actual UTF-8 text of the caption',
                           `contact` text COMMENT 'Contact object. Message is a shared contact, information about the contact',
                           `location` text COMMENT 'Location object. Message is a shared location, information about the location',
                           `venue` text COMMENT 'Venue object. Message is a Venue, information about the Venue',
                           `poll` text COMMENT 'Poll object. Message is a native poll, information about the poll',
                           `dice` text COMMENT 'Message is a dice with random value from 1 to 6',
                           `new_chat_members` text COMMENT 'List of unique user identifiers, new member(s) were added to the group, information about them (one of these members may be the bot itself)',
                           `left_chat_member` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier, a member was removed from the group, information about them (this member may be the bot itself)',
                           `new_chat_title` char(255) DEFAULT NULL COMMENT 'A chat title was changed to this value',
                           `new_chat_photo` text COMMENT 'Array of PhotoSize objects. A chat photo was change to this value',
                           `delete_chat_photo` tinyint(1) DEFAULT '0' COMMENT 'Informs that the chat photo was deleted',
                           `group_chat_created` tinyint(1) DEFAULT '0' COMMENT 'Informs that the group has been created',
                           `supergroup_chat_created` tinyint(1) DEFAULT '0' COMMENT 'Informs that the supergroup has been created',
                           `channel_chat_created` tinyint(1) DEFAULT '0' COMMENT 'Informs that the channel chat has been created',
                           `migrate_to_chat_id` bigint(20) DEFAULT NULL COMMENT 'Migrate to chat identifier. The group has been migrated to a supergroup with the specified identifier',
                           `migrate_from_chat_id` bigint(20) DEFAULT NULL COMMENT 'Migrate from chat identifier. The supergroup has been migrated from a group with the specified identifier',
                           `pinned_message` text COMMENT 'Message object. Specified message was pinned',
                           `invoice` text COMMENT 'Message is an invoice for a payment, information about the invoice',
                           `successful_payment` text COMMENT 'Message is a service message about a successful payment, information about the payment',
                           `connected_website` text COMMENT 'The domain name of the website on which the user has logged in.',
                           `passport_data` text COMMENT 'Telegram Passport data',
                           `proximity_alert_triggered` text COMMENT 'Service message. A user in the chat triggered another user''s proximity alert while sharing Live Location.',
                           `reply_markup` text COMMENT 'Inline keyboard attached to the message'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `new_raids`
--

CREATE TABLE `new_raids` (
                             `id` int(11) NOT NULL,
                             `server` int(11) NOT NULL,
                             `title` varchar(200) NOT NULL,
                             `description` varchar(200) DEFAULT NULL,
                             `timestamp` varchar(20) NOT NULL,
                             `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                             `alarm` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `poll`
--

CREATE TABLE `poll` (
                        `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique poll identifier',
                        `question` text NOT NULL COMMENT 'Poll question',
                        `options` text NOT NULL COMMENT 'List of poll options',
                        `total_voter_count` int(10) UNSIGNED DEFAULT NULL COMMENT 'Total number of users that voted in the poll',
                        `is_closed` tinyint(1) DEFAULT '0' COMMENT 'True, if the poll is closed',
                        `is_anonymous` tinyint(1) DEFAULT '1' COMMENT 'True, if the poll is anonymous',
                        `type` char(255) DEFAULT NULL COMMENT 'Poll type, currently can be â€œregularâ€ or â€œquizâ€',
                        `allows_multiple_answers` tinyint(1) DEFAULT '0' COMMENT 'True, if the poll allows multiple answers',
                        `correct_option_id` int(10) UNSIGNED DEFAULT NULL COMMENT '0-based identifier of the correct answer option. Available only for polls in the quiz mode, which are closed, or was sent (not forwarded) by the bot or to the private chat with the bot.',
                        `explanation` varchar(255) DEFAULT NULL COMMENT 'Text that is shown when a user chooses an incorrect answer or taps on the lamp icon in a quiz-style poll, 0-200 characters',
                        `explanation_entities` text COMMENT 'Special entities like usernames, URLs, bot commands, etc. that appear in the explanation',
                        `open_period` int(10) UNSIGNED DEFAULT NULL COMMENT 'Amount of time in seconds the poll will be active after creation',
                        `close_date` timestamp NULL DEFAULT NULL COMMENT 'Point in time (Unix timestamp) when the poll will be automatically closed',
                        `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `poll_answer`
--

CREATE TABLE `poll_answer` (
                               `poll_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique poll identifier',
                               `user_id` bigint(20) NOT NULL COMMENT 'The user, who changed the answer to the poll',
                               `option_ids` text NOT NULL COMMENT '0-based identifiers of answer options, chosen by the user. May be empty if the user retracted their vote.',
                               `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pre_checkout_query`
--

CREATE TABLE `pre_checkout_query` (
                                      `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique query identifier',
                                      `user_id` bigint(20) DEFAULT NULL COMMENT 'User who sent the query',
                                      `currency` char(3) DEFAULT NULL COMMENT 'Three-letter ISO 4217 currency code',
                                      `total_amount` bigint(20) DEFAULT NULL COMMENT 'Total price in the smallest units of the currency',
                                      `invoice_payload` char(255) NOT NULL DEFAULT '' COMMENT 'Bot specified invoice payload',
                                      `shipping_option_id` char(255) DEFAULT NULL COMMENT 'Identifier of the shipping option chosen by the user',
                                      `order_info` text COMMENT 'Order info provided by the user',
                                      `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `request_limiter`
--

CREATE TABLE `request_limiter` (
                                   `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this entry',
                                   `chat_id` char(255) DEFAULT NULL COMMENT 'Unique chat identifier',
                                   `inline_message_id` char(255) DEFAULT NULL COMMENT 'Identifier of the sent inline message',
                                   `method` char(255) DEFAULT NULL COMMENT 'Request method',
                                   `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_query`
--

CREATE TABLE `shipping_query` (
                                  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique query identifier',
                                  `user_id` bigint(20) DEFAULT NULL COMMENT 'User who sent the query',
                                  `invoice_payload` char(255) NOT NULL DEFAULT '' COMMENT 'Bot specified invoice payload',
                                  `shipping_address` char(255) NOT NULL DEFAULT '' COMMENT 'User specified shipping address',
                                  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `telegram_update`
--

CREATE TABLE `telegram_update` (
                                   `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Update''s unique identifier',
                                   `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
                                   `message_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming message of any kind - text, photo, sticker, etc.',
                                   `edited_message_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New version of a message that is known to the bot and was edited',
                                   `channel_post_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming channel post of any kind - text, photo, sticker, etc.',
                                   `edited_channel_post_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New version of a channel post that is known to the bot and was edited',
                                   `inline_query_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming inline query',
                                   `chosen_inline_result_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'The result of an inline query that was chosen by a user and sent to their chat partner',
                                   `callback_query_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming callback query',
                                   `shipping_query_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming shipping query. Only for invoices with flexible price',
                                   `pre_checkout_query_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming pre-checkout query. Contains full information about checkout',
                                   `poll_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New poll state. Bots receive only updates about polls, which are sent or stopped by the bot',
                                   `poll_answer_poll_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'A user changed their answer in a non-anonymous poll. Bots receive new votes only in polls that were sent by the bot itself.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
                        `id` bigint(20) NOT NULL COMMENT 'Unique identifier for this user or bot',
                        `is_bot` tinyint(1) DEFAULT '0' COMMENT 'True, if this user is a bot',
                        `first_name` char(255) NOT NULL DEFAULT '' COMMENT 'User''s or bot''s first name',
                        `last_name` char(255) DEFAULT NULL COMMENT 'User''s or bot''s last name',
                        `username` char(191) DEFAULT NULL COMMENT 'User''s or bot''s username',
                        `language_code` char(10) DEFAULT NULL COMMENT 'IETF language tag of the user''s language',
                        `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
                        `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_chat`
--

CREATE TABLE `user_chat` (
                             `user_id` bigint(20) NOT NULL COMMENT 'Unique user identifier',
                             `chat_id` bigint(20) NOT NULL COMMENT 'Unique user or chat identifier'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



create table version
(
    id int auto_increment
        primary key,
    version varchar(12) not null,
    description longtext null,
    used int(1) default 0 null,
    created_at timestamp default CURRENT_TIMESTAMP null
)
    charset=utf8mb4;

create table version_notification
(
    chat_id int null,
    version_id int null,
    created_at timestamp default CURRENT_TIMESTAMP null,
    constraint version_notification_user_id_version_id_uindex
        unique (chat_id, version_id)
)
    charset=utf8mb4;

CREATE TABLE `collection` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `language` varchar(255) NOT NULL,
    `user_id` int(11) NOT NULL,
    `created_at` timestamp NULL DEFAULT NOW(),
    `public` int(1) default 0 null,
    PRIMARY KEY (`id`),
    KEY (`name`),
    UNIQUE KEY `collection_name_language_uindex` (`name`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `word` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `word` varchar(255) NOT NULL,
    `collection_id` int(11) DEFAULT NULL,
    `translate` longtext,
    `created_at` timestamp NULL DEFAULT NOW(),
    PRIMARY KEY (`id`),
    KEY (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `training` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `word_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `collection_id` int(11) NOT NULL,
    `type` varchar(255) NOT NULL,
    `word` varchar(255) NOT NULL,
    `translate` longtext,
    `status` enum('first','second','third','fourth','fifth','sixth','never') DEFAULT 'first',
    `repeat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `training_word_id_user_id_type_uindex` (`word_id`,`user_id`,`type`),
    KEY `user_id` (`user_id`),
    KEY `repeat` (`repeat`),
    KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=715 DEFAULT CHARSET=utf8mb4;








--
-- Indexes for dumped tables
--

--
-- Indexes for table `callback_query`
--
ALTER TABLE `callback_query`
    ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `chat_id_2` (`chat_id`,`message_id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
    ADD PRIMARY KEY (`id`),
  ADD KEY `old_id` (`old_id`);

--
-- Indexes for table `chosen_inline_result`
--
ALTER TABLE `chosen_inline_result`
    ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `conversation`
--
ALTER TABLE `conversation`
    ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `edited_message`
--
ALTER TABLE `edited_message`
    ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chat_id_2` (`chat_id`,`message_id`);

--
-- Indexes for table `inline_query`
--
ALTER TABLE `inline_query`
    ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
    ADD PRIMARY KEY (`chat_id`,`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `forward_from` (`forward_from`),
  ADD KEY `forward_from_chat` (`forward_from_chat`),
  ADD KEY `reply_to_chat` (`reply_to_chat`),
  ADD KEY `reply_to_message` (`reply_to_message`),
  ADD KEY `via_bot` (`via_bot`),
  ADD KEY `left_chat_member` (`left_chat_member`),
  ADD KEY `migrate_from_chat_id` (`migrate_from_chat_id`),
  ADD KEY `migrate_to_chat_id` (`migrate_to_chat_id`),
  ADD KEY `reply_to_chat_2` (`reply_to_chat`,`reply_to_message`);


--
-- Indexes for table `poll`
--
ALTER TABLE `poll`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_answer`
--
ALTER TABLE `poll_answer`
    ADD PRIMARY KEY (`poll_id`,`user_id`);

--
-- Indexes for table `pre_checkout_query`
--
ALTER TABLE `pre_checkout_query`
    ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `request_limiter`
--
ALTER TABLE `request_limiter`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipping_query`
--
ALTER TABLE `shipping_query`
    ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `telegram_update`
--
ALTER TABLE `telegram_update`
    ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `chat_message_id` (`chat_id`,`message_id`),
  ADD KEY `edited_message_id` (`edited_message_id`),
  ADD KEY `channel_post_id` (`channel_post_id`),
  ADD KEY `edited_channel_post_id` (`edited_channel_post_id`),
  ADD KEY `inline_query_id` (`inline_query_id`),
  ADD KEY `chosen_inline_result_id` (`chosen_inline_result_id`),
  ADD KEY `callback_query_id` (`callback_query_id`),
  ADD KEY `shipping_query_id` (`shipping_query_id`),
  ADD KEY `pre_checkout_query_id` (`pre_checkout_query_id`),
  ADD KEY `poll_id` (`poll_id`),
  ADD KEY `poll_answer_poll_id` (`poll_answer_poll_id`),
  ADD KEY `chat_id` (`chat_id`,`channel_post_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `user_chat`
--
ALTER TABLE `user_chat`
    ADD PRIMARY KEY (`user_id`,`chat_id`),
  ADD KEY `chat_id` (`chat_id`);

--
-- Indexes for table `version`
--
ALTER TABLE `version`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `version_notification`
--
ALTER TABLE `version_notification`
    ADD UNIQUE KEY `version_notification_user_id_version_id_uindex` (`chat_id`,`version_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chosen_inline_result`
--
ALTER TABLE `chosen_inline_result`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry';

--
-- AUTO_INCREMENT for table `conversation`
--
ALTER TABLE `conversation`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry';

--
-- AUTO_INCREMENT for table `edited_message`
--
ALTER TABLE `edited_message`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry';

--
-- AUTO_INCREMENT for table `request_limiter`
--
ALTER TABLE `request_limiter`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry';

--
-- AUTO_INCREMENT for table `version`
--
ALTER TABLE `version`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `callback_query`
--
ALTER TABLE `callback_query`
    ADD CONSTRAINT `callback_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `callback_query_ibfk_2` FOREIGN KEY (`chat_id`,`message_id`) REFERENCES `message` (`chat_id`, `id`);

--
-- Constraints for table `chosen_inline_result`
--
ALTER TABLE `chosen_inline_result`
    ADD CONSTRAINT `chosen_inline_result_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `conversation`
--
ALTER TABLE `conversation`
    ADD CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `conversation_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`);

--
-- Constraints for table `edited_message`
--
ALTER TABLE `edited_message`
    ADD CONSTRAINT `edited_message_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`),
  ADD CONSTRAINT `edited_message_ibfk_2` FOREIGN KEY (`chat_id`,`message_id`) REFERENCES `message` (`chat_id`, `id`),
  ADD CONSTRAINT `edited_message_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `inline_query`
--
ALTER TABLE `inline_query`
    ADD CONSTRAINT `inline_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
    ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`),
  ADD CONSTRAINT `message_ibfk_3` FOREIGN KEY (`forward_from`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `message_ibfk_4` FOREIGN KEY (`forward_from_chat`) REFERENCES `chat` (`id`),
  ADD CONSTRAINT `message_ibfk_5` FOREIGN KEY (`reply_to_chat`,`reply_to_message`) REFERENCES `message` (`chat_id`, `id`),
  ADD CONSTRAINT `message_ibfk_6` FOREIGN KEY (`via_bot`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `message_ibfk_7` FOREIGN KEY (`left_chat_member`) REFERENCES `user` (`id`);

--
-- Constraints for table `poll_answer`
--
ALTER TABLE `poll_answer`
    ADD CONSTRAINT `poll_answer_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `poll` (`id`);

--
-- Constraints for table `pre_checkout_query`
--
ALTER TABLE `pre_checkout_query`
    ADD CONSTRAINT `pre_checkout_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `shipping_query`
--
ALTER TABLE `shipping_query`
    ADD CONSTRAINT `shipping_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `telegram_update`
--
ALTER TABLE `telegram_update`
    ADD CONSTRAINT `telegram_update_ibfk_1` FOREIGN KEY (`chat_id`,`message_id`) REFERENCES `message` (`chat_id`, `id`),
  ADD CONSTRAINT `telegram_update_ibfk_10` FOREIGN KEY (`poll_id`) REFERENCES `poll` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_11` FOREIGN KEY (`poll_answer_poll_id`) REFERENCES `poll_answer` (`poll_id`),
  ADD CONSTRAINT `telegram_update_ibfk_2` FOREIGN KEY (`edited_message_id`) REFERENCES `edited_message` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_3` FOREIGN KEY (`chat_id`,`channel_post_id`) REFERENCES `message` (`chat_id`, `id`),
  ADD CONSTRAINT `telegram_update_ibfk_4` FOREIGN KEY (`edited_channel_post_id`) REFERENCES `edited_message` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_5` FOREIGN KEY (`inline_query_id`) REFERENCES `inline_query` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_6` FOREIGN KEY (`chosen_inline_result_id`) REFERENCES `chosen_inline_result` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_7` FOREIGN KEY (`callback_query_id`) REFERENCES `callback_query` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_8` FOREIGN KEY (`shipping_query_id`) REFERENCES `shipping_query` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_9` FOREIGN KEY (`pre_checkout_query_id`) REFERENCES `pre_checkout_query` (`id`);

--
-- Constraints for table `user_chat`
--
ALTER TABLE `user_chat`
    ADD CONSTRAINT `user_chat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_chat_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;