<?php

class topic extends model {
  
  function save($obj) {
    global $db;
    if (is_array($obj)) {
      $obj = (object)$obj;
    }
    $sql = "SELECT id, von FROM topics WHERE "
      . "title = '" .  mysqli_real_escape_string($db, $obj->topic) . "'";

    if ($topic = $this->one($sql)) {
      $topic_id = $topic->id;
    }
    else {
      $sql = "INSERT INTO topics SET "
        . "title = '" .  mysqli_real_escape_string($db, $obj->topic) . "', "
        . "von = {$_SESSION['user']->id}, "
        . "slug = '" .  mysqli_real_escape_string($db, self::slug($obj->topic)) . "', "
        . 'created = NOW()';
      $this->exec($sql);

      $topic_id = $this->insert_id();
    }
    return $topic_id;
  }
}
