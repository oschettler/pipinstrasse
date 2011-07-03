<?php
$this->page_head();
?>
<link rel="stylesheet" href="<?php echo $config['static_url']; ?>/jquery-ui/css/smoothness/jquery-ui-1.8.13.custom.css" type="text/css" media="screen" title="no title" charset="utf-8">
<script src="<?php echo $config['static_url']; ?>/jquery-ui/js/jquery-ui-1.8.13.custom.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
jQuery(function($) {
  $('#topic').autocomplete({
    minLength: 0,
    source: '/topic/mine'
  })
  .click(function() {
    $(this).autocomplete('search');
  });
});
</script>
<?php
$this->end_page_head();
?>
<form method="POST" action="/photo/<?php 
echo $this->method == 'edit' ? "edit/{$_POST['id']}" : 'add'; 
?>" enctype="multipart/form-data">
  <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />

  <h2<?php if ($this->method != 'edit') { echo ' class="handle"'; } ?>>Ein Bild <?php echo $this->method == 'edit' ? 'bearbeiten' : 'hochladen'; ?></h2>

  <div<?php if ($this->method != 'edit') { echo ' class="folded"'; } ?>>

    <label for="title">Titel</label><input class="input" type="text" name="title" value="<?php if (!empty($_POST['title'])) echo $_POST['title']; ?>" id="">

    <?php
    if (!empty($_POST['id'])) {
      echo "<img src=\"/photo/scaled/{$_POST['id']}/200x200\">";
    }
    ?>

    <label for="bild">Bild</label><input class="input" type="file" name="bild" value="" id="">
    
    <label for="topic">Album</label><input class="input" type="text" name="topic" value="" id="topic">

    <input type="submit" value="Speichern">

  </div>
</form>
