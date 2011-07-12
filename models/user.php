<?php
define('USER_EMAIL_RE', '/^[-\.+\w]+@\w[-\w\.]+\w$/');
define('USER_RESOLUTION', '100x100');

class user extends model {
  
  /**
   * Validation
   */ 
  function validate($data, $options = array()) {
    $message = array();
    if (empty($data['mail']) || !preg_match(USER_EMAIL_RE, $data['mail'])) {
      $message[] = 'Bitte geben Sie eine gültige E-Mail-Adresse an';
    }
    
    if ($options['password_is_mandatory']) {
      // Bei der Registrierung ist das Kennwort Pflichtfeld
      if (empty($data['password']) || empty($data['password2']) || $data['password'] != $data['password2']) {
        $message[] = 'Bitte geben Sie das Kennwort zweimal an';
      }
    }
    else {
      // Sonst wird nur geprüft, ob beide Kennwort gleich
      if (!empty($data['password']) && $data['password'] != $data['password2']) {
        $message[] = 'Bitte geben Sie das Kennwort zweimal an';
      }
    }
    if (empty($data['vorname'])) {
      $message[] = 'Bitte geben Sie Ihren Vornamen an';
    }
    if (empty($data['nachname'])) {
      $message[] = 'Bitte geben Sie Ihren Nachnamen an';
    }
    if (empty($data['hausnummer'])) {
      $message[] = 'Bitte geben Sie Ihre Hausnummer an';
    }
    return $message;
  }
  
  function save($data) {  
    global $config;
    
    $user = $this->one('SELECT id FROM users WHERE mail = '
      . "'" . mysql_real_escape_string($data['mail']) . "'");
      
    if ($user) { 
      $sql = 'UPDATE users SET ';
    }
    else {
      $sql = 'INSERT INTO users SET ';
    }

    if (!empty($data['password'])) {
      $sql .= 'password = MD5(' .  "'" . mysql_real_escape_string($data['password']) . "'), ";
    }
    
    $slug = controller::slug("{$data['hausnummer']}-{$data['vorname']}-{$data['nachname']}");
    
    $sql .= 
        'slug = ' . "'" . mysql_real_escape_string($slug) . "', "
      . 'mail = ' . "'" . mysql_real_escape_string($data['mail']) . "', "
      . 'vorname = ' .  "'" . mysql_real_escape_string($data['vorname']) . "', "
      . 'nachname = ' .  "'" . mysql_real_escape_string($data['nachname']) . "', "
      . 'hausnummer = ' .  "'" . mysql_real_escape_string($data['hausnummer']) . "', "
      . 'hausnr_sort = ' . intval($data['hausnummer']) . ', '
      . 'bio = ' .  "'" . mysql_real_escape_string($data['bio']) . "', ";
      
    if ($user) {
      $sql .= 'updated = NOW()';

      $user_id = $user->id;
      $sql .= " WHERE id = {$user_id}";
    }
    else {
      $sql .= 'created = NOW()';
      $user_id = $this->user->insert_id();
    }

    if (!$this->exec($sql)) {
      return NULL;
    }

    $src = $data['avatar']['tmp_name']; 
    if (is_uploaded_file($src)) {
      $target = controller::image($user_id, NULL, 'avatars'); 
      // Speichere Bilder in einer Auflösung von 100x100
      // Erst auf Minimalwerte skalieren, dann beschneiden.
      $cmd = "{$config['convert']} {$src} -strip -gravity center -geometry '" . USER_RESOLUTION . "^' -crop " . USER_RESOLUTION . "+0+0 {$target}";
      system($cmd);
      unlink($src);
    }
    
    return $user_id;
  }
}

