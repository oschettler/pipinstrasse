/* Kommentarzähler */
ALTER TABLE `photos` ADD `comment_count` INT  NOT NULL  DEFAULT '0'  AFTER `title`;
UPDATE photos SET comment_count = (
  SELECT COUNT(*) AS n FROM comments WHERE object_type = 'photo' AND object_id = photos.id);
