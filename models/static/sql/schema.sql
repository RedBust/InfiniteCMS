CREATE TABLE `area_data` (`id` INT NOT NULL, `name` VARCHAR(100) NOT NULL, `superarea` INT NOT NULL) ENGINE = INNODB;
CREATE TABLE `crafts` (`id` INT, `craft` TEXT NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `drops` (`id` BIGINT AUTO_INCREMENT, `mob` INT NOT NULL, `item` INT NOT NULL, `seuil` INT DEFAULT '100' NOT NULL, `max` INT NOT NULL, `taux` DECIMAL(10, 2) NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `endfight_action` (`id` BIGINT AUTO_INCREMENT, `map` INT NOT NULL, `fighttype` INT NOT NULL, `action` INT NOT NULL, `args` VARCHAR(30) NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `experience` (`id` BIGINT AUTO_INCREMENT, `lvl` INT NOT NULL, `perso` BIGINT NOT NULL, `metier` BIGINT NOT NULL, `dinde` BIGINT NOT NULL, `pvp` INT NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `interactive_objects_data` (`id` INT, `respawn` INT DEFAULT '10000' NOT NULL, `duration` INT DEFAULT '1500' NOT NULL, `unknow` INT DEFAULT '4' NOT NULL, `walkable` INT DEFAULT '1' NOT NULL, `name io` TEXT NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `item_template` (`id` INT DEFAULT '-1' NOT NULL, `type` INT DEFAULT '-1' NOT NULL, `name` VARCHAR(50) DEFAULT '' NOT NULL, `level` INT DEFAULT '1' NOT NULL, `statstemplate` TEXT NOT NULL, `pod` INT DEFAULT '0' NOT NULL, `panoplie` INT DEFAULT '-1' NOT NULL, `prix` INT DEFAULT '0' NOT NULL, `condition` VARCHAR(100) DEFAULT '' NOT NULL, `armesinfos` VARCHAR(100) DEFAULT '' NOT NULL) ENGINE = INNODB;
CREATE TABLE `itemsets` (`id` INT, `name` VARCHAR(150) NOT NULL, `items` TEXT NOT NULL, `bonus` TEXT NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `jobs_data` (`id` TINYINT, `tools` TEXT NOT NULL, `crafts` TEXT NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `maps` (`id` INT, `date` VARCHAR(50) NOT NULL, `width` INT DEFAULT '-1' NOT NULL, `heigth` INT DEFAULT '-1' NOT NULL, `places` TEXT NOT NULL, `key` TEXT NOT NULL, `mapdata` TEXT NOT NULL, `monsters` TEXT NOT NULL, `capabilities` INT DEFAULT '0' NOT NULL, `mappos` VARCHAR(15) DEFAULT '0,0,0' NOT NULL, `numgroup` INT DEFAULT '5' NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `monsters` (`id` INT, `name` VARCHAR(100) NOT NULL, `gfxid` INT NOT NULL, `align` INT NOT NULL, `grades` TEXT NOT NULL, `colors` VARCHAR(30) DEFAULT '-1,-1,-1' NOT NULL, `stats` TEXT NOT NULL, `spells` TEXT NOT NULL, `pdvs` VARCHAR(200) DEFAULT '1|1|1|1|1|1|1|1|1|1' NOT NULL, `points` VARCHAR(200) DEFAULT '1;1|1;1|1;1|1;1|1;1|1;1|1;1|1;1|1;1|1;1' NOT NULL, `inits` VARCHAR(200) DEFAULT '1|1|1|1|1|1|1|1|1|1' NOT NULL, `minkamas` INT DEFAULT '0' NOT NULL, `maxkamas` INT DEFAULT '0' NOT NULL, `exps` VARCHAR(200) DEFAULT '1|1|1|1|1|1|1|1|1|1' NOT NULL, `ai_type` INT DEFAULT '1' NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `npc_questions` (`id` INT NOT NULL, `responses` VARCHAR(100) NOT NULL, `params` VARCHAR(100) NOT NULL) ENGINE = INNODB;
CREATE TABLE `npc_reponses_actions` (`id` INT NOT NULL, `type` INT NOT NULL, `args` TEXT NOT NULL) ENGINE = INNODB;
CREATE TABLE `npc_template` (`id` INT, `bonusvalue` INT NOT NULL, `gfxid` INT NOT NULL, `scalex` INT NOT NULL, `scaley` INT NOT NULL, `sex` INT NOT NULL, `color1` INT NOT NULL, `color2` INT NOT NULL, `color3` INT NOT NULL, `accessories` VARCHAR(30) DEFAULT '0,0,0,0' NOT NULL, `extraclip` INT DEFAULT '-1' NOT NULL, `customartwork` INT DEFAULT '0' NOT NULL, `initquestion` INT DEFAULT '-1' NOT NULL, `ventes` TEXT NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `npcs` (`id` BIGINT AUTO_INCREMENT, `mapid` INT NOT NULL, `npcid` INT NOT NULL, `cellid` INT NOT NULL, `orientation` INT NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `scripted_cells` (`id` BIGINT AUTO_INCREMENT, `mapid` INT NOT NULL, `cellid` INT NOT NULL, `actionid` INT NOT NULL, `eventid` INT NOT NULL, `actionsargs` TEXT NOT NULL, `conditions` TEXT NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `sorts` (`id` INT, `nom` VARCHAR(100) NOT NULL, `sprite` INT DEFAULT '-1' NOT NULL, `spriteinfos` VARCHAR(20) DEFAULT '0,0,0' NOT NULL, `lvl1` TEXT NOT NULL, `lvl2` TEXT NOT NULL, `lvl3` TEXT NOT NULL, `lvl4` TEXT NOT NULL, `lvl5` TEXT NOT NULL, `lvl6` TEXT NOT NULL, `effecttarget` TEXT NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `subarea_data` (`id` INT NOT NULL, `area` INT NOT NULL, `alignement` INT DEFAULT '-1' NOT NULL, `name` VARCHAR(200) NOT NULL) ENGINE = INNODB;
CREATE TABLE `use_item_actions` (`id` BIGINT AUTO_INCREMENT, `template` INT NOT NULL, `type` INT NOT NULL, `args` VARCHAR(100) NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
