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
  function do_add() {
    if (!empty($_POST)) {
      $sql = 'INSERT INTO comments SET '
        . 'von = ' .  "'" . mysql_real_escape_string($_SESSION['user']->id) . "', "
        . 'object_type = ' .  "'" . mysql_real_escape_string($_POST['type']) . "', "
        . 'object_id = ' .  "'" . mysql_real_escape_string($_POST['id']) . "', "
        . 'comment = ' .  "'" . mysql_real_escape_string($_POST['comment']) . "', "
        . 'liked = ' .  "'" . mysql_real_escape_string($_POST['like']) . "', "
        . 'created = NOW()';

      $result = mysql_query($sql);
      if (!$result) {
        $this->message(mysql_error(), 'error');
      }
      else {
        $this->message('Ihr Kommentar wurde gespeichert');

        $rs = mysql_query('SELECT mail FROM users WHERE id = '
          . "'" . mysql_real_escape_string($_POST['an']) . "'");
        $recipient = mysql_fetch_object($rs);

        $this->log('comment', $this->insert_id(), $_POST['comment']);

        $sender = $_SESSION['user'];

        mail($recipient->mail, "[pipinstrasse.de] Kommentar von {$sender->vorname} {$sender->nachname} aus Nummer {$sender->hausnummer}", 
          "Sie haben einen Kommentar erhalten: http://{$_SERVER['HTTP_HOST']}{$_POST['url']}"
        );
      }
    }
    $this->redirect();
  }
  
  function do_like() {
    if (!empty($_POST)) {
      $sql = 'INSERT INTO comments SET '
        . 'von = ' .  "'" . mysql_real_escape_string($_SESSION['user']->id) . "', "
        . 'object_type = ' .  "'" . mysql_real_escape_string($_POST['type']) . "', "
        . 'object_id = ' .  "'" . mysql_real_escape_string($_POST['id']) . "', "
        . 'liked = 1, '
        . 'created = NOW()';

      $result = mysql_query($sql);

      $this->log('like', $this->insert_id());
    }
  }
}