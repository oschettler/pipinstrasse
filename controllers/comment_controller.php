<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

class comment_controller extends controller {
  
  var $uses = array('comment');
  
  function update_comment_count($type, $id) {
    $type = preg_replace('/\W+/', '', $type);
    $id = intval($id);
    
    $sql = "SELECT COUNT(*) AS n FROM comments WHERE "
      . "object_type = '{$type}' AND "
      . "object_id = {$id}";

    $count = $this->comment->one($sql);
    return $this->$type->update_comment_count($id, $count->n);
  }
  
  function do_add() {
    global $db;
    if (!empty($_POST)) {
      $like = empty($_POST['like']) || !$_POST['like'] ? 0 : 1;
      
      $sql = 'INSERT INTO comments SET '
        . 'von = ' .  "'" . mysqli_real_escape_string($db, $_SESSION['user']->id) . "', "
        . 'object_type = ' .  "'" . mysqli_real_escape_string($db, $_POST['type']) . "', "
        . 'object_id = ' .  "'" . mysqli_real_escape_string($db, $_POST['id']) . "', "
        . 'comment = ' .  "'" . mysqli_real_escape_string($db, $_POST['comment']) . "', "
        . "liked = {$like}, "
        . 'created = NOW()';

      $result = mysqli_query($db, $sql);
      if (!$result) {
        $this->message(mysqli_error($db), 'error');
      }
      else {
        if (!$this->update_comment_count($_POST['type'], $_POST['id'])) {
          $this->message(mysqli_error($db));
        } 
        else {
          $this->message('Ihr Kommentar wurde gespeichert');
        }

        $rs = mysqli_query($db, 'SELECT mail FROM users WHERE id = '
          . "'" . mysqli_real_escape_string($db, $_POST['an']) . "'");
        $recipient = mysqli_fetch_object($rs);

        $this->log('comment', $this->comment->insert_id(), $_POST['comment']);

        $sender = $_SESSION['user'];

        mail($recipient->mail, "[pipinstrasse.de] Kommentar von {$sender->vorname} {$sender->nachname} aus Nummer {$sender->hausnummer}", 
          "Sie haben einen Kommentar erhalten: http://{$_SERVER['HTTP_HOST']}{$_POST['url']}"
        );
      }
    }
    $this->redirect();
  }
  
  function do_like() {
    global $db;
    if (!empty($_POST)) {
      $sql = 'INSERT INTO comments SET '
        . 'von = ' .  "'" . mysqli_real_escape_string($db, $_SESSION['user']->id) . "', "
        . 'object_type = ' .  "'" . mysqli_real_escape_string($db, $_POST['type']) . "', "
        . 'object_id = ' .  "'" . mysqli_real_escape_string($db, $_POST['id']) . "', "
        . 'liked = 1, '
        . 'created = NOW()';

      $result = mysqli_query($db, $sql);

      $this->log('like', $this->comment->insert_id());
    }
  }
}
