<?php
class photo extends model {

  function load($id) {
    $sql = 'SELECT p.created AS p_created, p.updated AS p_updated, p.id AS p_id, u.id AS u_id, t.title AS topic, p.*, u.* 
    FROM photos p 
    LEFT JOIN users u ON p.von = u.id 
    LEFT JOIN topics t ON p.topic_id = t.id 
    WHERE p.id = ' . intval($id);

    return $this->one($sql);
  }
}
