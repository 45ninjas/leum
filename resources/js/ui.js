window.onload = function()
{
    // get the dropown and button to open the dropdown.
    var dropdown = document.querySelector("#user-dropdown");
    var button = document.querySelector("#user-dropdown-button");

    // When the user presses the dropdown button remove hidden from the dropdown.
    button.onclick = function(){ ShowUserDropdown(true); };

    // Did the dropdown contain the click.
    function CheckClick(e)
    {
        if(!dropdown.contains(e.target))
            ShowUserDropdown(false);
    }
    // Sets the hidden attribute and adds/removes whole document click event.
    function ShowUserDropdown(value)
    {
        if(value)
        {
            // Open the dropdown.
            dropdown.removeAttribute("hidden");
            document.addEventListener('click', CheckClick, true);
        }
        else
        {
            dropdown.setAttribute("hidden", "yes");
            document.removeEventListener('click', CheckClick, true);
        }
    }
}
function GetRootDir()
{
    return document.head.querySelector("[property=site-root]").content;
}

function ParseAPIResponse($response)
{

}

/*// Menu Hambuger stuff.
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

}(this, this.document));*/