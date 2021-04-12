<?php

use Dalton\Framework\View;

$board = $args['board'];
$threads = $args['threads'];
$rootPosts = $args['root_posts'];
$is_archive = $args['is_archive'];
if (array_key_exists('error', $args))
    $error = $args['error'];

?>
<h1 class='center'><?php echo $board['directory']; ?> - <?php echo $board['name']; ?></h1>
<?php
    if ($is_archive) {
        echo "<h4 class='center'>These threads are archived. You can no longer reply.</h4>";
    }
?>
<hr>
<?php
    if (!$is_archive) {
?>
        <div class='center'>
            <div class='inline'>
                <?php
                    if (isset($_SESSION) && array_key_exists(LOGGED_IN, $_SESSION) && $_SESSION[LOGGED_IN]) {
                        echo "<div id='modal-show'><h2 class='post-thread-header-text'>[Post new thread]</h2></div>";
                    } else {
                        echo "<div><h2 class='post-thread-header-text' onclick='showSnackbar(\"Please login to make a thread.\");'>[Post new thread]</h2></div>";
                    }
                ?>
            </div>
                <div class='inline'><h2><a class='post-thread-header-text' href='index.php?board/archive/dir=<?php echo $board['directory']; ?>'>[Archive]</a></h2></div>
        </div>
        <hr>
<?php
    }
?>
<div class='board-catalog-grid'>
    <?php
        if (!empty($threads)) {
            for ($i = 0; $i < sizeof($threads); $i++) {
                $thread = $threads[$i];
                $post = $rootPosts[$i];
                ?>

                <div class='catalog-container'>
                    <a href='index.php?board/dir=<?php echo $board['directory']; ?>/thread=<?php echo $thread['id']; ?>'>
                        <img class='catalog-image' src='post_images/<?php echo $post['file_name']; ?>'/>
                    </a>
                    <div style='word-wrap: nowrap;'>
                        <p style='display: inline;'>R: <?php echo $thread['post_count']; ?> I: <?php echo $thread['image_count']; ?></p>
                        <p style='display: inline;' class='posted-by-text'>by <a class='posted-by-text-link' href='index.php?user/username=<?php echo $post['username']; ?>'><?php echo $post['username'] ?></a></p>
                    </div>
                    <div>
                    <p class='hide-long-text'>
                        <b class='h4'><?php echo $thread['name']; ?></b>:
                        <?php echo strip_tags($post['content'], '<a><p><span>'); ?>
                    </p>
                    </div>
                </div>

            <?php
            }
        } else {
            echo "<h4 class='center'>No threads yet!</h4>";
        }
    ?>
</div>
<?php 
    $args = ['board' => $board];
    if (isset($error))
        $args['error'] = $error;
    View::render('CreateThreadModal.php', $args); 
?>
