/* Sortierung nach hausnr_sort (numerisch); Anzeige wie bisher aus hausnummer */
ALTER TABLE `users` 
ADD `hausnr_sort` INT  NOT NULL  DEFAULT '0'  AFTER `hausnummer`,
ADD INDEX (`hausnr_sort`);
UPDATE users SET hausnr_sort = floor(hausnummer), updated = NOW();