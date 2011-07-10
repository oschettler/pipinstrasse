/* Reihenfolge in Alben */
ALTER TABLE `photos` 
ADD `seq` INT  NOT NULL  DEFAULT '0'  AFTER `von`,
ADD INDEX (`topic_id`, `seq`);
