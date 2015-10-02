ALTER TABLE `#__spsimpleportfolio_items` ADD `language` VARCHAR(255) NOT NULL DEFAULT '*' AFTER `enabled`;
ALTER TABLE `#__spsimpleportfolio_items` ADD `category_id` int(11) NOT NULL AFTER `alias`;
ALTER TABLE `#__spsimpleportfolio_tags` ADD `language` VARCHAR(255) NOT NULL DEFAULT '*' AFTER `alias`;