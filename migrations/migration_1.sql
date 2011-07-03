/* Versionierung des Datenbankschemas */ 
CREATE TABLE `migrations` (
  `last_id` int(11) NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
