<?php
$this->page_head();
?>
<script type="text/javascript" src="<?php echo $config['static_url']; ?>/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $config['static_url']; ?>/ckeditor/adapters/jquery.js"></script>
<style type="text/css" media="screen">
  #edit-user {
    display: none;
    background-color: #EEE;
    padding: 10px;
  }
  
  label {
    float: left;
  }
  
  input.input, select {
    display: block;
    margin-left: 100px;
  }
</style>
<script type="text/javascript" charset="utf-8">
var pages = 
<?php 
$_pages = array();
foreach ($pages as $page) {
  $_pages[$page->id] = $page;
}
echo json_encode($_pages);
?>;

jQuery(function($) {
  $('#edit-page').dialog({
    autoOpen: false,
    width: 600,
    modal: false, // Funktioniert mit WYSIWYG-Editor nicht
    buttons: {
      'Speichern': function() {
        $('#edit-page').submit();
      }
    },
    close: function() {
      location.reload();
    }
  });

  $('#edit-body').ckeditor(function() {}, {
    toolbar: [
      ['Cut','Copy','Paste','PasteText'],
      ['Image','Table'],
      ['Format'],
      ['Bold','Italic'],
      ['NumberedList','BulletedList','-','Outdent','Indent'],
      ['Link','Unlink'],
    ],
    height: '120px'
  });
  
  $('.edit').click(function() { 
    var p = pages[$(this).attr('href')];
    $('#edit-page #edit-id').val(p.id);
    $('#edit-page #edit-title').val(p.title);
    $('#edit-page #edit-slug').val(p.slug);
    $('#edit-page #edit-body').val(p.body);
    $('#edit-page #edit-public').val(p.public);
    $('#edit-page')
      .dialog('option', 'title', '#' + p.id + ' ' + p.title)
      .dialog('open');
    return false;
  });

  $('a[href=#add]').click(function() {
    $('#edit-page #edit-id').val(0);
    $('#edit-page')
      .dialog('option', 'title', 'Neue Seite anlegen')
      .dialog('open');
    return false;
  });
});
</script>
<?php
$this->end_page_head();
?>
<table>
  <tr>
    <th>ID</th>
    <th>Titel</th>
    <th>URL</th>
    <th>öffentlich</th>
    <th>seit</th>
    <th>geändert</th>
  </tr>
  <?php
  foreach ($pages as $i => $page) {
  ?>
  <tr>
    <td><?php echo "<a class=\"edit\" href=\"{$page->id}\">{$page->id}</a>"; ?></td>
    <td><?php echo "<a target=\"_blank\" href=\"/page/{$page->slug}\">{$page->title}</a>"; ?></td>
    <td><?php echo "/page/{$page->slug}"; ?></td>
    <td><?php echo $page->public; ?></td>
    <td><?php echo $page->created; ?></td>
    <td><?php if ($page->updated != '0000-00-00 00:00:00') { echo $page->updated; } ?></td>
  </tr>
  <tr>
    <td colspan="5"><?php echo $page->body; ?></td>
  </tr>
  <?php
  }
  ?>
</table>
<?php $this->render('_paginate'); ?>

<ul>
  <li><a href="#add">Neue Seite anlegen</a></li>
</ul>


<form id="edit-page" method="POST" action="/admin/pages">
  <input type="hidden" name="id" value="" id="edit-id">
  <label for="title">Titel</label><input class="input" type="text" name="title" value="" id="edit-title">
  <label for="slug">URL</label><input class="input" type="text" name="slug" value="" id="edit-slug">
  <label for="public">Öffentlich</label>
  <select name="public" id="edit-public">
    <option value="1">ja</option>
    <option value="0">nein</option>
  </select>
  <textarea rows="10" name="body" id="edit-body"></textarea>
</form>
