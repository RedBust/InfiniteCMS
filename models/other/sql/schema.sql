CREATE TABLE `accounts` (`guid` INT AUTO_INCREMENT, `account` VARCHAR(30) NOT NULL, `pass` VARCHAR(50) NOT NULL, `level` INT DEFAULT '0' NOT NULL, `email` VARCHAR(100) NOT NULL, `lastip` VARCHAR(15) NOT NULL, `lastconnectiondate` VARCHAR(100) NOT NULL, `question` VARCHAR(100) DEFAULT 'DELETE?' NOT NULL, `reponse` VARCHAR(100) DEFAULT 'DELETE' NOT NULL, `pseudo` VARCHAR(30) NOT NULL, `banned` TINYINT DEFAULT '0' NOT NULL, `reload_needed` TINYINT DEFAULT '1' NOT NULL, `bankkamas` INT DEFAULT '0' NOT NULL, `bank` TEXT NOT NULL, `friends` TEXT NOT NULL, `stable` TEXT NOT NULL, PRIMARY KEY(`guid`)) ENGINE = INNODB;
CREATE TABLE `personnages` (`guid` INT, `name` VARCHAR(30) NOT NULL, `sexe` TINYINT NOT NULL, `class` SMALLINT NOT NULL, `color1` INT NOT NULL, `color2` INT NOT NULL, `color3` INT NOT NULL, `kamas` INT NOT NULL, `spellboost` INT NOT NULL, `capital` INT NOT NULL, `energy` INT DEFAULT '10000' NOT NULL, `level` INT NOT NULL, `xp` BIGINT DEFAULT '0' NOT NULL, `size` INT NOT NULL, `gfx` INT NOT NULL, `alignement` INT DEFAULT '0' NOT NULL, `honor` INT DEFAULT '0' NOT NULL, `deshonor` INT DEFAULT '0' NOT NULL, `alvl` INT DEFAULT '1' NOT NULL, `account` INT NOT NULL, `vitalite` INT, `force` INT DEFAULT '0' NOT NULL, `sagesse` INT DEFAULT '0' NOT NULL, `intelligence` INT DEFAULT '0' NOT NULL, `chance` INT DEFAULT '0' NOT NULL, `agilite` INT DEFAULT '0' NOT NULL, `seespell` TINYINT DEFAULT '0' NOT NULL, `seefriend` TINYINT DEFAULT '1' NOT NULL, `canaux` VARCHAR(15) DEFAULT '*#%!pi$:?' NOT NULL, `map` INT DEFAULT '8479' NOT NULL, `cell` INT NOT NULL, `pdvper` INT DEFAULT '100' NOT NULL, `spells` TEXT NOT NULL, `objets` TEXT NOT NULL, `savepos` VARCHAR(20) DEFAULT '10298,314' NOT NULL, `zaaps` VARCHAR(250) DEFAULT '' NOT NULL, `jobs` TEXT NOT NULL, `mountxpgive` INT DEFAULT '0' NOT NULL, `mount` INT DEFAULT '-1' NOT NULL, INDEX `account_idx` (`account`), INDEX `mount_idx` (`mount`), PRIMARY KEY(`guid`)) ENGINE = INNODB;
CREATE TABLE `comment` (`id` BIGINT AUTO_INCREMENT, `news_id` BIGINT, `author_id` BIGINT, `title` VARCHAR(255), `content` text, `created_at` DATETIME NOT NULL, INDEX `news_id_idx` (`news_id`), INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `event` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), `period` datetime, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `event_participant` (`id` BIGINT AUTO_INCREMENT, `event_id` INT, `character_id` INT, INDEX `character_id_idx` (`character_id`), INDEX `event_id_idx` (`event_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `guilds` (`id` INT NOT NULL, `name` VARCHAR(50) NOT NULL, `emblem` VARCHAR(20) NOT NULL, `lvl` INT DEFAULT '1' NOT NULL, `xp` BIGINT DEFAULT '0' NOT NULL) ENGINE = INNODB;
CREATE TABLE `guild_members` (`guid` INT, `guild` INT NOT NULL, `rank` INT NOT NULL, `rights` INT NOT NULL, `xpdone` BIGINT NOT NULL, `pxp` INT NOT NULL, INDEX `guild_idx` (`guild`), PRIMARY KEY(`guid`)) ENGINE = INNODB;
CREATE TABLE `items` (`guid` INT, `template` INT NOT NULL, `qua` INT NOT NULL, `pos` INT NOT NULL, `stats` TEXT NOT NULL, PRIMARY KEY(`guid`)) ENGINE = INNODB;
CREATE TABLE `live_action` (`id` INT AUTO_INCREMENT, `playerid` INT NOT NULL, `action` INT NOT NULL, `nombre` INT NOT NULL, INDEX `playerid_idx` (`playerid`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `mounts_data` (`id` INT, `color` INT NOT NULL, `sexe` INT NOT NULL, `name` VARCHAR(30) NOT NULL, `xp` INT NOT NULL, `level` INT NOT NULL, `endurance` INT NOT NULL, `amour` INT NOT NULL, `maturite` INT NOT NULL, `serenite` INT NOT NULL, `reproductions` INT NOT NULL, `fatigue` INT NOT NULL, `energie` INT NOT NULL, `items` TEXT NOT NULL, `ancetres` VARCHAR(50) DEFAULT ',,,,,,,,,,,,,' NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `mountpark_data` (`mapid` INT, `size` INT NOT NULL, `owner` INT NOT NULL, `guild` INT DEFAULT '-1' NOT NULL, `price` INT DEFAULT '0' NOT NULL, `data` TEXT NOT NULL, PRIMARY KEY(`mapid`)) ENGINE = INNODB;
CREATE TABLE `news` (`id` BIGINT AUTO_INCREMENT, `author_id` BIGINT, `title` VARCHAR(255), `content` text, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `poll` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), `date_start` DATE, `date_end` DATE, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `poll_option` (`id` BIGINT AUTO_INCREMENT, `poll_id` BIGINT, `name` VARCHAR(255), INDEX `poll_id_idx` (`poll_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `poll_option_user` (`id` BIGINT AUTO_INCREMENT, `poll_option_id` BIGINT, `account_id` BIGINT, INDEX `account_id_idx` (`account_id`), INDEX `poll_option_id_idx` (`poll_option_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `private_message_answer` (`id` BIGINT AUTO_INCREMENT, `thread_id` INT, `author_id` INT, `message` text, `created_at` DATETIME NOT NULL, INDEX `thread_id_idx` (`thread_id`), INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `private_message_thread` (`id` BIGINT AUTO_INCREMENT, `title` VARCHAR(255), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `private_message_thread_receiver` (`id` BIGINT AUTO_INCREMENT, `thread_id` INT, `user_guid` INT, `next_page` INT DEFAULT 1, INDEX `thread_id_idx` (`thread_id`), INDEX `user_guid_idx` (`user_guid`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `review` (`id` BIGINT AUTO_INCREMENT, `author_id` BIGINT, `comment` text, `created_at` DATETIME NOT NULL, INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `shop_item` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), `cost` BIGINT, `description` text, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `shop_item_effect` (`id` BIGINT AUTO_INCREMENT, `item_id` BIGINT, `type` BIGINT, `value` BIGINT, INDEX `item_id_idx` (`item_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `ticket_answer` (`id` BIGINT AUTO_INCREMENT, `ticket_id` BIGINT, `author_id` BIGINT, `content` text, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, INDEX `ticket_id_idx` (`ticket_id`), INDEX `author_id_idx` (`author_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `ticket_category` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255), `icon` VARCHAR(40), `description` text, `root_id` BIGINT, `lft` INT, `rgt` INT, `level` SMALLINT, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `user` (`id` BIGINT AUTO_INCREMENT, `guid` BIGINT, `lastvote` BIGINT, `points` BIGINT DEFAULT 0, `audiotel` BIGINT DEFAULT 0, `votes` BIGINT DEFAULT 0, `lastip` BIGINT, `culture` VARCHAR(255), INDEX `guid_idx` (`guid`), PRIMARY KEY(`id`)) ENGINE = INNODB;
ALTER TABLE `personnages` ADD CONSTRAINT `personnages_mount_mounts_data_id` FOREIGN KEY (`mount`) REFERENCES `mounts_data`(`id`);
ALTER TABLE `personnages` ADD CONSTRAINT `personnages_account_accounts_guid` FOREIGN KEY (`account`) REFERENCES `accounts`(`guid`);
ALTER TABLE `comment` ADD CONSTRAINT `comment_news_id_news_id` FOREIGN KEY (`news_id`) REFERENCES `news`(`id`);
ALTER TABLE `comment` ADD CONSTRAINT `comment_author_id_user_id` FOREIGN KEY (`author_id`) REFERENCES `user`(`id`);
ALTER TABLE `event_participant` ADD CONSTRAINT `event_participant_event_id_event_id` FOREIGN KEY (`event_id`) REFERENCES `event`(`id`);
ALTER TABLE `event_participant` ADD CONSTRAINT `event_participant_character_id_personnages_guid` FOREIGN KEY (`character_id`) REFERENCES `personnages`(`guid`);
ALTER TABLE `guild_members` ADD CONSTRAINT `guild_members_guild_guilds_id` FOREIGN KEY (`guild`) REFERENCES `guilds`(`id`);
ALTER TABLE `live_action` ADD CONSTRAINT `live_action_playerid_personnages_guid` FOREIGN KEY (`playerid`) REFERENCES `personnages`(`guid`);
ALTER TABLE `news` ADD CONSTRAINT `news_author_id_user_id` FOREIGN KEY (`author_id`) REFERENCES `user`(`id`);
ALTER TABLE `poll_option` ADD CONSTRAINT `poll_option_poll_id_poll_id` FOREIGN KEY (`poll_id`) REFERENCES `poll`(`id`);
ALTER TABLE `poll_option_user` ADD CONSTRAINT `poll_option_user_poll_option_id_poll_option_id` FOREIGN KEY (`poll_option_id`) REFERENCES `poll_option`(`id`);
ALTER TABLE `poll_option_user` ADD CONSTRAINT `poll_option_user_account_id_user_id` FOREIGN KEY (`account_id`) REFERENCES `user`(`id`);
ALTER TABLE `private_message_answer` ADD CONSTRAINT `private_message_answer_thread_id_private_message_thread_id` FOREIGN KEY (`thread_id`) REFERENCES `private_message_thread`(`id`);
ALTER TABLE `private_message_answer` ADD CONSTRAINT `private_message_answer_author_id_user_guid` FOREIGN KEY (`author_id`) REFERENCES `user`(`guid`);
ALTER TABLE `private_message_thread_receiver` ADD CONSTRAINT `ptpi` FOREIGN KEY (`thread_id`) REFERENCES `private_message_thread`(`id`);
ALTER TABLE `private_message_thread_receiver` ADD CONSTRAINT `private_message_thread_receiver_user_guid_user_guid` FOREIGN KEY (`user_guid`) REFERENCES `user`(`guid`);
ALTER TABLE `private_message_thread_receiver` ADD CONSTRAINT `private_message_thread_receiver_user_guid_accounts_guid` FOREIGN KEY (`user_guid`) REFERENCES `accounts`(`guid`);
ALTER TABLE `review` ADD CONSTRAINT `review_author_id_user_id` FOREIGN KEY (`author_id`) REFERENCES `user`(`id`);
ALTER TABLE `shop_item_effect` ADD CONSTRAINT `shop_item_effect_item_id_shop_item_id` FOREIGN KEY (`item_id`) REFERENCES `shop_item`(`id`);
ALTER TABLE `ticket_answer` ADD CONSTRAINT `ticket_answer_ticket_id_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket`(`id`);
ALTER TABLE `ticket_answer` ADD CONSTRAINT `ticket_answer_author_id_user_id` FOREIGN KEY (`author_id`) REFERENCES `user`(`id`);
ALTER TABLE `user` ADD CONSTRAINT `user_guid_accounts_guid` FOREIGN KEY (`guid`) REFERENCES `accounts`(`guid`);
CREATE TABLE `ticket` (`id` BIGINT AUTO_INCREMENT, `category_id` BIGINT, `state` VARCHAR(255), `name` VARCHAR(255), INDEX `category_id_idx` (`category_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
ALTER TABLE `ticket` ADD CONSTRAINT `ticket_category_id_ticket_category_id` FOREIGN KEY (`category_id`) REFERENCES `ticket_category`(`id`);
