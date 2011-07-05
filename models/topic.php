<?php

class topic extends model {
  
  function save($obj) {
    if (is_array($obj)) {
      $obj = (object)$obj;
    }
    $rs = mysql_query("SELECT id, von FROM topics WHERE "
      . "title = '" .  mysql_real_escape_string($obj->topic) . "'"
    );
    if ($topic = mysql_fetch_object($rs)) {
      $topic_id = $topic->id;
    }
    else {
      $sql = "INSERT INTO topics SET "
        . "title = '" .  mysql_real_escape_string($obj->topic) . "', "
        . "von = {$_SESSION['user']->id}, "
        . "slug = '" .  mysql_real_escape_string($this->slug($obj->topic)) . "', "
        . 'created = NOW()';
      mysql_query($sql);

      $topic_id = $this->insert_id();
    }
    return $topic_id;
  }
}
