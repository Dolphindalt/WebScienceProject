<?php

use Dalton\Framework\View;

$board = $args['board'];
$thread = $args['thread'];
$posts = $args['posts'];
if (array_key_exists('error', $args))
    $error = $args['error'];

?>
<h1 class='center'><?php echo $board['directory']; ?> - <?php echo $board['name']; ?></h1>
<p class='info-text center'>thread no. <?php $thread['id']; ?></p>
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
<?php
    $args = ['board' => $board];
    if (isset($error))
        $args['error'] = $error;
    View::render('CreatePostModal.php', $args); 
?>