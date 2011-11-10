CREATE TABLE `comment` (`id` BIGINT AUTO_INCREMENT, `news_id` BIGINT, `author_id` BIGINT, `title` VARCHAR(255), `content` text, `created_at` DATETIME NOT NULL, INDEX `news_id_idx` (`news_id`), INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `contest` (`id` BIGINT AUTO_INCREMENT, `reward_id` INT, `name` TEXT, `ended` INT DEFAULT 0, `level` INT, INDEX `reward_id_idx` (`reward_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `contest_juror` (`id` BIGINT AUTO_INCREMENT, `contest_id` INT, `user_id` INT, INDEX `contest_id_idx` (`contest_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `contest_participant` (`id` BIGINT AUTO_INCREMENT, `contest_id` INT, `character_id` INT, `votes` BIGINT DEFAULT 0, `position` BIGINT DEFAULT 0, INDEX `contest_id_idx` (`contest_id`), INDEX `character_id_idx` (`character_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `contest_voter` (`id` BIGINT AUTO_INCREMENT, `contest_id` INT, `user_id` INT, INDEX `contest_id_idx` (`contest_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `event` (`id` BIGINT AUTO_INCREMENT, `guild_id` BIGINT, `winner_id` BIGINT, `reward_id` BIGINT, `is_tombola` bool, `name` VARCHAR(255), `period` datetime, `capacity` BIGINT DEFAULT -1, INDEX `guild_id_idx` (`guild_id`), INDEX `winner_id_idx` (`winner_id`), INDEX `reward_id_idx` (`reward_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `event_participant` (`id` BIGINT AUTO_INCREMENT, `event_id` INT, `character_id` INT, INDEX `character_id_idx` (`character_id`), INDEX `event_id_idx` (`event_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `news` (`id` BIGINT AUTO_INCREMENT, `author_id` BIGINT, `title` VARCHAR(255), `content` text, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `poll` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), `date_start` DATE, `date_end` DATE, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `poll_option` (`id` BIGINT AUTO_INCREMENT, `poll_id` BIGINT, `name` VARCHAR(255), INDEX `poll_id_idx` (`poll_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `poll_option_user` (`id` BIGINT AUTO_INCREMENT, `poll_option_id` BIGINT, `account_id` BIGINT, INDEX `account_id_idx` (`account_id`), INDEX `poll_option_id_idx` (`poll_option_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `private_message_answer` (`id` BIGINT AUTO_INCREMENT, `thread_id` INT, `author_id` INT, `message` text, `created_at` DATETIME NOT NULL, INDEX `thread_id_idx` (`thread_id`), INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `private_message_thread` (`id` BIGINT AUTO_INCREMENT, `title` VARCHAR(255), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `private_message_thread_receiver` (`id` BIGINT AUTO_INCREMENT, `thread_id` INT, `account_id` INT, `present` bool DEFAULT 1, `next_page` INT DEFAULT 1, INDEX `thread_id_idx` (`thread_id`), INDEX `account_id_idx` (`account_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `review` (`id` BIGINT AUTO_INCREMENT, `author_id` BIGINT, `comment` text, `created_at` DATETIME NOT NULL, INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `shop_category` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `shop_item` (`id` BIGINT AUTO_INCREMENT, `category_id` BIGINT, `name` VARCHAR(255), `cost` BIGINT, `cost_vip` BIGINT, `description` text, `is_vip` TINYINT(1) DEFAULT '0', `is_lottery` TINYINT(1) DEFAULT '0', `is_hidden` TINYINT(1) DEFAULT '0', INDEX `category_id_idx` (`category_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `shop_item_effect` (`id` BIGINT AUTO_INCREMENT, `item_id` BIGINT, `type` BIGINT, `value` VARCHAR(255), INDEX `item_id_idx` (`item_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `staff_role` (`id` BIGINT AUTO_INCREMENT, `account_id` BIGINT, `name` VARCHAR(255), INDEX `account_id_idx` (`account_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `ticket` (`id` BIGINT AUTO_INCREMENT, `category_id` BIGINT, `state` VARCHAR(255), `name` VARCHAR(255), INDEX `category_id_idx` (`category_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `ticket_answer` (`id` BIGINT AUTO_INCREMENT, `ticket_id` BIGINT, `author_id` BIGINT, `content` text, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, INDEX `ticket_id_idx` (`ticket_id`), INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `ticket_category` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), `icon` VARCHAR(40), `description` text, `root_id` BIGINT, `lft` INT, `rgt` INT, `level` SMALLINT, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `user` (`id` BIGINT AUTO_INCREMENT, `guid` BIGINT, `lastvote` BIGINT, `points` BIGINT DEFAULT 0, `audiotel` BIGINT DEFAULT 0, `votes` BIGINT DEFAULT 0, `lastip` BIGINT, `culture` VARCHAR(255), `main_char` BIGINT, INDEX `guid_idx` (`guid`), PRIMARY KEY(`id`)) ENGINE = INNODB;

#These are integrity checks.
#O.K., I'd only need half of them (guild delete + reset main_char) if I worked with native checks. But srsly, it's Ancestra.
DELIMITER //
CREATE TRIGGER clean_delchar AFTER DELETE ON `personnages`
FOR EACH ROW
BEGIN
	--these 2 can be done through constraints
	DELETE FROM `event_participant` WHERE character_id = OLD.guid;
	DELETE FROM `contest_participant` WHERE character_id = OLD.guid;
	UPDATE `user` SET main_char = 0 WHERE main_char = OLD.guid;
END//

CREATE TRIGGER clean_delgm AFTER DELETE ON `guild_members`
FOR EACH ROW
BEGIN --This can be done through constraint
	DELETE FROM `event_participant` WHERE character_id = OLD.guid AND event_id IN (SELECT id FROM event WHERE guild_id = OLD.guild);
END//

CREATE TRIGGER clean_delguild AFTER DELETE ON `guilds`
FOR EACH ROW
BEGIN
	DELETE FROM `event_participant` WHERE event_id IN (SELECT id FROM `event` WHERE guild_id = OLD.id);
	--this can be done through constraint
	DELETE FROM `event` WHERE guild_id = OLD.id;
END//

CREATE TRIGGER clean_delacc AFTER DELETE ON `accounts`
FOR EACH ROW
BEGIN
	--this can be done through constraint
	DELETE FROM `user` WHERE `guid` = OLD.guid;
END//

CREATE TRIGGER clean_deluser AFTER DELETE ON `user`
FOR EACH ROW
BEGIN
	
	DELETE FROM `contest_juror` WHERE user_id = OLD.id;
	DELETE FROM `contest_voter` WHERE user_id = OLD.id;
END//