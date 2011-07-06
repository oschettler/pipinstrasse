<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

define('LIST_PAGE_SIZE', 10);

class list_controller extends controller {
  var $uses = array('alist');

  /**
   * Action: Eigene Listen anschauen 
   */
  function do_mine() {
    $this->vars['title'] = "Schwarzes Brett";
    
    if (!empty($_POST) && !empty($_POST['list'])) {
      $unique = FALSE;
      while (!$unique) {
        $code = base_convert(rand(), 10, 36);

        $sql = "INSERT INTO lists SET "
          . " title = '" . mysql_real_escape_string($_POST['list']) . "', "
          . " slug = '" . mysql_real_escape_string($code) . "', "
          . " von = {$_SESSION['user']->id}, "
          . " shared = 0, "
          . " created = NOW()";

        $unique = mysql_query($sql);
      }

      $result = mysql_query($sql);
      if (!$result) {
        $this->message(mysql_error(), 'error');
      }
      else {
        $this->message('Ihre Liste wurde gespeichert');
        $this->log('alist', $this->alist->insert_id(), $_POST['list']);
      }
      $this->redirect('/list/mine');
    }

    $sql = 'SELECT COUNT(*) FROM lists';
    $count = $this->alist->count($sql);
    
    $page = $this->paginate(LIST_PAGE_SIZE, $count, '/list/mine');
        
    $sql = 'SELECT l.created as l_created, l.id AS l_id, l.*, u.* FROM alist l LEFT JOIN users u ON l.von = u.id '
      . 'ORDER BY l.created DESC LIMIT ' . (($page-1)*LIST_PAGE_SIZE) . ',' . LIST_PAGE_SIZE;

    $this->vars['lists'] = $this->alist->query($sql);
    $this->render();
  }  
}

