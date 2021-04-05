CREATE TABLE IF NOT EXISTS `#__spsimpleportfolio_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(55) NOT NULL,
  `catid` int NOT NULL,
  `image` text NOT NULL,
  `thumbnail` text NOT NULL,
  `video` text NOT NULL,
  `description` mediumtext,
  `client` varchar(100) NOT NULL DEFAULT '',
  `client_avatar` text NOT NULL,
  `tagids` text NOT NULL,
  `url` text NOT NULL,
  `published` tinyint NOT NULL DEFAULT '1',
  `language` varchar(255) NOT NULL DEFAULT '*',
  `access` int NOT NULL DEFAULT '1',
  `ordering` int NOT NULL DEFAULT '0',
  `created_by` bigint NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified_by` bigint NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  `checked_out` bigint NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__spsimpleportfolio_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
