<?php
$this->page_head();
?>
<style type="text/css" media="screen">
  #edit-topic {
    display: none;
    background-color: #EEE;
    padding: 10px;
  }
  
  label {
    float: left;
  }
  
  input.input, select {
    display: block;
    margin-left: 110px;
  }
</style>
<script type="text/javascript" charset="utf-8">
var topics = 
<?php 
$_topics = array();
foreach ($topics as $topic) {
  $_topics[$topic->id] = $topic;
}
echo json_encode($_topics);
?>;

jQuery(function($) {
  $('#edit-topic').dialog({
    autoOpen: false,
    width: 350,
    modal: true,
    buttons: {
      'Speichern': function() {
        $('#edit-topic').submit();
      }
    },
    close: function() {
      location.reload();
    }
  });

  $('.edit').click(function() { 
    var t = topics[$(this).attr('href')];
    $('#edit-topic #edit-id').val(t.id);
    $('#edit-topic #edit-title').val(t.title);
    $('#edit-topic #edit-shared').val(t.shared);
    $('#edit-topic')
      .dialog('option', 'title', '#' + t.id + ' ' + t.title)
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
    <th>gemeinsam</th>
    <th>seit</th>
    <th>geändert</th>
  </tr>
  <?php
  foreach ($topics as $i => $topic) {
  ?>
  <tr>
    <td><?php echo "<a class=\"edit\" href=\"{$topic->id}\">{$topic->id}</a>"; ?></td>
    <td><?php echo $topic->title; ?></td>
    <td><?php echo $topic->shared; ?></td>
    <td><?php echo $topic->created; ?></td>
    <td><?php if ($topic->updated != '0000-00-00 00:00:00') { echo $topic->updated; } ?></td>
  </tr>
  <?php
  }
  ?>
</table>
<?php $this->render('_paginate'); ?>
<form id="edit-topic" method="POST" action="/admin/topics">
  <input type="hidden" name="id" value="" id="edit-id">
  <label for="title">Titel</label><input class="input" type="text" name="title" value="" id="edit-title">
  <label for="shared">Gemeinsam</label>
  <select name="shared" id="edit-shared">
    <option value="1">ja</option>
    <option value="0">nein</option>
  </select>
</form>