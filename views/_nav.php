<ul>
  <?php
  foreach ($navigation as $_url => $_title) {
    $_confirm = '';
    
    if (is_array($_title)) {
      if (!empty($_title['confirm'])) {
        $_confirm = $_title['confirm'] == TRUE
          ? " class=\"confirm\" title=\"Wirklich {$_title[0]}?\""
          : " class=\"confirm\" title=\"{$_title['confirm']}\"";
      }
      $_title = $_title[0];
    }

    list(,$_name) = explode('/', $_url);
    if ($this->name == $_name || $_name == '' && $this->name == 'home') {
      $_class = ' class="current_page_item"';
    }
    else {
      $_class = '';
    }
    ?><li<?php echo $_class; ?>><a<?php echo $_confirm; ?> href="<?php echo $_url; ?>"><?php echo $_title; ?></a></li><?php
  }
  ?>
</ul> 
