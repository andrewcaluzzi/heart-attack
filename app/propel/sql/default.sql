
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- cards
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `cards`;

CREATE TABLE `cards`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `game_id` int(10) unsigned NOT NULL,
    `player_id` int(10) unsigned,
    `cards` TEXT NOT NULL,
    `type` enum('PILE','HAND','PLAY'),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- games
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `games`;

CREATE TABLE `games`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `draw` int(10) unsigned,
    `discard` int(10) unsigned,
    `shop` int(10) unsigned,
    `player_one_id` int(10) unsigned NOT NULL,
    `player_two_id` int(10) unsigned NOT NULL,
    `last_turn_id` int(10) unsigned,
    PRIMARY KEY (`id`),
    INDEX `fk_player_one` (`player_one_id`),
    INDEX `fk_player_two` (`player_two_id`),
    INDEX `fk_draw_idx` (`draw`),
    INDEX `fk_discard_idx` (`discard`),
    INDEX `fk_shop_idx` (`shop`),
    INDEX `fk_last_turn` (`last_turn_id`),
    CONSTRAINT `fk_discard`
        FOREIGN KEY (`discard`)
        REFERENCES `cards` (`id`)
        ON UPDATE CASCADE,
    CONSTRAINT `fk_draw`
        FOREIGN KEY (`draw`)
        REFERENCES `cards` (`id`)
        ON UPDATE CASCADE,
    CONSTRAINT `fk_last_turn`
        FOREIGN KEY (`last_turn_id`)
        REFERENCES `turns` (`id`)
        ON UPDATE CASCADE,
    CONSTRAINT `fk_player_one`
        FOREIGN KEY (`player_one_id`)
        REFERENCES `players` (`id`)
        ON UPDATE CASCADE,
    CONSTRAINT `fk_player_two`
        FOREIGN KEY (`player_two_id`)
        REFERENCES `players` (`id`)
        ON UPDATE CASCADE,
    CONSTRAINT `fk_shop`
        FOREIGN KEY (`shop`)
        REFERENCES `cards` (`id`)
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
