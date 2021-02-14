function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    var wrapper = document.getElementById(elmnt.id + "-wrapper");
    var draggable = document.getElementById(elmnt.id + "-draggable");
    draggable.onmousedown = dragMouseDown;

    if (document.getElementById(elmnt.id + "-close")) {
        document.getElementById(elmnt.id + "-close").onclick = () => {
            wrapper.style.display = "none";
            elmnt.style.display = "none";
        }
    }

    if (document.getElementById(elmnt.id + "-show")) {
        document.getElementById(elmnt.id + "-show").onclick = () => {
            wrapper.style.display = "block";
            elmnt.style.display = "block";
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
    }
}