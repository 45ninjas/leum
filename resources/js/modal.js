 modalCounter = 0;

// The modal class.
//var Modal = (function ()
function Modal()
{
    var index = modalCounter ++;
    var isShown = false;

    this.Show = function(title, content, buttons, modalClass, autoBackground = true)
    {
        // Remove the modal if it's already been shown.
        // TODO: Make it update the content or title if only the content or title has changed.
        if(isShown)
            InternalClose(false);

        isShown = true;

        // Set the background.
        if(autoBackground)
            SetModalBack(true);

        // Get the template and clone it.
        var template = document.querySelector("#modal-template").content;
        var clone = document.importNode(template, true);

        // Add the index of this modal to it's id.
        clone.querySelector(".modal").id = "modal-index-" +  index;

        if(modalClass !== undefined)
            clone.querySelector(".modal").className += " " + modalClass;

        SetTitle(clone, title);

        // Set the content. If the content is a string create a new text node.
        if(typeof(content) === 'string')
            SetContent(clone, document.createTextNode(content));
        else
            SetContent(clone, content);

        // Add the buttons. If there was no buttons add a close button.
        if(buttons === undefined)
            SetButtons(clone, new Button("Close", "pure-button-default", function(){ InternalClose(autoBackground); }));
        else
            SetButtons(clone, buttons);

        // Finally, show the clone.
        document.body.appendChild(clone);
    }

    this.Close = function(autoBackground = true)
    {
        InternalClose(autoBackground);
    }
    function InternalClose(autoBackground)
    {
        var modal = document.querySelector(".modal#modal-index-" + index);
        document.body.removeChild(modal);

        if(autoBackground)
            SetModalBack(false);
    }

    function SetButtons(clone, buttons)
    {
        // If there is only one button. Make it into an array.
        if (!Array.isArray(buttons))
            buttons = [buttons];

        // Add all the buttons in the array to the modal-footer element.
        for (var i = 0; i < buttons.length; i++)
            buttons[i].AppendButton(clone.querySelector(".modal-footer"), i);

        // Add the event listener so we can figure out what we need to do.
        clone.querySelector(".modal-footer").addEventListener("click", function(e) {
            var target = e.target;
            if(target && target.matches(".modal-button"))
            {
                var btnIndex = parseInt(target.id.substring(13));
                buttons[btnIndex].click();
            }
        });
    }
    function SetTitle(clone, title)
    {
        clone.querySelector(".modal-title").innerHTML = title;
    }
    function SetContent(clone, content)
    {
        clone.querySelector(".content").appendChild(content);
    }

}
//    return Modal;
//}());

var Button = (function ()
{
    function Button(text, classes, click)
    {
        if (click === void 0)
            click = null;

        this.text = text;
        this.classes = classes;
        this.click = click;
    }

    Button.prototype.AppendButton = function (target, index)
    {
        // Create the button and text elements/
        var button = document.createElement("button");
        var text = document.createTextNode(this.text);
        button.appendChild(text);

        // Set the classes of the button.
        button.className = this.classes + " pure-button modal-button";
        
        // Show a disabled button if we have nothing to do.
        if(this.click === undefined || this.click === null)
            button.className += " pure-button-disabled";

        button.id += "modal-button-" + index;

        target.appendChild(button);
    };
    return Button;
}());

// Toggle Modal Background.
function SetModalBack(value)
{
    var back = document.querySelector("#modal-background");
    if(value == true)
        back.removeAttribute("hidden");
    else
        back.setAttribute("hidden","");
}