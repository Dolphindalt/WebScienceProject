<div id='modal-wrapper'>
    <div id='modal'>
        <div class='container-wrapper'>
            <div class='container-header' id='modal-draggable'>
                <h2 class='inline'>Create Post</h2>
                <div class='inline' id='modal-close'>X</div>
            </div>
            <div class='container-content'>
                <form action="<?php echo htmlspecialchars('router.php?/some/route'); ?>" method='post' enctype='multipart/form-data'>
                    <span><label for='image'>Image:</label><input class='form-input' type='file' name='image'/></span><br>
                    <span><label for='comment'>Comment:</label><textarea class='form-input' name='comment'></textarea></span><br>
                    <input type='submit' value='Post'/>
                </form>
            </div>
        </div>
    </div>
</div>
<script>dragElement(document.getElementById("modal"));</script>