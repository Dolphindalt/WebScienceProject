<?php

// The OP is displayed differently.
$thread = $args['thread'];
$thread_name = $thread['name'];
$post = $args['post'];

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
        post_wrapper.style.display = "inline-block";
    }
}
</script>

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
    <div class='post-content-wrapper'>
        <div class='post-header'>
            <p class='info-text inline'>post no. <?php echo $post['id']; ?></p>
            <h4 class='inline'><?php echo $thread['name']; ?></h4><br>
            <p class='posted-by-text inline'>by <?php echo $post['username'] ?> at <?php echo $post['time_created'] ?></p>
        </div>
        <div class='post-content'>
            <p><?php echo $post['content']; ?></p>
        </div>
    </div>
</div>