/* Chat */
CREATE TABLE `chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  INDEX  (`created`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users` ADD `chat` datetime NULL DEFAULT NULL  AFTER `updated`;
ALTER TABLE `users` ADD INDEX  (`chat`);
