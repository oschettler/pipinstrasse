<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

define('ADMIN_PAGE_SIZE', 10);

class admin_controller extends controller {
  
  /**
   * Teste auf Rolle "admin"
   */
  function allowed() {
    $roles = explode(',', $_SESSION['user']->roles);

    if (in_array('admin', $roles)) {
      return TRUE;
    }
    else {
      $this->message('Sie dürfen den Verwaltungsbereich nicht nutzen', 'error');
      return FALSE;
    }
  }
  
  /**
   * Menü mit Admin-Optionen
   */
  function do_index() {
    $this->layout = 'admin';
    $this->vars['title'] = 'Verwaltungsbereich';
    $this->render();
  }
  
  /**
   * Wende Dateien aus dem Verzeichnis migrations/ an, 
   * bis die Datenbank auf dem aktuellen Stand ist.
   * Die Nummer der letzten Datei wird in Tabelle migrations gespeichert.
   */
  function do_migrate() {
    global $config;
    $this->layout = 'admin';
    $this->vars['title'] = 'Datenbank aktualisieren';
    
    $this->layout = 'admin';
    $migration = mysql_fetch_object(mysql_query('SELECT last_id FROM migrations'));

    echo "Aktualisiere die Datenbank seit Migration #{$migration->last_id} ...\n";
    
    $dir = realpath("{$_SERVER['DOCUMENT_ROOT']}/{$config['dir_migrations']}");
    $d = opendir($dir);

    $migrations = array();
    while ($entry = readdir($d)) {
      if (!preg_match('/^migration_(\d+)\.(sql|php)$/', $entry, $matches) || $matches[1] <= $migration->last_id) {
        continue;
      }
      $migrations[$matches[1]] = array($entry, $matches[2]);
    }
    ksort($migrations, SORT_NUMERIC);
    
    foreach ($migrations as $n => $entry) {
      echo $entry[0], ":\n";
      switch ($entry[1]) {
        case 'sql':
          // Benutze Kommandozeile, um mehrer SQL-Befehle ausführen zu können
          exec("{$config['mysql']} -h{$config['db_host']} -u{$config['db_user']} -p{$config['db_password']} {$config['db_name']} < {$dir}/{$entry[0]}", $out, $status);
          if ($status != 0) {
            echo "ERROR " . join("\n", $out). ". Migration abgebrochen\n";
            return;
          }
          break;

        case 'php':
          include $dir . $entry[0];
          break;

        default:
          echo " - WARNING: Typ {$entry[1]} nicht unterstützt\n";
      }
      mysql_query("UPDATE migrations SET last_id = {$n}, updated = NOW()");
    }
    echo "Fertig.\n";
  }
  
  /**
   * EINMALIG: Verschiebe Bilder aus www/avatar und www/photos
   * nach www/img/avatar und www/img/photos
   */
  function do_move_images() {
    $this->layout = 'admin';
    $this->vars['title'] = 'Verschiebe Fotos in hierarchische Struktur';

    foreach (array('photos', 'avatars') as $type) {
      $dir = "{$_SERVER['DOCUMENT_ROOT']}/{$type}";
      $d = opendir($dir);
      while ($entry = readdir($d)) {
        $src = "{$dir}/{$entry}";
        if (strpos($entry, '0') !== 0) {
          continue;
        }
        $target = $_SERVER['DOCUMENT_ROOT'] . $this->image_path(intval($entry), $type);
        @mkdir($target, 0775, /*recursive*/TRUE);
        
        echo "mv {$src} {$target}/{$entry}\n";
        rename($src, "{$target}/{$entry}");
      }
      closedir($d);
    }
  }
  
  function admin_paginate($model, $order = NULL) {
    $sql = "SELECT COUNT(*) FROM {$model}";
    $rs = mysql_query($sql);
    $counter = mysql_fetch_row($rs);

    $page = $this->paginate(ADMIN_PAGE_SIZE, $counter[0], '/admin/' . $model);

    $sql = "SELECT * FROM {$model} ";
    if ($order) {
      $sql .= "ORDER BY {$order} ";
    }
    $sql .= 'LIMIT ' . (($page-1)*ADMIN_PAGE_SIZE) . ',' . ADMIN_PAGE_SIZE;

    $this->vars[$model] = array();

    $rs = mysql_query($sql);
    while ($_ = mysql_fetch_object($rs)) {
      $this->vars[$model][] = $_;
    }
  }
  
  /**
   * Nutzerverwaltung, besonders aktivieren
   */
  function do_users() {
    $this->layout = 'admin';
    $this->vars['title'] = 'Verwalten von Nutzerkonten';

    // Kehre nach bearbeiten eines Nutzers hierher zurück
    $_SESSION['return_to'] = $_GET['url'];

    if (!empty($_POST)) {
      $sql = "UPDATE users SET"
        . " vorname = '" . mysql_real_escape_string($_POST['vorname']) . "',"
        . " nachname = '" . mysql_real_escape_string($_POST['nachname']) . "',"
        . " mail = '" . mysql_real_escape_string($_POST['email']) . "',"
        . " active = '" . mysql_real_escape_string($_POST['active']) . "',"
        . " updated = NOW()";
      
      if (!empty($_POST['password'])) {
        $sql .= ", password = MD5('" . mysql_real_escape_string($_POST['password']) . "'";
      }
      $sql .= " WHERE id = '" . mysql_real_escape_string($_POST['id']) . "'";
      
      if (mysql_query($sql)) {
        $this->message("Die Änderungen wurden gespeichert");
      }
      else {
        $this->message("Die Änderungen wurden nicht gespeichert: " . mysql_error());
      }
      $this->redirect();
    }
    else {
      $this->admin_paginate('users', 'nachname, vorname');
      $this->render();
    }
  }
  
  /**
   * Albenverwaltung, besonders Setzen des "shared"-Zustanded
   */
  function do_topics() {
    $this->layout = 'admin';
    $this->vars['title'] = 'Verwalten von Fotoalben';

    // Kehre nach bearbeiten eines Nutzers hierher zurück
    $_SESSION['return_to'] = $_GET['url'];

    if (!empty($_POST)) {
      $sql = "UPDATE topics SET"
        . " title = '" . mysql_real_escape_string($_POST['title']) . "',"
        . " shared = '" . mysql_real_escape_string($_POST['shared']) . "',"
        . " updated = NOW()"
        . " WHERE id = '" . mysql_real_escape_string($_POST['id']) . "'";
      
      if (mysql_query($sql)) {
        $this->message("Die Änderungen wurden gespeichert");
      }
      else {
        $this->message("Die Änderungen wurden nicht gespeichert: " . mysql_error());
      }
      $this->redirect();
    }
    else {
      $this->admin_paginate('topics', 'title');
      $this->render();
    }
  }
}
