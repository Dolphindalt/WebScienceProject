<?php

use Dalton\Framework\View;

$board = $args['board'];
$thread = $args['thread'];
$posts = $args['posts'];
$post_ids = [];
foreach ($posts as $post) {
    array_push($post_ids, $post['id']);
}
if (array_key_exists('error', $args))
    $error = $args['error'];

?>
<h1 class='center'><?php echo $board['directory']; ?> - <?php echo $board['name']; ?></h1>
<p class='info-text center'>thread no. <?php echo $thread['id']; ?></p>
<?php
    if ($thread['is_archived']) {
        echo "<h4 class='center'>This thread is archived. You can no longer reply.</h4>";
    }
?>
<hr>
<div class='center'>
    <?php 
        if (!$thread['is_archived']) {
    ?>
            <div class='inline'><a class='post-thread-header-text' href='index.php?board/dir=<?php echo $board['directory']; ?>'><h2>[Catalog]</h2></a></div>
            <div id='modal-show' class='inline'><h2 class='post-thread-header-text'>[Reply]</h2></div>
    <?php
        } else {
    ?>
            <div class='inline'><a class='post-thread-header-text' href='index.php?board/archive/dir=<?php echo $board['directory']; ?>'><h2>[Archive]</h2></a></div>
    <?php
        }
    ?>
</div>
<hr>
<?php
    $op_post = array_shift($posts);
    View::render('PostView.php', ['post' => $op_post, 'thread' => $thread, 'post_ids' => $post_ids]);
    foreach ($posts as $post) {
        View::render('PostView.php', ['post' => $post, 'post_ids' => $post_ids]);
    }
?>
<?php
    $args = ['board' => $board];
    if (isset($error))
        $args['error'] = $error;
    View::render('CreatePostModal.php', $args); 
?>