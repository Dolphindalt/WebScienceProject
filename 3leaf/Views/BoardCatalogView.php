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
                <div id='modal-show'><h2 class='post-thread-header-text'>[Post new thread]</h2></div>
            </div>
                <div class='inline'><a class='post-thread-header-text' href='index.php?board/archive/dir=<?php echo $board['directory']; ?>'><h2>[Archive]</h2></a></div>
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
                    <p>R: <?php echo $thread['post_count']; ?> I: <?php echo $thread['image_count']; ?></p>
                    <p class='info-text'>thread no. <?php echo $thread['id']; ?></p>
                    <p class='posted-by-text'>by <a class='posted-by-text-link' href='index.php?user/username=<?php echo $post['username']; ?>'><?php echo $post['username'] ?></a></p>
                    <h4><?php echo $thread['name']; ?></h4>
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
