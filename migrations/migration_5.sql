/* Einladungen an externe Gäste */
CREATE TABLE `invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `object_type` varchar(20) NOT NULL,
  `object_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
