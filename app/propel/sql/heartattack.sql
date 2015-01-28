
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- games
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `games`;

CREATE TABLE `games`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `deck` TEXT,
    `discard` TEXT,
    `shop` TEXT,
    `playerOne` int(10) unsigned NOT NULL,
    `playerTwo` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_playerOne` (`playerOne`),
    INDEX `fk_playerTwo_idx` (`playerTwo`),
    CONSTRAINT `fk_playerOne`
        FOREIGN KEY (`playerOne`)
        REFERENCES `players` (`id`)
        ON UPDATE CASCADE,
    CONSTRAINT `fk_playerTwo`
        FOREIGN KEY (`playerTwo`)
        REFERENCES `players` (`id`)
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- players
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `players`;

CREATE TABLE `players`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `name_UNIQUE` (`name`),
    UNIQUE INDEX `username_UNIQUE` (`username`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- turns
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `turns`;

CREATE TABLE `turns`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `game_id` int(10) unsigned NOT NULL,
    `player_id` int(10) unsigned NOT NULL,
    `phase` enum('SETUP','ATTACK','SHOP','CLEANUP') NOT NULL,
    `cards` VARCHAR(100),
    PRIMARY KEY (`id`),
    INDEX `fk_game_idx` (`game_id`),
    INDEX `fk_player_idx` (`player_id`),
    CONSTRAINT `fk_game`
        FOREIGN KEY (`game_id`)
        REFERENCES `games` (`id`)
        ON UPDATE CASCADE,
    CONSTRAINT `fk_player`
        FOREIGN KEY (`player_id`)
        REFERENCES `players` (`id`)
        ON UPDATE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
