<?php
$this->page_head();
?>
<script src="/js/picup.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
jQuery(function($) {
  $('#topic').autocomplete({
    minLength: 0,
    source: '/topic/mine'
  })
  .click(function() {
    $(this).autocomplete('search');
  });
  
  Picup.callbackHandler = function(params) {
    for (var key in params) {
      alert(key+' == '+params[key]);
    }
  }
  // Für Picup
  window.name = 'photo_upload';
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
