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
  
  .photo {
    margin-right: 4px;
  }
  
  td.photos {
    position: relative;
  }
  
  input.select {
    position: absolute;
    z-index: 2;
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
  
  $('#move-to').autocomplete({
    minLength: 2,
    source: '/topic/mine'
  });
  
  $('a[href=#del]').click(function() {
    alert("DEL");
    return false;
  });

  $('a[href=#mov]').click(function() {
    var topic = $('#move-to').val();
    if (confirm('Verschieben nach "' + topic + '"?')) {
      $('#movphotos').submit();
    }
    return false;
  });
  
  $('a.confirm').click(function() {
    if (confirm('Wirklich ' + $(this).attr('title') + '?')) {
      return true;
    }
    else {
      return false;
    }
  });
});
</script>
<?php
$this->end_page_head();
?>
<form id="movphotos" action="/admin/movphotos" method="POST">
<table>
  <tr>
    <th>ID</th>
    <th>Titel</th>
    <th>gemeinsam</th>
    <th>seit</th>
    <th>geändert</th>
    <th></th>
  </tr>
  <?php
  $no_topic = (object)array(
    'id' => NULL,
    'title' => '<em>Kein Album</em>',
    'shared' => '',
    'created' => '',
    'updated' => '',
  );
  array_unshift($topics, $no_topic);
  foreach ($topics as $i => $topic) {
  ?>
  <tr>
    <td><?php if ($topic->id) { echo "<a class=\"edit\" href=\"{$topic->id}\">{$topic->id}</a>"; } ?></td>
    <td><?php echo $topic->title; ?></td>
    <td><?php echo $topic->shared; ?></td>
    <td><?php echo $topic->created; ?></td>
    <td><?php if ($topic->updated != '0000-00-00 00:00:00') { echo $topic->updated; } ?></td>
    <td>
      <?php 
      if ($topic->id) { 
        echo "<a href=\"/admin/topicseq/{$topic->id}\">Reihenfolge reparieren</a>"; 
        echo " | <a class=\"confirm\" title=\"alle Fotos im import-Verzeichnis importieren\" href=\"/admin/topicimport/{$topic->id}\">Massenimport</a>"; 
      } 
      ?>
    </td>
  </tr>
  <tr>
    <td colspan="5" class="photos">
      <?php
      foreach ($this->photos($topic->id) as $photo) {
        echo "<input class=\"select\" name=\"ids[]\" value=\"{$photo->id}\" type=\"checkbox\"><img class=\"photo\" src=\"/photo/scaled/{$photo->id}/60x60\">";
      }
      ?>
    </td>
  </tr>
  <?php
  }
  ?>
</table>
<?php $this->render('_paginate'); ?>

<p>Ausgewählte&hellip;</p>
<ul>
  <li>&hellip;nach <input id="move-to" name="topic"> <a href="#mov">verschieben</a></li>
  <!--li>&hellip;<a href="#del">löschen</a></li-->
</ul>
</form>

<form id="edit-topic" method="POST" action="/admin/topics">
  <input type="hidden" name="id" value="" id="edit-id">
  <label for="title">Titel</label><input class="input" type="text" name="title" value="" id="edit-title">
  <label for="shared">Gemeinsam</label>
  <select name="shared" id="edit-shared">
    <option value="1">ja</option>
    <option value="0">nein</option>
  </select>
</form>