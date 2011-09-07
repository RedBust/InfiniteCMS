CREATE TABLE `user` (`id` BIGINT AUTO_INCREMENT, `guid` BIGINT, `lastvote` BIGINT, `points` BIGINT DEFAULT 0, `audiotel` BIGINT DEFAULT 0, `votes` BIGINT DEFAULT 0, `lastip` BIGINT, `culture` VARCHAR(255), `main_char` BIGINT, INDEX `guid_idx` (`guid`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `news` (`id` BIGINT AUTO_INCREMENT, `author_id` BIGINT, `title` VARCHAR(255), `content` text, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `comment` (`id` BIGINT AUTO_INCREMENT, `news_id` BIGINT, `author_id` BIGINT, `title` VARCHAR(255), `content` text, `created_at` DATETIME NOT NULL, INDEX `news_id_idx` (`news_id`), INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `review` (`id` BIGINT AUTO_INCREMENT, `author_id` BIGINT, `comment` text, `created_at` DATETIME NOT NULL, INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `event` (`id` BIGINT AUTO_INCREMENT, `guild_id` BIGINT, `winner_id` BIGINT, `reward_id` BIGINT, `name` VARCHAR(255), `period` datetime, `capacity` BIGINT DEFAULT -1, INDEX `guild_id_idx` (`guild_id`), INDEX `winner_id_idx` (`winner_id`), INDEX `reward_id_idx` (`reward_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `event_participant` (`id` BIGINT AUTO_INCREMENT, `event_id` INT, `character_id` INT, INDEX `character_id_idx` (`character_id`), INDEX `event_id_idx` (`event_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `poll` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), `date_start` DATE, `date_end` DATE, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `poll_option` (`id` BIGINT AUTO_INCREMENT, `poll_id` BIGINT, `name` VARCHAR(255), INDEX `poll_id_idx` (`poll_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `poll_option_user` (`id` BIGINT AUTO_INCREMENT, `poll_option_id` BIGINT, `account_id` BIGINT, INDEX `account_id_idx` (`account_id`), INDEX `poll_option_id_idx` (`poll_option_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `private_message_answer` (`id` BIGINT AUTO_INCREMENT, `thread_id` INT, `author_id` INT, `message` text, `created_at` DATETIME NOT NULL, INDEX `thread_id_idx` (`thread_id`), INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `private_message_thread` (`id` BIGINT AUTO_INCREMENT, `title` VARCHAR(255), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `private_message_thread_receiver` (`id` BIGINT AUTO_INCREMENT, `thread_id` INT, `user_guid` INT, `next_page` INT DEFAULT 1, INDEX `thread_id_idx` (`thread_id`), INDEX `user_guid_idx` (`user_guid`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `shop_item` (`id` BIGINT AUTO_INCREMENT, `category_id` BIGINT, `name` VARCHAR(255), `cost` BIGINT, `cost_vip` BIGINT, `description` text, `is_vip` TINYINT(1) DEFAULT '0', `is_lottery` TINYINT(1) DEFAULT '0', `is_hidden` TINYINT(1) DEFAULT '0', PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `shop_item_category` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `shop_item_effect` (`id` BIGINT AUTO_INCREMENT, `item_id` BIGINT, `type` BIGINT, `value` BIGINT, INDEX `item_id_idx` (`item_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `ticket` (`id` BIGINT AUTO_INCREMENT, `category_id` BIGINT, `state` VARCHAR(255), `name` VARCHAR(255), INDEX `category_id_idx` (`category_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `ticket_answer` (`id` BIGINT AUTO_INCREMENT, `ticket_id` BIGINT, `author_id` BIGINT, `content` text, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, INDEX `ticket_id_idx` (`ticket_id`), INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `ticket_category` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), `icon` VARCHAR(40), `description` text, `root_id` BIGINT, `lft` INT, `rgt` INT, `level` SMALLINT, PRIMARY KEY(`id`)) ENGINE = INNODB;

#These are integrity checks.
DELIMITER //
CREATE TRIGGER clean_delchar AFTER DELETE ON `personnages`
FOR EACH ROW
BEGIN
	DELETE FROM `event_participant` WHERE character_id = OLD.guid;
	UPDATE `user` SET main_char = 0 WHERE main_char = OLD.guid;
END//

CREATE TRIGGER clean_delgm AFTER DELETE ON `guild_members`
FOR EACH ROW
BEGIN
	DELETE FROM `event_participant` WHERE character_id = OLD.guid AND event_id IN (SELECT id FROM event WHERE guild_id = OLD.guild);
END//

CREATE TRIGGER clean_delguild AFTER DELETE ON `guilds`
FOR EACH ROW
BEGIN
	DELETE FROM `event_participant` WHERE event_id IN (SELECT id FROM `event` WHERE guild_id = OLD.id);
	DELETE FROM `event` WHERE guild_id = OLD.id;
END//
DELIMITER ;