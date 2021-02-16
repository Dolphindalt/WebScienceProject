<?php
    $board = $args['board'];
    $threads = $args['threads'];
    $rootPosts = $args['root_posts'];
?>
<h1 class='center'><?php echo $board['directory']; ?> - <?php echo $board['name']; ?></h1>
<hr>
<div id='modal-show'><h2>[Post new thread]</h2></div>
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
                <p class='info-text'>thread no. <?php echo str_pad($thread['id'], 10, '0', STR_PAD_LEFT); ?></p>
                <p class='posted-by-text'>by <?php echo $post['username'] ?></p>
                <h4><?php echo $thread['name']; ?></h4>
                <p><?php echo $post['content']; ?></p>
            </div>

        <?php
        }
    ?>
</div>
<?php require_once(ROOT_PATH.'3leaf/Views/CreateThreadModal.php'); ?>