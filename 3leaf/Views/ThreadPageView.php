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
            <div class='inline'><h2><a class='post-thread-header-text' href='index.php?board/dir=<?php echo $board['directory']; ?>'>[Catalog]</a></h2></div>
            <?php
                if (isset($_SESSION) && array_key_exists(LOGGED_IN, $_SESSION) && $_SESSION[LOGGED_IN]) {
                    echo "<div id='modal-show' class='inline'><h2 class='post-thread-header-text'>[Reply]</h2></div>";
                } else {
                    echo "<div class='inline' onclick='showSnackbar(\"Please login to make a reply.\");'><h2 class='post-thread-header-text'>[Reply]</h2></div>";
                }
            ?>
    <?php
        } else {
    ?>
            <div class='inline'><h2><a class='post-thread-header-text' href='index.php?board/archive/dir=<?php echo $board['directory']; ?>'>[Archive]</a></h2></div>
    <?php
        }
    ?>
</div>
<hr>
<?php
    $op_post = array_shift($posts);
    View::render('PostView.php', ['post' => $op_post, 'thread' => $thread, 'post_ids' => $post_ids, 'board' => $board, 'op_id' => $op_post['id']]);
    foreach ($posts as $post) {
        View::render('PostView.php', ['post' => $post, 'post_ids' => $post_ids, 'board' => $board, 'op_id' => $op_post['id']]);
    }
?>
<?php
    $args = ['board' => $board];
    if (isset($error))
        $args['error'] = $error;
    View::render('CreatePostModal.php', $args); 
?>