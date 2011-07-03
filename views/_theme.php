<form id="switch-theme" method="POST" action="/home/theme">
  <select name="theme">
    <?php
    foreach ($this->themes() as $_theme) { 
      $selected = $_theme == $theme ? ' selected="SELECTED"' : '';
      echo "<option{$selected}>{$_theme}</option>"; 
    } 
    ?>
  </select>
</form>
