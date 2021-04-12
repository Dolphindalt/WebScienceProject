<?php 
use Dalton\ThreeLeaf\Models\PostModel;

require_once ROOT_PATH.'3leaf/Models/PostModel.php';
require_once ROOT_PATH.'3leaf/global_const.php';

// The OP is displayed differently.
if (array_key_exists('thread', $args)) {
    $thread = $args['thread'];
    $thread_name = $thread['name'];
}

$text_version_post = "";
$post_ids = $args['post_ids']; // The posts in this thread.
$post_id = $args['post_id'];
$board = $args['board'];
$op_post_id = $args['op_id'];
$go_text = false;
if (array_key_exists("go_text", $args)) {
    $go_text = true;
}
$post = PostModel::selectPostByID($post_id);
if ($post != null) {

    // Process links to other posts and threads here.
    $callback = function($matches) use ($post_ids, $op_post_id) {
        $carrots = $matches[1];
        $digits = $matches[2];
        if (in_array($digits, $post_ids)) {
            $ext = "";
            if ((int) $digits == (int) $op_post_id) {
                $ext = " (OP)";
            }
            return '<a class="text-link" href=\'#p' . $digits . '\'>'. $carrots . $digits . $ext . '</a>';
        }

        $post_reply = PostModel::selectPostIDsFromPostID($digits);
        if ($post_reply == null) {
            return "<a class='text-link' style='text-decoration: line-through;'>". $carrots . $digits . '</a>';
        }

        return "<a class='text-link' href='index.php?board/dir=" . $post_reply['directory'] . "/thread=" . $post_reply['thread_id'] . "#p" . $digits . "'>". $carrots . $digits . "&#8594;" . $post_reply['directory'] . "</a>";
    };

    $post['content'] = preg_replace_callback('/(&gt;&gt;)([0-9]+)/', $callback, $post['content']);

    $replies_to_post = PostModel::getRepliesToPost($post['id']);
    $reply_ids = [];
    foreach ($replies_to_post as $reply) {
        array_push($reply_ids, $reply['reply_post_id']);
    }

if (!$go_text)
{

    ?>

<div class='post-margins post-preview'>
    <div class='post-wrapper' style='border: 1px solid var(--variable-text-color);'>
        <?php
            if (array_key_exists('file_name', $post) && $post['file_name'] != null) {
        ?>
            <img class='post-thumb-image' src='<?php echo "post_images/" . $post['file_name']; ?>' alt='<?php $post['file_name']; ?>' />
        <?php
            }
        ?>
        <div class='post-content-wrapper'>
            <div class='post-header'>
                <?php if (isset($thread)) { ?>
                    <h4 class='inline'><?php echo $thread['name']; ?></h4>
                <?php } ?>
                    <p class='posted-by-text inline'><a class='posted-by-text-link' href='index.php?user/username=<?php echo $post['username']; ?>'><?php echo $post['username'] ?></a> <?php echo date('d/m/y h:i A', strtotime($post['time_created'])); ?></p>
                    <p class='info-text inline post-id' onclick='onPostIDClick(this)'>no.<?php echo $post['id']; ?></p>

                    <div class='dropdown inline'>
                        <span>&#10157;</span>
                    </div>
                <?php
                    if (!empty($reply_ids)) {
                        foreach ($reply_ids as $reply_id) {
                            echo '&nbsp;';
                            if (in_array($reply_id, $post_ids)) {
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

<?php
}
    if ($go_text == true)
    {
        // This is cancer, but it is my last second ghetto fix.
        $text_version_post .= "<div class='post-margins post-preview'>";
        $text_version_post .= "<div class='post-wrapper' style='border: 1px solid var(--variable-text-color);'>";
            if (array_key_exists('file_name', $post) && $post['file_name'] != null) {
            $text_version_post .= "<img class='post-thumb-image' src='post_images/" . $post["file_name"] . "' alt='" . $post["file_name"] . "'/>";
            }
        $text_version_post .= "<div class='post-content-wrapper'>";
        $text_version_post .= "<div class='post-header'>";
                if (isset($thread)) {
                    $text_version_post .= "<h4 class='inline'>" . $thread['name'] . "</h4>";
                }
                    $text_version_post .= "<p class='posted-by-text inline'><a class='posted-by-text-link'>" . $post['username'] . "</a> " . date('d/m/y h:i A', strtotime($post['time_created'])) . "</p>" . '&nbsp';
                    $text_version_post .= "<p class='info-text inline post-id'>no." . $post['id'] . "</p>";

                    $text_version_post .= "<div class='dropdown inline'>";
                    $text_version_post .= "<span>&#10157;</span>";
                    $text_version_post .= "</div>";
                    if (!empty($reply_ids)) {
                        foreach ($reply_ids as $reply_id) {
                            $text_version_post .= '&nbsp;';
                            if (in_array($reply_id, $post_ids)) {
                                $text_version_post .= '<a class="text-link" href=\'#p' . $reply_id . '\'>>>' . $reply_id . '</a>';
                            } else {
                                $post_reply = PostModel::selectPostIDsFromPostID($reply_id);
                                if ($post_reply == null) {
                                    $text_version_post .= '<a class="text-link style=\'text-decoration: line-through;\'>>>' . $reply_id . '</a>';
                                } else {
                                    $text_version_post .= "<a class='text-link' href='index.php?board/dir=" . $post_reply['directory'] . "/thread=" . $post_reply['thread_id'] . "#p" . $reply_id . "'>>>" . $reply_id . "</a>";
                                }
                            }
                        }
                    }
            $text_version_post .= "</div>";
            $text_version_post .= "<div class='post-content'>";
                $text_version_post .= "<p>" . $post['content'] . "</p>";
            $text_version_post .= "</div>";
        $text_version_post .= "</div>";
    $text_version_post .= "</div>";
    $text_version_post .= "</div>";
    $text_version_post = trim($text_version_post);
    }
}

?>