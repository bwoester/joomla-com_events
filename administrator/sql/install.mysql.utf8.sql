CREATE TABLE IF NOT EXISTS `#__events_event` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`catid` INT(11)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`time_start` DATETIME NOT NULL,
`description` TEXT,
`location` VARCHAR(255),
`time_end` DATETIME,
`meeting_place` VARCHAR(255),
`meeting_time` DATETIME,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

