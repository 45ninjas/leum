function GetRootDir()
{
    return document.head.querySelector("[property=site-root]").content;
}

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

function HtmlToElement(htmlString)
{
    var template = document.createElement('template');
    htmlString = htmlString.trim();
    template.innerHTML = htmlString;
    return template.content;
}

function ShowMediaItemModal(mediaIndex)
{
    var url = GetRootDir() + "/api/v1/browse/media-modal/" + mediaIndex;
    SetModalBack(true);
    var modal = new Modal();
    // Step one, download the media information.
    var jqxhr = $.getJSON(url, function(data)
    {
        if(data != false)
        {
            modal.Show(
                data["title"],
                HtmlToElement(data["html"]),
                [
                    new Button("Close", "button-warning", function() { location.hash =""; modal.Close(); }),
                    new Button("Edit", "", function() { window.location.href = GetRootDir() + "/edit/media/" + mediaIndex; })
                ],
                "media-modal"
            );
        }
        else
            modal.Show("Error", "There was an error getting the media information. (Invalid Media_ID)");
    })

    .fail(function()
    {
        modal.Show("Error", "There was an error getting the media information.");
    })

    .always(function()
    {
        //console.log("Remove loading things here I guess.");
    });
}

// Menu Hambuger stuff.
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