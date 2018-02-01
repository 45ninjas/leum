
var mediaId = GetMediaItemIndex();
if(mediaId != null)
    ShowMediaItemModal(mediaId);

$(window).bind('hashchange', function()
{
    var mediaId = GetMediaItemIndex();
    
    if(mediaId != null)
        ShowMediaItemModal(mediaId);
});

function GetMediaItemIndex()
{
    var prefix = "#view";

    var hash = location.hash;

    if(hash.startsWith(prefix))
        return parseInt(hash.substring(prefix.length), 10);
    else
        return null;
}

function ShowMediaItemModal(mediaIndex)
{
    // Step one, download the media information.
    $()
    // Create the content to show in the modal.
    var contentNode = document.createTextNode("Hello World!");

    // Create the modal.
    var modal = new Modal(
        "[Media Name]",
        contentNode,
        [new Button("Close", "pure-button-default", function() {location.hash = ""; modal.Close();})]
    );
}



// Menu Hambuger
(function (window, document) {

    var layout   = document.getElementById('layout'),
        menu     = document.getElementById('menu'),
        menuLink = document.getElementById('menuLink'),
        content  = document.getElementById('main');

    function toggleClass(element, className) {
        var classes = element.className.split(/\s+/),
            length = classes.length,
            i = 0;

        for(; i < length; i++) {
          if (classes[i] === className) {
            classes.splice(i, 1);
            break;
          }
        }
        // The className is not found
        if (length === classes.length) {
            classes.push(className);
        }

        element.className = classes.join(' ');
    }

    function toggleAll(e) {
        var active = 'active';

        e.preventDefault();
        toggleClass(layout, active);
        toggleClass(menu, active);
        toggleClass(menuLink, active);
    }

    menuLink.onclick = function (e) {
        toggleAll(e);
    };

    content.onclick = function(e) {
        if (menu.className.indexOf('active') !== -1) {
            toggleAll(e);
        }
    };

}(this, this.document));