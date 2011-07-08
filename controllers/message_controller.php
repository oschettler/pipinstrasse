<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

define('MESSAGE_PAGE_SIZE', 10);

class message_controller extends controller {
  
  var $uses = array('message');
  
  /**
   * Action: Schreibe eine Nachricht
   */
  function do_write() {
    if (!empty($_POST)) {
      $sql = 'INSERT INTO messages SET '
        . 'von = ' .  "'" . mysql_real_escape_string($_SESSION['user']->id) . "', "
        . 'an = ' .  "'" . mysql_real_escape_string($_POST['an']) . "', "
        . 'gift = ' .  "'" . mysql_real_escape_string($_POST['gift']) . "', "
        . 'nachricht = ' .  "'" . mysql_real_escape_string($_POST['nachricht']) . "', "
        . 'created = NOW()';

      $result = mysql_query($sql);
      if (!$result) {
        $this->message(mysql_error(), 'error');
      }
      else {
        $this->message('Ihre Nachricht wurde gespeichert');

        $rs = mysql_query('SELECT mail FROM users WHERE id = '
          . "'" . mysql_real_escape_string($_POST['an']) . "'");
        $recipient = mysql_fetch_object($rs);

        $insert_id = $this->message->insert_id();

        $sender = $_SESSION['user'];

        mail($recipient->mail, "[pipinstrasse.de] Nachricht von {$sender->vorname} {$sender->nachname} aus Nummer {$sender->hausnummer}", 
          "Sie haben eine Nachricht erhalten: http://{$_SERVER['HTTP_HOST']}/message/view/{$insert_id}"
        );
      }
    }
    $this->redirect();
  }
  
  /**
   * Action: Lies eine Nachricht
   */
  function do_view() {
    if (count($this->path) != 3) {
      $this->message('FALSCHE URL');
      $this->redirect();
    }

    $sql = 'SELECT m.created as m_created, m.*, u.* 
    FROM messages m 
    LEFT JOIN users u ON m.von = u.id 
    WHERE m.id = '
      . "'" . mysql_real_escape_string($this->path[2]) . "'";

    $rs = mysql_query($sql);
    $this->vars['message'] = $message = mysql_fetch_object($rs);

    if ($message->an != $_SESSION['user']->id && $message->von != $_SESSION['user']->id) {
      $this->message('Diese Nachricht ist nicht an Sie gerichtet');
      $this->redirect();
    }
    
    $sql = 'UPDATE messages SET viewed = NOW() WHERE id = '
      . "'" . mysql_real_escape_string($this->path[2]) . "'";
    mysql_query($sql);

    $this->vars['title'] = "Nachricht von {$message->vorname} {$message->nachname} aus Nummer {$message->hausnummer}";
    
    $this->render();
  }
  
  function do_index() {
    $this->vars['title'] = "Ihre letzten Nachrichten";

    $sql = 'SELECT COUNT(*) FROM messages WHERE '
      . "an = '" . mysql_real_escape_string($_SESSION['user']->id) . "' "
      . "OR von = '" . mysql_real_escape_string($_SESSION['user']->id) . "'";
    $count = $this->message->count($sql);
    
    $page = $this->paginate(MESSAGE_PAGE_SIZE, $count, '/message/index');
        
    $sql = 'SELECT 
      m.created as m_created, 
      m.id AS m_id, 
      von.id AS von_id, 
      an.id AS an_id, 
      m.*, von.* 
    FROM messages m 
    LEFT JOIN users von ON m.von = von.id 
    LEFT JOIN users an ON m.an = an.id 
    WHERE '
      . "an = '" . mysql_real_escape_string($_SESSION['user']->id) . "' "
      . "OR von = '" . mysql_real_escape_string($_SESSION['user']->id) . "' "
      . 'ORDER BY m.created DESC LIMIT ' . (($page-1)*MESSAGE_PAGE_SIZE) . ',' . MESSAGE_PAGE_SIZE;

    $this->vars['messages'] = $this->message->query($sql);
    foreach ($this->vars['messages'] as $i => $message) {
      $sql = 'SELECT * 
      FROM users  
      WHERE '
        . "id = {$message->an_id}";
      
      $this->vars['messages'][$i]->an = $this->user->one($sql);
    }
    $this->render();
  }
  
  function do_contact() {
    global $config;
    
    $this->vars['title'] = "Ihre Nachricht an uns";

    $this->vars['captcha'] = $captcha = $this->captcha($_POST);

    if (!empty($_POST)) {
      $success = TRUE;
      if (empty($_POST['user']) || $_POST['user']->guest) {
        if ($captcha !== TRUE) {
          $this->message('Bitte lösen Sie die Rechenaufgabe', 'error');
          $success = FALSE;
        }
      }
      
      if ($success) {
        mail($config['admin_email'], "[pipinstrasse.de] Kontaktformular",
          "Browser: {$_SERVER['HTTP_USER_AGENT']}\n"
          . "Nachricht: {$_POST['nachricht']}\n"
        );
        $this->message('Ihre Nachricht wurde verschickt');
      }
    }

    $this->render();
  }
  
  function allowed() {
    if (empty($_SESSION['user']) || $_SESSION['user']->guest) {
      return $this->method == 'contact';
    }
    else {
      return TRUE;
    }
  }
}

