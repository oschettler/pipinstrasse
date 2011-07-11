<?php
$this->page_head();
?>
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
var users = 
<?php 
$_users = array();
foreach ($users as $user) {
  $_users[$user->id] = $user;
}
echo json_encode($_users);
?>;

jQuery(function($) {
  $('#edit-user').dialog({
    autoOpen: false,
    width: 350,
    modal: true,
    buttons: {
      'Speichern': function() {
        $('#edit-user').submit();
      }
    },
    close: function() {
      location.reload();
    }
  });

  $('.edit').click(function() { 
    var u = users[$(this).attr('href')];
    $('#edit-user #edit-id').val(u.id);
    $('#edit-user #edit-hausnummer').val(u.hausnummer);
    $('#edit-user #edit-vorname').val(u.vorname);
    $('#edit-user #edit-nachname').val(u.nachname);
    $('#edit-user #edit-email').val(u.mail);
    $('#edit-user #edit-active').val(u.active);
    $('#edit-user')
      .dialog('option', 'title', '#' + u.id + ' ' + u.vorname + ' ' + u.nachname)
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
    <th>Hausnummer</th>
    <th>Nachname</th>
    <th>Vorname</th>
    <th>E-Mail</th>
    <th>aktiv</th>
    <th>seit</th>
    <th>geändert</th>
    <th>eingeladen</th>
  </tr>
  <?php
  foreach ($users as $i => $user) {
  ?>
  <tr>
    <td><?php echo "<a class=\"edit\" href=\"{$user->id}\">{$user->id}</a>"; ?></td>
    <td><?php echo $user->hausnummer; ?></td>
    <td><?php echo $user->nachname; ?></td>
    <td><?php echo $user->vorname; ?></td>
    <td><?php echo $user->mail; ?></td>
    <td><?php echo $user->active; ?></td>
    <td><?php echo $user->created; ?></td>
    <td><?php if ($user->updated != '0000-00-00 00:00:00') { echo $user->updated; } ?></td>
    <td><?php if ($user->invited) { echo "{$user->invited} von {$user->invited_by}"; } ?></td>
  </tr>
  <?php
  }
  ?>
</table>
<?php $this->render('_paginate'); ?>
<form id="edit-user" method="POST" action="/admin/users">
  <input type="hidden" name="id" value="" id="edit-id">
  <label for="hausnummer">Hausnr.</label><input class="input" type="text" name="hausnummer" value="" id="edit-hausnummer">
  <label for="vorname">Vorname</label><input class="input" type="text" name="vorname" value="" id="edit-vorname">
  <label for="nachname">Nachname</label><input class="input" type="text" name="nachname" value="" id="edit-nachname">
  <label for="password">Kennwort</label><input class="input" type="password" name="password" value="" id="edit-password">
  <label for="email">E-Mail</label><input class="input" type="text" name="email" value="" id="edit-email">
  <label for="active">Aktiv</label>
  <select name="active" id="edit-active">
    <option value="1">ja</option>
    <option value="0">nein</option>
  </select>
</form>