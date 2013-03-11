CREATE TABLE IF NOT EXISTS `#__events_` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`title` VARCHAR(255)  NOT NULL ,
`description` TEXT(65535)  NOT NULL ,
`location` VARCHAR(255)  NOT NULL ,
`time_start` DATETIME NOT NULL ,
`time_end` DATETIME NOT NULL ,
`meeting_place` VARCHAR(255)  NOT NULL ,
`meeting_time` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

