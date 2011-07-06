<?php

class page_controller extends controller {
  var $uses = array('page');
  
  function do_view() {
    $this->vars['title'] = $this->context->title;
    $this->vars['body'] = $this->context->body;
    $this->render();
  }
  
  function allowed() {
    if (count($this->path) != 3) {
      $this->message('FALSCHE URL');
      return FALSE;
    }
    
    $sql = 'SELECT * FROM pages WHERE '
      . "slug = '" . mysql_real_escape_string($this->path[2]) . "'";
      
    $this->context = $this->page->one($sql);
    if (!$this->context) {
      $this->message('Seite nicht gefunden', 'error');
      return FALSE;
    }
    
    if (!$this->context->public && (empty($_SESSION['user']) || $_SESSION['user']->guest)) {
      $this->message('Sie dürfen auf diese Seite nicht zugreifen', 'error');
      return FALSE;
    }
    return TRUE;
  }
  
  function method() { 
    if (count($this->path) == 2) {
      $this->path = array('page', 'view', $this->path[1]);
      return 'view';
    }
    else {
      return parent::method();
    }
  }
}
