/* Nutzerrollen als Komma-getrenntes Feld */
ALTER TABLE `users` ADD `roles` varchar(100) NOT NULL AFTER `mail`;
