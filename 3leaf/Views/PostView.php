<?php

use Dalton\ThreeLeaf\Models\PostModel;

require_once ROOT_PATH.'3leaf/Models/PostModel.php';
require_once ROOT_PATH.'3leaf/global_const.php';

// The OP is displayed differently.
if (array_key_exists('thread', $args)) {
    $thread = $args['thread'];
    $thread_name = $thread['name'];
}

$post_ids = $args['post_ids']; // The posts in this thread.
$post = $args['post'];
$board = $args['board'];

// Process links to other posts and threads here.
$callback = function($matches) use ($post_ids) {
    $carrots = $matches[1];
    $digits = $matches[2];
    if (in_array($digits, $post_ids)) {
        return '<a class="text-link" href=\'#p' . $digits . '\'>'. $carrots . $digits . '</a>';
    }

    $post_reply = PostModel::selectPostIDsFromPostID($digits);
    if ($post_reply == null) {
        return "<a class='text-link' style='text-decoration: line-through;'>". $carrots . $digits . '</a>';
    }

    return "<a class='text-link' href='index.php?board/dir=" . $post_reply['directory'] . "/thread=" . $post_reply['thread_id'] . "#p" . $digits . "'>". $carrots . $digits . "</a>";
};

$post['content'] = preg_replace_callback('/(&gt;&gt;)([0-9]+)/', $callback, $post['content']);

$replies_to_post = PostModel::getRepliesToPost($post['id']);
$reply_ids = [];
foreach ($replies_to_post as $reply) {
    array_push($reply_ids, $reply['reply_post_id']);
}

?>
<script>
function toggleVisibleByClick(elmnt1, elmnt2) {
    if (elmnt1 == null || elmnt2 == null)
        return;
    let post_wrapper = document.getElementById("<?php echo 'p' . $post['id']; ?>");

    elmnt1.onclick = () => {
        elmnt1.style.display = "none";
        elmnt2.style.display = "inline-block";
        post_wrapper.style.display = "flex";
    }

    elmnt2.onclick = () => {
        elmnt2.style.display = "none";
        elmnt1.style.display = "inline-block";
        post_wrapper.style.display = "flex";
    }
}
</script>

<div class='post-margins'>
    <div class='post-wrapper' id='<?php echo 'p' . $post['id']; ?>'>
        <?php
            if (array_key_exists('file_name', $post) && $post['file_name'] != null) {
        ?>
            <img class='post-image' id='<?php echo $post['file_name'];?>' src='<?php echo "post_images/" . $post['file_name']; ?>' alt='<?php $post['file_name']; ?>' />
            <img class='post-thumb-image' id='<?php echo $post['file_name'];?>-thumb' src='<?php echo "post_images/" . $post['file_name']; ?>' alt='<?php $post['file_name']; ?>' />
            <script>toggleVisibleByClick(document.getElementById("<?php echo $post['file_name'];?>"), document.getElementById("<?php echo $post['file_name'];?>-thumb"));</script>
        <?php
            }
        ?>
        <div class='post-content-wrapper'>
            <div class='post-header'>
                <p id='<?php echo 'pid' . $post['id']; ?>' class='info-text inline post-id' onclick='onPostIDClick(this)'>post no. <?php echo $post['id']; ?></p>
                <?php if (isset($thread)) { ?>
                    <h4 class='inline'><?php echo $thread['name']; ?></h4><br>
                <?php } ?>
                    <p class='posted-by-text inline'>by <a class='posted-by-text-link' href='index.php?user/username=<?php echo $post['username']; ?>'><?php echo $post['username'] ?></a> at <?php echo $post['time_created'] ?></p>
                    <div class='dropdown inline'>
                        <span>&#10157;</span>
                        <div class='dropdown-content'>
                            <p class='dropdown-content-box'><a id='report-<?php echo $post['id']; ?>' onclick='createReport(this);'>Report</a></p>
                            <?php 
                                if (array_key_exists(USER_ID, $_SESSION) && strtolower($post['username']) == strtolower($_SESSION[USERNAME]) || 
                                    array_key_exists(ROLE, $_SESSION) && $_SESSION[ROLE] == MODERATOR) {
                                    // This is OP, so we delete the thread if OP is deleted.
                                    if (array_key_exists('thread', $args)) {
                                        echo "<p class='dropdown-content-box'><a id='delete-" . $thread['id'] . "' onclick='deleteThread(this, \"" . $board['directory']  . "\");'>Delete</a></p>";
                                    } else {
                                        echo "<p class='dropdown-content-box'><a id='delete-" . $post['id'] . "' onclick='deletePost(this);'>Delete</a></p>";
                                    }
                                }
                            ?>
                        </div>
                    </div>
                <?php
                    if (!empty($reply_ids)) {
                        foreach ($reply_ids as $reply_id) {
                            if (in_array($reply_id, $post_ids)) {
                                echo ' ';
                                echo '<a class="text-link" href=\'#p' . $reply_id . '\'>>>' . $reply_id . '</a>';
                            } else {
                                $post_reply = PostModel::selectPostIDsFromPostID($reply_id);
                                if ($post_reply == null) {
                                    echo '<a class="text-link style=\'text-decoration: line-through;\'>>>' . $reply_id . '</a>';
                                } else {
                                    echo "<a class='text-link' href='index.php?board/dir=" . $post_reply['directory'] . "/thread=" . $post_reply['thread_id'] . "#p" . $reply_id . "'>>>" . $reply_id . "</a>";
                                }
                            }
                        }
                    }
                ?>
            </div>
            <div class='post-content'>
                <p><?php echo $post['content']; ?></p>
            </div>
        </div>
    </div>
</div>