var lastModalInex = 0;
var Modal = /** @class */ (function () {
    var modalIndex = lastModalInex;
    function Modal(title, content, buttons)
    {
        lastModalInex ++;
        var tc = document.querySelector("#modal-template").content;
        // Create the modal.
        var clone = document.importNode(tc, true);

        clone.querySelector(".modal").id = "modal-index-" + modalIndex;
        clone.querySelector(".modal-title").innerHTML = title;
        clone.querySelector(".content").appendChild(content);
        // Do buttons and stuff.
        for (var i = buttons.length - 1; i >= 0; i--) {
            buttons[i].AppendButton(clone.querySelector(".modal-footer"), i);
        }
        clone.querySelector(".modal-footer").addEventListener("click", function (e) {
            var target = e.target;
            if (target && target.matches(".modal-button")) {
                var number = parseInt(target.id.substr(13));
                console.log("The " + buttons[number].text + " [" + number + "] button was pressed");
                buttons[number].click();
            }
        });
        var thing = document.body.appendChild(clone);
        console.log(thing);

        SetModalBack(true);
    }
    Modal.prototype.Close = function ()
    {
        SetModalBack(false);
        var modal = document.querySelector(".modal#modal-index-" + modalIndex);
        document.body.removeChild(modal);
    };
    return Modal;
}());
var Button = /** @class */ (function () {
    function Button(text, classes, click) {
        if (click === void 0) { click = null; }
        this.text = text;
        this.classes = classes;
        this.click = click;
    }
    Button.prototype.AppendButton = function (target, index) {
        var button = document.createElement("button");
        var text = document.createTextNode(this.text);
        button.appendChild(text);
        button.className = this.classes + " pure-button modal-button";
        button.id += "modal-button-" + index;
        target.appendChild(button);
        //if(this.click == null || this.click == undefined)
        //	button.className += " pure-button-disabled";
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