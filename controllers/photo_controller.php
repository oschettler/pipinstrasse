<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

define('PHOTO_PAGE_SIZE', 30);

class photo_controller extends controller {
  var $uses = array('comment', 'topic');
  
  function before_layout() {
    
    /*
     * Block "Mehr aus Album"
     */
    if (in_array($this->method, array('view', 'full'))) {
      $photo = $this->vars['photo'];

      if ($photo->topic_id) {
        $sql = 'SELECT * FROM photos WHERE ' 
          . "topic_id = {$photo->topic_id} ";

        if ($this->method == 'view') {
          $sql .= "AND id != {$photo->p_id} ";
        }

        $sql .= 'ORDER BY seq ASC LIMIT 10';

        $photos = $this->photo->query($sql);
        foreach ($photos as $i => $_photo) {
          if ($_photo->id == $photo->p_id) {
            $photos[$i]->current = TRUE;
          }
          else {
            $photos[$i]->current = FALSE;
          }
        }
      }
      else {
        $photos = array();
      }

      if ($photos) {
        $this->vars['blocks']['above'][] = $this->block(array(
          'name' => 'album',
          'title' => 'Mehr ' . $photo->topic,
          'view' => '_topic'
        ), array(
          'photos' => $photos,
        ));
      }
    }
    else {
      if ($this->method != 'full' && !$_SESSION['user']->guest) {
        /*
         * Block "Neueste Alben"
         */ 
        $sql = "SELECT p.id as p_id, p.*, t.* FROM topics t "
          . 'LEFT JOIN photos p ON p.topic_id = t.id '
          . 'GROUP BY t.id '
          . 'ORDER BY t.created DESC LIMIT 10';

        $topics = $this->topic->query($sql);

        $this->vars['blocks']['above'][] = $this->block(array(
          'name' => 'topics',
          'title' => 'Neueste Alben',
          'view' => '_topics'
        ), array(
          'topics' => $topics,
        ));
      }
    }
    parent::before_layout();
  }
  
  function do_add($id = 0) {
    global $config;
    
    if (empty($_POST) || empty($_FILES)) {
      $this->redirect('/photo/index');
    }

    if (!$id && !$_FILES['bild']['name']) {
      $this->message('Bitte laden Sie ein Bild hoch');
      $this->redirect('/photo/index');
    }

    if (!empty($_POST['topic'])) {
      $topic = $this->topic->one("SELECT id, von, shared FROM topics WHERE "
        . "title = '" .  mysql_real_escape_string($_POST['topic']) . "'"
      );
      if ($topic) {
        if (!$topic->shared && $topic->von != $_SESSION['user']->id) {
          $this->message('Dieses Album gehört nicht Ihnen.');
          $this->redirect('/photo/index');
        }
        $topic_id = $topic->id;
      }
      else {
        $sql = "INSERT INTO topics SET "
          . "title = '" .  mysql_real_escape_string($_POST['topic']) . "', "
          . "von = {$_SESSION['user']->id}, "
          . "slug = '" .  mysql_real_escape_string($this->slug($_POST['topic'])) . "', "
          . 'created = NOW()';
        $this->topic->exec($sql);

        $topic_id = $this->topic->insert_id();
      }
    }
    else {
      $topic_id = 0;
    }

    if (empty($_POST['title'])) {
      if (empty($_FILES['bild']['name'])) {
        $title = '';
      }
      else {
        $title = $_FILES['bild']['name'];
      }
    }
    else {
      $title = $_FILES['bild']['name'];
    }

    $result_id = $this->photo->save(array(
      'id' => $id,
      'topic_id' => $topic_id,
      'title' => $title,
      'von' => $_SESSION['user']->id, 
      'upload' => $_FILES['bild']
    ));

    if ($result_id) {
      if ($id) {
        $this->message('Ihre Änderungen wurden gespeichert');
      }
      else {
        $this->message('Das Bild wurde gespeichert');
      }
      $this->log('photo', $result_id, $title);
    }
    else {
      $this->message('Das Bild konnte nicht gespeichert werden: ' . mysql_error(), 'error');
    }

    $this->redirect("/photo/view/{$result_id}");
  }
  
  /**
   * Action: Zeige das Album
   */
  function do_index() {
    $this->vars['title'] = "Fotos";
        
    $sql = 'SELECT COUNT(*) FROM photos';
    $count = $this->photo->count($sql);
    
    $page = $this->paginate(PHOTO_PAGE_SIZE, $count, '/photo/index');
        
    $sql = 'SELECT p.created as p_created, p.id AS p_id, u.id AS u_id, t.title AS topic, t.*, u.*, p.* '
      . 'FROM photos p ' 
      . 'LEFT JOIN users u ON p.von = u.id '
      . 'LEFT JOIN topics t ON p.topic_id = t.id '
      . 'ORDER BY p.created DESC LIMIT ' . (($page-1)*PHOTO_PAGE_SIZE) . ',' . PHOTO_PAGE_SIZE;

    $this->vars['photos'] = $this->photo->query($sql);
    
    $this->render();
  }
  
  /**
   * Betrachte ein skaliertes Bild. Lege Cache der skalierten Variante ab
   * Aufruf z.B. /photo/scaled/123/100x100. Default-Geometrie ist 1024x768
   */
  function do_scaled() {
    $this->layout = FALSE;

    $status = $this->show_scaled_image(IMAGE_RESOLUTION, 'photos');
    if (TRUE !== $status) {
      $this->message($status);
      $this->redirect('/photo/index');
    }
  }
  
  function do_edit() {
    if (count($this->path) != 3) {
      $this->message('FALSCHE URL');
      $this->redirect();
    }
    $photo_id = intval($this->path[2]);
    
    $sql = 'SELECT * FROM photos p WHERE '
      . "id = {$photo_id}";

    $photo = $this->photo->one($sql);
    
    if (!$photo) {
      $this->message("Dieses Foto gibt es nicht");
      $this->redirect('/photo/index');
    }

    if ($photo->von != $_SESSION['user']->id) {
      $this->message("Dieses Foto gehört Ihnen nicht");
      $this->redirect('/photo/index');
    }

    if (empty($_POST)) {
      $_POST = (array)$photo;
      $this->render();
    }
    else {
      return $this->do_add($photo_id);
    }
  }

  function do_view() {
    if (count($this->path) != 3) {
      $this->message('FALSCHE URL');
      $this->redirect('/photo/index');
    }
    
    $photo = $this->photo->load($this->path[2]);
    
    if (!$photo) {
      $this->message("Dieses Foto gibt es nicht");
      $this->redirect('/photo/index');
    }

    $this->vars['title'] = $photo->title;
    $this->vars['photo'] = $photo;
    $this->vars['comments'] = $comments = $this->comments('photo', $photo->p_id, "/photo/view/{$photo->id}");

    // Kehre nach Schreiben eines Kommentars hierher zurück
    $_SESSION['return_to'] = $_GET['url'];

    $this->render();
  }
  
  function do_full() {
    $this->layout = 'photo';
    return $this->do_view();
  }
  
  /**
   * Liefere das nächste Bild in der Diashow, nach dem Feld photos.seq
   * Beim letzten Bild wird das erste Bild im Album geliefert
   * Zusätzlich werden bis zu 5 Bilder vor und 5 Bilder nach dem Bild als Navigation geliefert
   * Aufruf per $.getJSON von /photo/full
   */
  function do_next() {
    if (count($this->path) != 3) {
      $this->message('FALSCHE URL');
      return;
    }
    
    $photo = $this->photo->load($this->path[2]);
    
    if (!$photo) {
      $this->message("Dieses Foto gibt es nicht");
      return;
    }

    header('Content-type: text/javascript');
    $this->layout = FALSE;
    
    $sql = "SELECT id, title, seq FROM photos WHERE topic_id = {$photo->topic_id} AND seq = " . ($photo->seq + 1);
    $next = $this->photo->one($sql);
    //krumo($photo, $sql, $next); exit;
    
    if (!$next) {
      $sql = "SELECT id, title, seq FROM photos WHERE topic_id = {$photo->topic_id} AND seq = 1";
      $next = $this->photo->one($sql);
    }
    
    $sql = "SELECT id, title FROM photos WHERE topic_id = {$photo->topic_id}"
      . " AND seq >= " . ($next->seq - 5)
      . " AND seq <= " . ($next->seq + 5)
      . ' ORDER BY seq ASC';
      
    $links = array();
    foreach ($this->photo->query($sql) as $link) {
      $class = $link->id == $next->id ? ' class="current"' : '';
      $links[] = "<li{$class}><a title=\"" . addslashes($link->title) . "\"href=\"/photo/full/{$link->id}\"><img class=\"photo\" src=\"/photo/scaled/{$link->id}/x50\"></a></li>";
    }

    $data = (object)array(
      'title' => $next->title,
      'next' => '/photo/scaled/' . $next->id,
      'links' => join('', $links)
    );

    echo json_encode($data);
  }
  
  /**
   * Checke, ob Gast auf erlaubte Inhalte zugreift
   */
  function allowed() {
    $this->model('invitation');
    
    if ($_SESSION['user']->guest) {
      $sql = "SELECT * FROM invitations WHERE id = {$_SESSION['user']->id}";
      $invite = $this->invitation->one($sql);
      if (!$invite) {
        $this->message('Ungültige Einladung');
        unset($_SESSION['user']);
        $this->redirect('/');
      }
      
      if ($_SESSION['user']->object_type == 'topic') {   
        if (count($this->path) < 3) {
          $this->message('FALSCHE URL');
          return FALSE;
        }
        $photo_id = $this->path[2];
      
        $sql = "SELECT * FROM photos WHERE id = {$photo_id} LIMIT 1";
        $photo = $this->photo->one($sql);
        if (!$photo) {
          $this->message('FALSCHE URL');
          return FALSE;
        }
      
        if ($photo->topic_id != $_SESSION['user']->object_id) {
          $this->message('Sie haben kein Zugriff auf diese Inhalte');
          return FALSE;
        }
        
        return TRUE;
      }
    }
    return parent::allowed();
  }
}
