<?php

use Dalton\Framework\View;

$board = $args['board'];
$threads = $args['threads'];
$rootPosts = $args['root_posts'];
if (array_key_exists('error', $args))
    $error = $args['error'];

?>
<h1 class='center'><?php echo $board['directory']; ?> - <?php echo $board['name']; ?></h1>
<hr>
<div class='center'>
    <div id='modal-show'><h2 class='post-thread-header-text'>[Post new thread]</h2></div>
</div>
<hr>
<div class='board-catalog-grid'>
    <?php
        for ($i = 0; $i < sizeof($threads); $i++) {
            $thread = $threads[$i];
            $post = $rootPosts[$i];
            ?>

            <div class='catalog-container'>
                <a href='index.php?board/dir=r/thread=<?php echo $thread['id']; ?>'>
                    <img class='catalog-image' src='post_images/<?php echo $post['file_name']; ?>'/>
                </a>
                <p>R: <?php echo $thread['post_count']; ?> I: <?php echo $thread['image_count']; ?></p>
                <p class='info-text'>thread no. <?php echo $thread['id']; ?></p>
                <p class='posted-by-text'>by <?php echo $post['username'] ?></p>
                <h4><?php echo $thread['name']; ?></h4>
            </div>

        <?php
        }
    ?>
</div>
<?php 
    $args = ['board' => $board];
    if (isset($error))
        $args['error'] = $error;
    View::render('CreateThreadModal.php', $args); 
?>