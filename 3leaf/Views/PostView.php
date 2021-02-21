<?php

use Dalton\ThreeLeaf\Models\PostModel;

require_once ROOT_PATH.'3leaf/Models/PostModel.php';

// The OP is displayed differently.
if (array_key_exists('thread', $args)) {
    $thread = $args['thread'];
    $thread_name = $thread['name'];
}
$post_ids = $args['post_ids'];
$post = $args['post'];

// Process links to other posts and threads here.
$post['content'] = preg_replace('/(&gt;&gt;)([0-9]+)/', '<a href=\'#p\2\'>\1\2</a>', $post['content']);

$callback = function($matches) use ($post_ids) {
    $carrots = $matches[1];
    $digits = $matches[2];
    if (in_array($digits, $post_ids)) {
        return '<a href=\'#p' . $digits . '\'>'. $carrots . $digits . '</a>';
    }

    $post = PostModel::selectPostIDsFromPostID($digits);
    if ($post == null) {
        return '<a style=\'text-decoration: line-through;\'>'. $carrots . $digits . '</a>';
    }

    return '<a href="index.php?board/dir=' . $post['directory'] . '/thread=' . $post['thread_id'] . '#p' . $digits . '">'. $carrots . $digits . '</a>';
};

$post['content'] = preg_replace_callback('/(&gt;&gt;)([0-9]+)/', $callback, $post['content']);

?>
<script>
function toggleVisibleByClick(elmnt1, elmnt2) {
    let post_wrapper = document.getElementById("<?php echo 'p' . $post['id']; ?>-wrapper");

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
    <div class='post-wrapper' id='<?php echo 'p' . $post['id']; ?>-wrapper'>
        <?php
            if (array_key_exists('file_name', $post)) {
        ?>
            <img class='post-image' id='<?php echo $post['file_name'];?>' src='<?php echo "post_images/" . $post['file_name']; ?>' alt='<?php $post['file_name']; ?>' />
            <img class='post-thumb-image' id='<?php echo $post['file_name'];?>-thumb' src='<?php echo "post_images/" . $post['file_name']; ?>' alt='<?php $post['file_name']; ?>' />
            <script>toggleVisibleByClick(document.getElementById("<?php echo $post['file_name'];?>"), document.getElementById("<?php echo $post['file_name'];?>-thumb"));</script>
        <?php
            }
        ?>
        <div id="p<?php echo $post['id']; ?>" class='post-content-wrapper'>
            <div class='post-header'>
                <p class='info-text inline'>post no. <?php echo $post['id']; ?></p>
                <?php if (isset($thread)) { ?>
                    <h4 class='inline'><?php echo $thread['name']; ?></h4><br>
                <?php } ?>
                <p class='posted-by-text inline'>by <?php echo $post['username'] ?> at <?php echo $post['time_created'] ?></p>
            </div>
            <div class='post-content'>
                <p><?php echo $post['content']; ?></p>
            </div>
        </div>
    </div>
</div>