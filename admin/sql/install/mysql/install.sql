CREATE TABLE IF NOT EXISTS `#__spsimpleportfolio_items` (
  `spsimpleportfolio_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(55) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image` text NOT NULL,
  `video` text NOT NULL,
  `description` mediumtext,
  `spsimpleportfolio_tag_id` text NOT NULL,
  `url` text NOT NULL,
  `enabled` tinyint(3) NOT NULL DEFAULT '1',
  `language` varchar(255) NOT NULL DEFAULT '*',
  `access` int(5) NOT NULL DEFAULT '1',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `created_by` bigint(20) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` bigint(20) NOT NULL DEFAULT '0',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by` bigint(20) NOT NULL DEFAULT '0',
  `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`spsimpleportfolio_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__spsimpleportfolio_tags` (
  `spsimpleportfolio_tag_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(55) NOT NULL,
  `language` varchar(255) NOT NULL DEFAULT '*',
  PRIMARY KEY (`spsimpleportfolio_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;