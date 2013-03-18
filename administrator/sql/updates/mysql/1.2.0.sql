DROP TABLE IF EXISTS `#__events_event`;
CREATE TABLE IF NOT EXISTS `#__events_event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `catid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `time_start` datetime NOT NULL,
  `description` text,
  `location` varchar(255) DEFAULT NULL,
  `time_end` datetime DEFAULT NULL,
  `meeting_place` varchar(255) DEFAULT NULL,
  `meeting_time` datetime DEFAULT NULL,
  `cancelled` TINYINT(1)  NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`),
  KEY `title` (`title`),
  KEY `time_start` (`time_start`),
  KEY `time_end` (`time_end`),
  KEY `meeting_time` (`meeting_time`),
  KEY `location` (`location`),
  KEY `meeting_place` (`meeting_place`),
  KEY `cancelled` (`cancelled`)
--
-- Can't specify fk on checked_out, because joomla core will set the column to
-- value "0" whenever a record is closed ("0" isn't a valid user).
-- @see "libraries/joomla/database/table.php:921" (JTable::checkIn())
-- 
-- FOREIGN KEY `fk_events_event_checked_out_users_id` (`checked_out`)
--    REFERENCES `#__users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
--
-- Can't specify fk on catid, because joomla core will set the column to
-- value "0" if we select the components "root" category (which is sematically
-- the same as specifying "not categorized").
-- 
-- FOREIGN KEY `fk_events_event_catid_categories_id` (`catid`)
--  REFERENCES `#__categories` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;