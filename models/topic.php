<?php

class topic extends model {
  
  function save($obj) {
    if (is_array($obj)) {
      $obj = (object)$obj;
    }
    $sql = "SELECT id, von FROM topics WHERE "
      . "title = '" .  mysql_real_escape_string($obj->topic) . "'";

    if ($topic = $this->one($sql)) {
      $topic_id = $topic->id;
    }
    else {
      $sql = "INSERT INTO topics SET "
        . "title = '" .  mysql_real_escape_string($obj->topic) . "', "
        . "von = {$_SESSION['user']->id}, "
        . "slug = '" .  mysql_real_escape_string(self::slug($obj->topic)) . "', "
        . 'created = NOW()';
      $this->exec($sql);

      $topic_id = $this->insert_id();
    }
    return $topic_id;
  }
}
