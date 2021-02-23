<?php

$posts = $args['posts'];
$username = $args['username'];

?>
<div class='container-wrapper'>
    <div class='container-header'>
        <h2>Activity of User <?php echo $username; ?></h2>
    </div>
    <div class='container-content'>
        <?php
            if (!empty($posts)) {
                echo "<table><tbody>";
                foreach ($posts as $post) {
                    $post_content = $post['content'];
                    $post_id = $post['post_id'];
                    $post_time = $post['time_created'];
                    $thread_id = $post['thread_id'];
                    $board_dir = $post['directory'];
                    $board_name = $post['board_name'];
                    ?>
                        <tr>
                            <td><a href='index.php?board/dir=<?php echo $board_dir; ?>/thread=<?php echo $thread_id; ?>#p<?php echo $post_id; ?>' class='text-link'>>><?php echo $post_id; ?></a></td>
                            <td><h4><?php echo $board_name; ?></h4></td>
                            <td><p class='info-text'>thread no. <?php echo $thread_id; ?> post no. <?php echo $post_id; ?></p></td>
                            <td><p class='posted-by-text'>at <?php echo $post_time; ?></p>
                        </tr>
                    <?php
                }
                echo "</tbody></table>";
            } else {
                echo "<p>This user is not active.</p>";
            }
        ?>
    </div>
</div>