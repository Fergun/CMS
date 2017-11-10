var taskItemClassName = "div-row";
var menuState = 0;
var contextMenuActive = "div-context-menu-active";
var menu = document.querySelector(".div-context-menu");

if (document.addEventListener) { // IE >= 9; other browsers
    document.addEventListener('contextmenu', function(e) {
        var div_row = $( ".div-row:hover" ).attr('id');
        var body = $( "body" ).attr('name');
        // if(div_row) {
            // $(".div-context-menu").toggleClass('div-context-menu-active');

            taskItemInContext = clickInsideElement( e, taskItemClassName );
            if ( taskItemInContext ) {
                e.preventDefault();
                toggleMenuOn();
                var x = mouseX(event)
                var y = mouseY(event)
                $(".div-context-menu-active").css({'left' : x + 'px'})
                $(".div-context-menu-active").css({'top' : y + 'px'})
                $(".context-menu-list").each(function(i){
                    $(this).attr("onclick", 'window.location.href="http://undertheowl.pl/cms/edit.php?header_code='+ body +'&mode=' + statuses_map($(this).text()) +'&id=' + div_row + '"');
                });
                // positionMenu(e);
            } else {
                taskItemInContext = null;
                toggleMenuOff();
            }
        // }
    }, false);
} else { // IE < 9
    document.attachEvent('oncontextmenu', function() {
        alert("Your browser is not supported");
        window.event.returnValue = false;
    });
}

$(document).click(function(){
    taskItemInContext = null;
    toggleMenuOff();
});

function statuses_map(text){
    switch(text) {
        case 'Edytuj':
            string = 'edit';
            break;
        case 'Usuń':
            string = 'delete';
            break;
    }
    return string;
}

function mouseX(evt) {
    if (evt.pageX) {
        return evt.pageX;
    } else if (evt.clientX) {
        return evt.clientX + (document.documentElement.scrollLeft ?
                document.documentElement.scrollLeft :
                document.body.scrollLeft);
    } else {
        return null;
    }
}

function mouseY(evt) {
    if (evt.pageY) {
        return evt.pageY;
    } else if (evt.clientY) {
        return evt.clientY + (document.documentElement.scrollTop ?
                document.documentElement.scrollTop :
                document.body.scrollTop);
    } else {
        return null;
    }
}

function toggleMenuOn() {
    if ( menuState !== 1 ) {
        menuState = 1;
        menu.classList.add( contextMenuActive );
    }
}

function toggleMenuOff() {
    if ( menuState !== 0 ) {
        menuState = 0;
        menu.classList.remove( contextMenuActive );
    }
}

function clickInsideElement( e, className ) {
    var el = e.srcElement || e.target;

    if ( el.classList.contains(className) ) {
        return el;
    } else {
        while ( el = el.parentNode ) {
            if ( el.classList && el.classList.contains(className) ) {
                return el;
            }
        }
    }

    return false;
}