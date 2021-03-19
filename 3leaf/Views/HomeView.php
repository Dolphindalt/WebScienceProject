<?php
    $threads = $args['threads'];
    $rootPosts = $args['root_posts'];
?>
<div class='container-wrapper'>
    <div class='container-header'>
        <h2>Welcome to 3leaf</h2>
    </div>
    <div class='container-content'>
        Welcome to 3leaf, an image board site for sharing whatever content you want. Make at home by familiarizing yourself with the <a href='index.php?rules'>rules</a> and reading the <a href='index.php?faq'>FAQ</a>.
    </div>
</div>

<div class='container-wrapper'>
    <div class='container-header'>
        <h2>Recent activity</h2>
    </div>
    <div class='container-content'>
        <div class='board-catalog-grid'>
            <?php
                if (!empty($threads)) {
                    for ($i = 0; $i < sizeof($threads); $i++) {
                        $thread = $threads[$i];
                        $post = $rootPosts[$i];
                        ?>

                        <div class='catalog-container'>
                            <a href='index.php?board/dir=<?php echo $thread['directory']; ?>/thread=<?php echo $thread['id']; ?>'>
                                <img class='catalog-image' src='post_images/<?php echo $post['file_name']; ?>'/>
                            </a>
                            <p>B: <?php echo $thread['directory'] ?> R: <?php echo $thread['post_count']; ?> I: <?php echo $thread['image_count']; ?></p>
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
    </div>
</div>