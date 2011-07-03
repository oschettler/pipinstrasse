<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

class chat_controller extends controller {
  function do_index() {
    $this->layout = FALSE;
    $this->render();
  }
  
  function do_users() {
    header('Content-type: text/javascript');
    $this->layout = FALSE;

    $sql = "UPDATE users SET chat = NULL WHERE chat < DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
    mysql_query($sql);    
    
    $sql = "SELECT COUNT(*) FROM users WHERE chat IS NOT NULL";
    $user_count = mysql_fetch_row(mysql_query($sql));

    $data = new stdclass;
    $data->user_count = $user_count[0];
    
    echo json_encode($data);
  }
  
  function do_update() {
    header('Content-type: text/javascript');
    $this->layout = FALSE;

    if (count($this->path) != 3) {
      $since = 0;
    }
    else {
      $since = intval($this->path[2]);
    }
    
    $sql = "UPDATE users SET chat = NOW() WHERE id = {$_SESSION['user']->id}";
    mysql_query($sql);
    
    $data = new stdclass;
    
    /*
     * Nachrichten
     */
    $sql = "SELECT c.id AS c_id, c.message, DATE_FORMAT(c.created, '%H:%i:%S') AS created, u.id, u.hausnummer, u.vorname, u.nachname FROM chat c LEFT JOIN users u ON c.von = u.id ";
    if ($since == 0) {
      $sql .= "ORDER BY c.created DESC LIMIT 10";
    }
    else {
      $sql .= "WHERE c.id > {$since} ORDER BY c.created DESC LIMIT 10";
    }

    $data->messages = array();
    $rs = mysql_query($sql);
    while ($message = mysql_fetch_object($rs)) { 
      if ($message->id == $_SESSION['user']->id) {
        array_unshift($data->messages, '<a title="um ' . $message->created . '" p:id="' . $message->c_id . '"><strong>Ich</strong></a>: ' . $message->message);
      }
      else {
        array_unshift($data->messages, preg_replace('/<a/', '<a title="um ' . $message->created . '" p:id="' . $message->c_id . '"', $this->user_link($message)) . ': ' . $message->message);
      }
    }
    
    /*
     * Nutzer
     */
    $sql = "SELECT id, hausnummer, vorname, nachname FROM users WHERE chat IS NOT NULL AND  id != {$_SESSION['user']->id} ORDER BY hausnummer, nachname, vorname";

    $data->users = array();
    $rs = mysql_query($sql);
    while ($user = mysql_fetch_object($rs)) { 
      $data->users[] = $this->user_link($user);
    }
    
    echo json_encode($data);
  }
  
  function do_write() {
    $this->layout = FALSE;

    $sql = "DELETE FROM chat WHERE created < DATE_SUB(NOW(), INTERVAL 1 DAY)";
    mysql_query($sql);        
    
    if (!empty($_POST['input']) && trim($_POST['input'])) {
      $sql = 'INSERT INTO chat SET '
        . 'von = ' .  "'" . mysql_real_escape_string($_SESSION['user']->id) . "', "
        . 'message = ' .  "'" . mysql_real_escape_string(strip_tags(trim($_POST['input']))) . "', "
        . 'created = NOW()';
      mysql_query($sql);
    }
  }
}

