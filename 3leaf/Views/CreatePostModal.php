<div id='modal-wrapper'>
    <div id='modal'>
        <div class='container-wrapper'>
            <div class='container-header' id='modal-draggable'>
                <h2>Create Post</h2>
                <div id='modal-close'>X</div>
            </div>
            <div class='container-content'>
                <form action='router.php?/some/route' method='post' enctype='multipart/form-data'>
                    <span><label for='image'>Image:</label><input type='file' name='image'/></span><br>
                    <span><label for='comment'>Comment:</label><input type='text' name='comment'/></span><br>
                    <input type='submit' value='Post'/>
                </form>
            </div>
        </div>
    </div>
</div>
<script>dragElement(document.getElementById("modal"));</script>