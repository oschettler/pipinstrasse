<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

class home_controller extends controller {

  function do_index() {
    $this->vars['title'] = 'Pipinstraße';

    $sql = 'SELECT s.created as s_created, s.id AS s_id, s.*, u.* FROM stream s LEFT JOIN users u ON s.von = u.id WHERE '
      . " von != '" . mysql_real_escape_string($_SESSION['user']->id) . "' "
      . 'ORDER BY s.created DESC LIMIT 10';

    $this->vars['stream'] = array();

    $rs = mysql_query($sql); 
    while ($action = mysql_fetch_object($rs)) {
      
      $sql2 = "SELECT u.id, u.vorname, u.nachname, u.hausnummer FROM comments c LEFT JOIN users u ON c.von = u.id WHERE c.object_type = 'stream' AND c.object_id = {$action->s_id} LIMIT 3";
      
      $action->likes = array(); 
      $rs2 = mysql_query($sql2); 
      while ($like = mysql_fetch_object($rs2)) {
        $action->likes[] = $like;
      }
      
      $this->vars['stream'][] = $action;
    }

    $this->render();
  }
  
  function do_theme() {
    if (!empty($_POST)) {
      if ($_POST['theme'] == '---') {
        unset($_SESSION['theme']);
      }
      else {
        $_SESSION['theme'] = $_POST['theme'];
      }
    }
    $this->redirect();
  }
}

