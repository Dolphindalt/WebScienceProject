function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    var wrapper = document.getElementById(elmnt.id + "-wrapper");
    var draggable = document.getElementById(elmnt.id + "-draggable");
    draggable.onmousedown = dragMouseDown;

    if (getCookie("modal-status") == "open") {
        wrapper.style.display = "block";
        elmnt.style.display = "block";
        let x_style = getCookie("modal-x");
        if (x_style != "") {
            elmnt.style.left = x_style;
        }
        let y_style = getCookie("modal-y") 
        if (y_style != "") {
            elmnt.style.top = y_style;
        }
    }

    if (document.getElementById(elmnt.id + "-close")) {
        document.getElementById(elmnt.id + "-close").onclick = () => {
            wrapper.style.display = "none";
            elmnt.style.display = "none";
            setCookie("modal-status", "closed");
        }
    }

    if (document.getElementById(elmnt.id + "-show")) {
        document.getElementById(elmnt.id + "-show").onclick = () => {
            wrapper.style.display = "block";
            elmnt.style.display = "block";
            setCookie("modal-status", "open");
        } 
    }

    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        // get the mouse cursor position at startup:
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position:
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position:
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
      }
    
    function closeDragElement() {
        // stop moving when mouse button is released:
        document.onmouseup = null;
        document.onmousemove = null;
        setCookie("modal-x", elmnt.style.left);
        setCookie("modal-y", elmnt.style.top);
    }
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
  
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function mysqlGmtStrToJSDate(str) {
    var t = str.split(/[- :]/);
    // Apply each element to the Date function
    return new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
}

function mysqlGmtStrToJSLocal(str) {
    // first create str to Date object
    var g = mysqlGmtStrToJSDate(str);
    return new Date(g.getTime() - ( g.getTimezoneOffset() * 60000 ));
}

function onPostIDClick(clicked_elmnt) {
    let elm_id = clicked_elmnt.id;
    let post_id = elm_id.substring(3, elm_id.length);
    $('#comment').val($('#comment').val() + '>>' + post_id + '\n');
    document.getElementById("modal-show").onclick();
}

function deleteThread(clicked_elmnt, board_dir) {
    let elm_id = clicked_elmnt.id;
    let thread_id = elm_id.substring(7, elm_id.length);
    $.ajax({
        url: "index.php?threads/thread_id=" + thread_id,
        type: "POST",
        success: () => {
            window.location.replace("index.php?board/dir=" + board_dir);
            showSnackbar('Thread deleted.');
        },
        error: () => {
            window.location.reload();
            showSnackbar('Failed to delete thread.');
        }
    });
}

function deletePost(clicked_elmnt) {
    let elm_id = clicked_elmnt.id;
    let post_id = elm_id.substring(7, elm_id.length);
    $.ajax({
        url: "index.php?posts/post_id=" + post_id,
        type: "POST",
        success: () => {
            window.location.reload();
            showSnackbar('Post deleted.');
        },
        error: () => {
            window.location.reload();
            showSnackbar('Failed to delete post.');
        }
    });
}

function createReport(clicked_elmnt) {
    let elm_id = clicked_elmnt.id;
    let post_id = elm_id.substring(7, elm_id.length);
    $.ajax({
        url: "index.php?reports/post_id=" + post_id,
        type: "POST",
        success: () => {
            showSnackbar('Report created.');
        },
        error: (xhr) => {
            if (xhr.status == 401) {
                showSnackbar('Login to do this.');
            } else if (xhr.status == 409) {
                showSnackbar('This post is already reported.');
            } else {
                showSnackbar('Something went wrong.');
            }
        }
    });
}

function deleteReport(clicked_elmnt) {
    let elm_id = clicked_elmnt.id;
    let report_id = elm_id.substring(7, elm_id.length);
    $.ajax({
        url: "index.php?reports/report_id=" + report_id,
        type: "POST",
        success: () => {
            window.location.reload();
        },
        error: () => {
            window.location.reload();
        }
    })
}

function showSnackbar(text) {
    let elmt = document.getElementById("snackbar");
    elmt.className = "show";
    elmt.innerHTML = text;
    setTimeout(() => { elmt.className = elmt.className.replace("show", ""); }, 3000);
}
