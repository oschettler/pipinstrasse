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
    global $config, $db;
    $this->layout = 'admin';
    $this->vars['title'] = 'Datenbank aktualisieren';
    
    $this->layout = 'admin';
    $migration = mysqli_fetch_object(mysqli_query($db, 'SELECT last_id FROM migrations'));

    echo "Aktualisiere die Datenbank seit Migration #{$migration->last_id} ...<pre>\n";
    
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
          $cmd = "{$config['mysql']} -h{$config['db_host']} -u{$config['db_user']} -p{$config['db_password']} {$config['db_name']} 2>&1 < {$dir}/{$entry[0]}";
          $out = array();
          exec($cmd, $out, $status);
          if ($status != 0) {
            echo join("\n", $out) . "</pre>Migration abgebrochen\n";
            return;
          }
          break;

        case 'php':
          include $dir . $entry[0];
          break;

        default:
          echo " - WARNING: Typ {$entry[1]} nicht unterstützt\n";
      }
      mysqli_query($db, "UPDATE migrations SET last_id = {$n}, updated = NOW()");
    }
    echo "</pre>Fertig.\n";
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
    global $db;
    $sql = "SELECT COUNT(*) FROM {$model}";
    $rs = mysqli_query($db, $sql);
    $counter = mysqli_fetch_row($rs);

    $page = $this->paginate(ADMIN_PAGE_SIZE, $counter[0], '/admin/' . $model);

    $sql = "SELECT * FROM {$model} ";
    if ($order) {
      $sql .= "ORDER BY {$order} ";
    }
    $sql .= 'LIMIT ' . (($page-1)*ADMIN_PAGE_SIZE) . ',' . ADMIN_PAGE_SIZE;

    $this->vars[$model] = array();

    $rs = mysqli_query($db, $sql);
    while ($_ = mysqli_fetch_object($rs)) {
      $this->vars[$model][] = $_;
    }
  }
  
  /**
   * Nutzerverwaltung, besonders aktivieren
   */
  function do_users() {
    global $db;
    $this->layout = 'admin';
    $this->vars['title'] = 'Verwalten von Nutzerkonten';

    // Kehre nach Bearbeiten eines Nutzers hierher zurück
    $_SESSION['return_to'] = $_GET['url'];

    if (!empty($_POST)) {
      $this->model('user');
      
      $validation_messages = $this->user->validate($_POST);

      if ($validation_messages) {
        $this->message(join('<br>', $validation_messages), 'error');
        $this->redirect();
      }
      else {
        $result = $this->user->save($_POST);
        if ($result) {
          $this->message("Die Änderungen wurden gespeichert");
        }
        else {
          $this->message("Die Änderungen wurden nicht gespeichert: " . mysqli_error($db));
        }
        $this->redirect();
      }
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
    global $db;
    $this->layout = 'admin';
    $this->vars['title'] = 'Verwalten von Fotoalben';

    // Kehre nach bearbeiten eines Nutzers hierher zurück
    $_SESSION['return_to'] = $_GET['url'];

    if (!empty($_POST)) {
      $sql = "UPDATE topics SET"
        . " title = '" . mysqli_real_escape_string($db, $_POST['title']) . "',"
        . " shared = '" . mysqli_real_escape_string($db, $_POST['shared']) . "',"
        . " updated = NOW()"
        . " WHERE id = '" . mysqli_real_escape_string($db, $_POST['id']) . "'";
      
      if (mysqli_query($db, $sql)) {
        $this->message("Die Änderungen wurden gespeichert");
      }
      else {
        $this->message("Die Änderungen wurden nicht gespeichert: " . mysqli_error($db));
      }
      $this->redirect();
    }
    else {
      $this->admin_paginate('topics', 'title');
      $this->render();
    }
  }
  
  function photos($topic_id = NULL) {
    $this->model('photo');

    $sql = 'SELECT * FROM photos WHERE '; 
    if ($topic_id === NULL) {
      $sql .= "topic_id IS NULL ";
    }
    else {
      $sql .= "topic_id = {$topic_id} ";
    }
    $sql .= 'ORDER BY created DESC';

    return $this->photo->query($sql);
  }
  
  function do_movphotos() {
    global $db;
    $this->layout = FALSE;

    $this->model('topic');
    $this->model('photo');

    if (empty($_POST['ids'])) {
      $this->message('Wählen Sie erst Bilder aus', 'error');
    }
    
    $sql = "UPDATE photos SET ";
    
    if ($_POST['topic'] == 'Kein Album') {
      $sql .= 'topic_id = NULL ';
    }
    else {
      $topic_id = $this->topic->save($_POST);
      $sql .= "topic_id = {$topic_id}";
    }
    $sql .= ", updated = NOW() WHERE id IN (" 
      . preg_replace('/[^,\d]/', '', join(',', $_POST['ids'])) . ')';
    
    if ($this->photo->exec($sql)) {
      $this->message('Die Änderungen wurden gespeichert');
    }
    else {
      $this->message('Die Änderungen konnten nicht gespeichert werden: ' . mysqli_error($db), 'error');
    }
    $this->redirect();
  }
  
  function do_topicseq() {
    $topic_id = $this->path[2];
    
    $this->model('photo');

    $sql = "SELECT id FROM photos WHERE topic_id={$topic_id} ORDER BY seq, updated ASC";
    foreach ($this->photo->query($sql) as $i => $photo) {
      // Anfang bei 1
      $this->photo->exec('UPDATE photos SET seq = ' . ($i + 1) . ", updated = NOW() WHERE id={$photo->id}");
    }
    $this->message("Reihenfolge repariert");
    $this->redirect('/admin/topics');
  }
  
  /**
   * Importiere Fotos aus dem Verzeichnis 'import' in ein Album
   */  
  function do_topicimport() {
    $topic_id = $this->path[2];
        
    $this->model('photo');

    $dir = realpath($_SERVER['DOCUMENT_ROOT'] . '/../import');
    $d = opendir($dir);
    $n = 0;
    
    $msg = array();
    while ($entry = readdir($d)) {
      if (strpos($entry, '.') === 0) {
        continue;
      }
      $path = "{$dir}/{$entry}";
      if (is_dir($path)) {
        continue;
      }

      $n++;

      $id = $this->photo->save(array(
        'topic_id' => $topic_id,
        'title' => $entry,
        'von' => $_SESSION['user']->id, 
        'batch' => TRUE,
        'upload' => array(
          'name' => $entry,
          'tmp_name' => $path,
        ),
      ));
      if ($id) {
        $msg[] = "<a href=\"/photo/view/{$id}\">{$entry}</a>";
      }
      else {
        $msg[] = "Fehler bei {$entry}";
      }
    }
    closedir($d);
    
    $this->message("{$n} Einträge:<br>" . join('<br>', $msg));
    $this->redirect('/admin/topics');
  }
  
  function do_pages() {
    global $db;
    $this->layout = 'admin';
    $this->vars['title'] = 'Verwalten von Seiten';

    // Kehre nach bearbeiten einer Seite hierher zurück
    $_SESSION['return_to'] = $_GET['url'];

    if (!empty($_POST)) {
      if ($_POST['id']) {
        $sql = "UPDATE pages SET";
      }
      else {
        $sql = "INSERT INTO pages SET";
      }
      
      $slug = empty($_POST['slug'])
        ? model::slug($_POST['title'])
        : model::slug($_POST['slug']);
      
      $sql .= " title = '" . mysqli_real_escape_string($db, $_POST['title']) . "',"
        . " slug = '" . mysqli_real_escape_string($db, $slug) . "',"
        . " public = '" . mysqli_real_escape_string($db, $_POST['public']) . "',"
        . " body = '" . mysqli_real_escape_string($db, $_POST['body']) . "',";
      
      if (empty($_POST['id'])) {
        $sql .= " created = NOW(), updated = NOW()";
      }
      else {
        $sql .= " updated = NOW() WHERE id = '" . mysqli_real_escape_string($db, $_POST['id']) . "'";
      }
      
      if (mysqli_query($db, $sql)) {
        $this->message("Die Änderungen wurden gespeichert");
      }
      else {
        $this->message("Die Änderungen wurden nicht gespeichert: " . mysqli_error($db));
      }
      $this->redirect();
    }
    else {
      $this->admin_paginate('pages', 'title');
      $this->render();
    }
  }
}
