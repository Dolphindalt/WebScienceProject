<div id='modal-wrapper'>
    <div id='modal'>
        <div class='container-wrapper'>
            <div class='container-header' id='modal-draggable'>
                <h2 class='inline'>Create Post</h2>
                <div class='inline' id='modal-close'>X</div>
            </div>
            <div class='container-content'>
                <form id='postForm' method='post' enctype='multipart/form-data'>
                    <span><input id='file' class='form-input' type='file' name='image'/></span><br>
                    <span><textarea id='comment' class='form-input' name='comment' placeholder='Comment'></textarea></span><br>
                    <button id='postButton' class='center'>Post</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>dragElement(document.getElementById("modal"));</script>