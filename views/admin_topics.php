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
    if (confirm('Verschieben nach "' + to + '"?')) {
      var ids = [];
      $('input.select:checked').each(function() {
        ids.push($(this).val());
      });

      $.post('/admin/movphotos', {
        ids: ids,
        topic: topic
      }, function() {
        location.reload();
      });
    }
    return false;
  });

});
</script>
<?php
$this->end_page_head();
?>
<form>
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
  <tr>
    <td colspan="5" class="photos">
      <?php
      foreach ($this->photos($topic->id) as $photo) {
        echo "<input class=\"select\" value=\"{$photo->id}\" type=\"checkbox\"><img class=\"photo\" src=\"/photo/scaled/{$photo->id}/60x60\">";
      }
      ?>
    </td>
  </tr>
  <?php
  }
  ?>
</table>
</form>
<?php $this->render('_paginate'); ?>

<p>Ausgewählte&hellip;</p>
<ul>
  <li>&hellip;<a href="#mov">verschieben</a> nach <input id="move-to"></li>
  <li>&hellip;<a href="#del">löschen</a></li>
</ul>

<form id="edit-topic" method="POST" action="/admin/topics">
  <input type="hidden" name="id" value="" id="edit-id">
  <label for="title">Titel</label><input class="input" type="text" name="title" value="" id="edit-title">
  <label for="shared">Gemeinsam</label>
  <select name="shared" id="edit-shared">
    <option value="1">ja</option>
    <option value="0">nein</option>
  </select>
</form>