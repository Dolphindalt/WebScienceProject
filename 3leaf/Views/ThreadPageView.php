<?php

use Dalton\Framework\View;

$board = $args['board'];
$thread = $args['thread'];
$posts = $args['posts'];

?>
<h1 class='center'><?php echo $board['directory']; ?> - <?php echo $board['name']; ?></h1>
<p class='info-text center'>thread no. <?php echo str_pad($thread['id'], 10, '0', STR_PAD_LEFT); ?></p>
<hr>
<div id='modal-show'><h2>[Post reply]</h2></div>
<hr>
<?php
    foreach ($posts as $post) {
        View::render('PostView.php', ['post' => $post, 'thread' => $thread]);
    }
?>
<?php require_once(ROOT_PATH.'3leaf/Views/CreatePostModal.php'); ?>