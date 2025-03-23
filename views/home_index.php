<?php
global $db;
$this->page_head();
?>
<script type="text/javascript" charset="utf-8">
jQuery(function($) {
  $('a.thumb').click(function() {
    var $img = $('img', this);
    $.ajax({
      url: '/comment/like',
      type: 'POST',
      data: {
        type: $(this).attr('p:type'),
        id: $(this).attr('p:id')
      },
      success: function() {
        $img.slideUp('fast');
      }
    });
    return false;
  });
});
</script>
<?php
$this->end_page_head();
?>
<form action="/user/status" method="POST" accept-charset="utf-8">
  <label for="status">Was machen Sie gerade?</label>
  <textarea name="status" rows="3" cols="40"></textarea>
  <input type="submit" value="Speichern">
</form>

<ul id="actions">
  <?php
  $thumb = '<img src="/img/thumb.png">';
  
  foreach ($stream as $action) {
    // Naive Sperre gegen Cheater
    $i_like = FALSE;
    
    $likes = array();
    foreach ($action->likes as $like) {
      if ($_SESSION['user']->id == $like->id) {
        $i_like = TRUE;
        $likes[$like->id] = 'Ihnen';
      }
      else {
        $likes[$like->id] = $this->user_link($like, /*url_only*/FALSE, /*image*/FALSE);
      }
    }
    if ($likes) {
      $likes = ' ' . join(', ', $likes) . ' gefällt das.';
    }
    else {
      $likes = '';
    }

    switch ($action->object_type) {
      case 'photo':
        echo '<li>';
        if (!$i_like) echo "<a class=\"thumb\" p:type=\"stream\" p:id=\"{$action->s_id}\" href=\"#\">", $thumb, '</a>';
        echo '<img class="photo" title="', addslashes($action->title), '" src="/photo/scaled/', $action->object_id, '/100x100" />';
        echo $this->user_link($action), ' hat ', $this->reltime($action->s_created), " ein <a href=\"/photo/view/{$action->object_id}\">Foto</a> hochgeladen.{$likes}</li>";
        break;

      case 'board':
        echo '<li>'; 
        if (!$i_like) echo "<a class=\"thumb\" p:type=\"stream\" p:id=\"{$action->s_id}\" href=\"#\">", $thumb, '</a>';
        echo $this->user_link($action), ' hat ', $this->reltime($action->s_created), " aufs <a href=\"/board/index\">Schwarze Brett</a> geschrieben: <span>{$action->title}</span>{$likes}</li>";
        break;

      case 'status':
        echo '<li>';
        if (!$i_like) echo "<a class=\"thumb\" p:type=\"stream\" p:id=\"{$action->s_id}\" href=\"#\">", $thumb, '</a>';
        echo $this->user_link($action), ' ', $this->reltime($action->s_created), ": <span>{$action->title}.</span>{$likes}</li>";
        break;

      case 'comment':
        $sql = "SELECT * FROM comments WHERE id = {$action->object_id}";
        $rs = mysqli_query($db, $sql);
        $comment = mysqli_fetch_object($rs);

        switch ($comment->object_type) {
          case 'photo':
            echo '<li>';
            if (!$i_like) echo "<a class=\"thumb\" p:type=\"stream\" p:id=\"{$action->s_id}\" href=\"#\">", $thumb, '</a>';
            echo '<img class="photo" src="/photo/scaled/', $comment->object_id, '/100x100" />';
            echo $this->user_link($action), ' hat ', $this->reltime($action->s_created), " ein <a href=\"/photo/view/{$comment->object_id}\">Foto</a> kommentiert.{$likes}</li>";
            break;
        }
    }
  }
  ?>
</ul>