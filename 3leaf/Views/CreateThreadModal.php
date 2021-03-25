<?php

$board = $args['board'];

if (isset($args['error'])) {
    $error = $args['error'];
}

?>
<div id='modal-wrapper'>
    <div id='modal'>
        <div class='container-wrapper' style='width: inherit !important;'>
            <div class='container-header' id='modal-draggable'>
                <h2 class='inline'>Create Thread</h2>
                <div class='inline' id='modal-close'>X</div>
            </div>
            <div class='container-content'>
                <form id="threadCreate" name="threadCreate" method='post' enctype='multipart/form-data' class='form-style'>
                    <span><input class='form-input' type='file' name='image' id='image'/></span><br>
                    <span><input class='form-input' type='text' name='name' id='name' placeholder='Thread name' maxlength="64"/></span><br>
                    <span><textarea class='form-input' name='comment' id='comment' placeholder='Comment' maxlength='8192'></textarea></span><br>
                    <input style="display: none;" type="text" id="website" name="website"/>
                    <button id='threadSubmit' class='center'>Post</button>
                    <?php
                        if (isset($error)) {
                            echo "<p class='error-text center'>$error</p>";
                        }
                    ?>
                </form>
            </div>
        </div>
    </div>
</div>
<script>dragElement(document.getElementById("modal"));</script>