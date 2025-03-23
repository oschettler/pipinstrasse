<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

class topic_controller extends controller {
  
  function do_mine() {
    return $this->do_all(TRUE);
  }
  
  function do_all($mine = FALSE) {
    global $db;
    $this->layout = FALSE;
    $topics = array('Kein Album');

    $sql = 'SELECT * FROM topics ';
    if ($mine) {
      $sql .= " WHERE (shared = 1 OR von = {$_SESSION['user']->id}) ";
    } 

    if (!empty($_GET['term'])) {
      $sql .= "AND title LIKE '%" .  mysqli_real_escape_string($db, $_GET['term']) . "%' ";
    }
    
    $sql .= 'ORDER BY title LIMIT 10';

    $rs = mysqli_query($db, $sql);
    while ($topic = mysqli_fetch_object($rs)) {
      $topics[] = $topic->title;
    }
    echo $result = json_encode($topics);
  }
}
