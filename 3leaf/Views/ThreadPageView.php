<?php

use Dalton\Framework\View;

$board = $args['board'];
$thread = $args['thread'];
$posts = $args['posts'];

?>
<h1 class='center'><?php echo $board['directory']; ?> - <?php echo $board['name']; ?></h1>
<p class='info-text center'>thread no. <?php echo str_pad($thread['id'], 10, '0', STR_PAD_LEFT); ?></p>
<hr>
<div class='center'>
    <div class='inline'><a class='post-thread-header-text' href='index.php?board/dir=<?php echo $board['directory']; ?>'><h2>[Catalog]</h2></a></div>
    <div id='modal-show' class='inline'><h2 class='post-thread-header-text'>[Reply]</h2></div>
</div>
<hr>
<?php
    foreach ($posts as $post) {
        View::render('PostView.php', ['post' => $post, 'thread' => $thread]);
    }
?>
<?php View::render('CreatePostModal.php', ['board' => $board]); ?>