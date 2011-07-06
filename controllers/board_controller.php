<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

define('BOARD_PAGE_SIZE', 10);

class board_controller extends controller {
  var $uses = array('board');

  /**
   * Action: Das schwarze Brett anschauen
   */
  function do_index() {
    $this->vars['title'] = "Schwarzes Brett";
    
    if (!empty($_POST) && !empty($_POST['nachricht'])) {
      $sql = 'INSERT INTO board SET '
        . 'von = ' .  "'" . mysql_real_escape_string($_SESSION['user']->id) . "', "
        . 'nachricht = ' .  "'" . mysql_real_escape_string($_POST['nachricht']) . "', "
        . 'created = NOW()';

      $result = mysql_query($sql);
      if (!$result) {
        $this->message(mysql_error(), 'error');
      }
      else {
        $this->message('Ihre Nachricht wurde gespeichert');
        $this->log('board', $this->message->insert_id(), $_POST['nachricht']);
      }
      $this->redirect('/board/index');
    }

    $sql = 'SELECT COUNT(*) FROM board';
    $count = $this->board->count($sql);
    
    $page = $this->paginate(BOARD_PAGE_SIZE, $count, '/board/index');
        
    $sql = 'SELECT m.created as m_created, m.id AS m_id, m.*, u.* FROM board m LEFT JOIN users u ON m.von = u.id '
      . 'ORDER BY m.created DESC LIMIT ' . (($page-1)*BOARD_PAGE_SIZE) . ',' . BOARD_PAGE_SIZE;

    $this->vars['messages'] = $this->board->query($sql);
    $this->render();
  }  
}

