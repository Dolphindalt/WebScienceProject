<div id='modal-wrapper'>
    <div id='modal'>
        <div class='container-wrapper'>
            <div class='container-header' id='modal-draggable'>
                <h2 class='inline'>Create Thread</h2>
                <div class='inline' id='modal-close'>X</div>
            </div>
            <div class='container-content'>
                <form id="threadCreate" method='post' name="threadCreate" enctype='multipart/form-data'>
                    <span><label for='image'>Image:</label><input class='form-input' type='file' name='image' id='image'/></span><br>
                    <p style='display: none;' class='error-text' id='image-error-text'></p>
                    <span><label for='name'>Name:</label><input class='form-input' type='text' name='name' id='name' /></span><br>
                    <p style='display: none;' class='error-text' id='name-error-text'></p>
                    <span><label for='comment'>Comment:</label><textarea class='form-input' name='comment' id='comment' ></textarea></span><br>
                    <p style='display: none;' class='error-text' id='comment-error-text'></p>
                    <input id='threadSubmit' type='button' value='Post'/>
                </form>
            </div>
        </div>
    </div>
</div>
<script>dragElement(document.getElementById("modal"));</script>
<script>
    $(document).ready(() => {
        $("#threadSubmit").click(() => {
            $("#image-error-text").css('display', 'none');
            $("#name-error-text").css('display', 'none');
            $("#comment-error-text").css('display', 'none');
            let local_image = $('#image').prop('files')[0];
            let local_name = $("#name").val();
            let local_comment = $("#comment").val();
            if (local_image == null) {
                $("#image-error-text").text((i, o) => {
                    return "Image is required.";
                });
            }
            if (local_name == '') {
                $("#name-error-text").text((i, o) => {
                    return "Thread name is required.";
                });
            }
            if (local_comment == '') {
                $("#comment-error-text").text((i, o) => {
                    return "Thread comment is required.";
                });
            }
            $.post("index.php/form?something", {
                image: local_image,
                name: local_name,
                comment: local_comment
            }, (data) => {
                $("#threadCreate")[0].reset();
                alert(data);
            }, (error) => {
                alert(error);
            });
        });
    });
</script>