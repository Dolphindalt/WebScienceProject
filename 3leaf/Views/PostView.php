<?php

use Dalton\ThreeLeaf\Models\PostModel;
use Dalton\Framework\View;

require_once ROOT_PATH.'3leaf/Models/PostModel.php';
require_once ROOT_PATH.'3leaf/global_const.php';
require_once ROOT_PATH.'framework/View.php';

// The OP is displayed differently.
if (array_key_exists('thread', $args)) {
    $thread = $args['thread'];
    $thread_name = $thread['name'];
}

$post_ids = $args['post_ids']; // The posts in this thread.
$post = $args['post'];
$board = $args['board'];
$op_post_id = $args['op_id'];

// Process links to other posts and threads here.
$callback = function($matches) use ($post_ids, $op_post_id, $board) {
    $carrots = $matches[1];
    $digits = $matches[2];
    if (in_array($digits, $post_ids)) {
        $ext = "";
        if ((int) $digits == (int) $op_post_id) {
            $ext = " (OP)";
        }
        // Magic here and there.
        $view = 'PostPreview.php';
        $args = ['post_ids' => $post_ids, 'post_id' => $digits, 'board' => $board, 'op_id' => $op_post_id, 'go_text' => true ];
        extract($args, EXTR_SKIP);

        $file = ROOT_PATH . "/3leaf/Views/$view";
        if (is_readable($file)) {
            require $file;
        } 
        return '<a class="text-link" href=\'#p' . $digits . '\'>'. $carrots . $digits . $ext . '</a>' . $text_version_post;
    }

    $post_reply = PostModel::selectPostIDsFromPostID($digits);
    if ($post_reply == null) {
        return "<a class='text-link' style='text-decoration: line-through;'>". $carrots . $digits . '</a>';
    }

    $view = 'PostPreview.php';
    $args = ['post_ids' => $post_ids, 'post_id' => $digits, 'board' => $board, 'op_id' => $op_post_id, 'go_text' => true ];
    extract($args, EXTR_SKIP);

    $file = ROOT_PATH . "/3leaf/Views/$view";

    if (is_readable($file)) {
        require $file;
    }
    return "<a class='text-link' href='index.php?board/dir=" . $post_reply['directory'] . "/thread=" . $post_reply['thread_id'] . "#p" . $digits . "'>". $carrots . $digits . "&#8594;" . $post_reply['directory'] . "</a>" . $text_version_post;
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
                <?php if (isset($thread)) { ?>
                    <h4 class='inline'><?php echo $thread['name']; ?></h4>
                <?php } ?>
                    <p class='posted-by-text inline'><a class='posted-by-text-link' href='index.php?user/username=<?php echo $post['username']; ?>'><?php echo $post['username'] ?></a> <?php echo date('d/m/y h:i A', strtotime($post['time_created'])); ?></p>
                    <p id='<?php echo 'pid' . $post['id']; ?>' class='info-text inline post-id' onclick='onPostIDClick(this)'>no.<?php echo $post['id']; ?></p>

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
                            echo '&nbsp;';
                            if (in_array($reply_id, $post_ids)) {
                                echo '<a class="text-link" href=\'#p' . $reply_id . '\'>>>' . $reply_id . '</a>';
                                View::render('PostPreview.php', $args = ['post_ids' => $post_ids, 'post_id' => $reply_id, 'board' => $board, 'op_id' => $op_post_id ]);
                            } else {
                                $post_reply = PostModel::selectPostIDsFromPostID($reply_id);
                                if ($post_reply == null) {
                                    echo '<a class="text-link style=\'text-decoration: line-through;\'>>>' . $reply_id . '</a>';
                                } else {
                                    echo "<a class='text-link' href='index.php?board/dir=" . $post_reply['directory'] . "/thread=" . $post_reply['thread_id'] . "#p" . $reply_id . "'>>>" . $reply_id . "</a>";
                                    View::render('PostPreview.php', $args = ['post_ids' => $post_ids, 'post_id' => $reply_id, 'board' => $board, 'op_id' => $op_post_id ]);
                                }
                            }
                        }
                    }
                ?>
            </div>
            <div class='post-content' style='color: var(--variable-text-color);'>
                <?php echo $post['content']; ?>
            </div>
        </div>
    </div>
</div>