ALTER TABLE  `topics` ADD `shared` TINYINT( 1 ) NOT NULL COMMENT  'Alben, in die von jedem Bilder eingestellt werden dürfen' AFTER  `von`,
ADD INDEX ( `shared` )