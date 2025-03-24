<script src="<?php echo $config['static_url']; ?>/js/jquery-1.6.1.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $config['static_url']; ?>/jquery-ui/js/jquery-ui-1.8.14.custom.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
jQuery(function($) {
  // Verstecke Statusmeldungen nach 5s
  setTimeout(function() {
    $('.message').slideUp('fast');
  }, 5000);

  $('#switch-theme').hover(function() {
    $(this).css({ opacity: 1 });
  }, function() {
    $(this).css({ opacity: 0.1 });
  });

  // Änderen Theme
  $('#switch-theme select').change(function() {
    $(this).parent().submit();
  });
  
  // Ein/Ausblenden in Formularen
  $('.handle')
    .toggle(function() {
      $(this).siblings('.folded').slideDown('fast');
      return false;
    }, function() {
      $(this).siblings('.folded').slideUp('fast');
      return false;
    })
    .attr('title', 'Klicken zum Aufklappen');

  /*
   * Chat system
   */
<?php
if (!isset($config['chat_enabled']) || $config['chat_enabled']) {
  if (!empty($_SESSION['user']) && !$_SESSION['user']->guest) {
  ?>
    $('#chat #users a').live('click', function() {
      window.open('/chat', 'chat', 'width=400,height=600');
      return false;
    });

    var chat_first = true;
    var chat_enabled = true;
  
    // Update alle 5s
    setInterval(function() {
      if (chat_enabled) {
        $.getJSON('/chat/users', function(data) {
          if (chat_first) {
            $('#chat').slideDown('fast');
            first = false;
          }
          var txt = data.user_count == 1 ? '1 Nachbar' : (data.user_count + ' Nachbarn');
          $('#chat #users').html('<a href="#" title="Öffne Chat-Fenster">' + txt + ' im Chat</a>');
        });
      } // chat_enabled
    }, 5000);
  <?php
  }
} // Chat
?>
});
</script>
