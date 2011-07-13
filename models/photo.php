<?php
define('IMAGE_RESOLUTION', '1024x768');

class photo extends model {

  function load($id) {
    $sql = 'SELECT p.created AS p_created, p.updated AS p_updated, p.id AS p_id, u.id AS u_id, t.title AS topic, p.*, u.* 
    FROM photos p 
    LEFT JOIN users u ON p.von = u.id 
    LEFT JOIN topics t ON p.topic_id = t.id 
    WHERE p.id = ' . intval($id);

    return $this->one($sql);
  }
  
  function save($data) {
    global $config;

    $id = !empty($data['id']) && $data['id'] ? $data['id'] : 0;

    if ($id) {
      $sql = 'UPDATE photos SET '      
        . 'updated = NOW(), ';
    }
    else {
      $sql = 'INSERT INTO photos SET '
        . 'created = NOW(), ';
      
      $seq = $this->one("SELECT MAX(seq) AS seq FROM photos");
      $seq = $seq->seq + 1;

      $sql .= "seq = {$seq}, ";
    }
    
    $sql .=
        'von = ' .  "'" . mysql_real_escape_string($data['von']) . "', "
      . 'title = ' .  "'" . mysql_real_escape_string($data['title']) . "' ";

    if ($data['topic_id']) {
      $sql .= ", topic_id = {$data['topic_id']} ";
    }
      
    if ($id) {
      $sql .= "WHERE id = {$id}";
    }

    $result = $this->exec($sql);
    if (!$result) {
      error_log("Import: " . mysql_error());
      return NULL;
    }
    
    if ($id) {
      // Action: edit   
      if (!$data['upload']['name']) {
        return $id;
      }
    }
    else {
      $id = $this->insert_id();
    }

    // Ab hier wird in jedem Fall ein Bild hochgeladen

    $src = $data['upload']['tmp_name'];
    if ($data['batch'] || (empty($data['batch']) && is_uploaded_file($src))) {
      $target = controller::image($id);
      // Speichere Bilder in einer Auflösung von 1024x768
      system("{$config['convert']} {$src} -strip -geometry " . IMAGE_RESOLUTION . " {$target}");
      unlink($src);

      return $id;
    } else {
      error_log("Import: {$src} ist kein Upload");
      return NULL;
    }
  }
}
