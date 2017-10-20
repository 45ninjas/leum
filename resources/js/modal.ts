module Modal
{
	class Modal {
		public modal;
		constructor(title: string, content: any, buttons: Array<Button>)
		{
			var tc = (document.querySelector("#modal-template") as HTMLTemplateElement).content;
			// Create the modal.
			var clone = document.importNode(tc, true);

			clone.querySelector(".modal-title").innerHTML = title;
			clone.querySelector(".content").appendChild(content);

			// Do buttons and stuff.

			for (var i = buttons.length - 1; i >= 0; i--) {
				buttons[i].AppendButton(clone.querySelector(".modal-footer"), i);
			}

			clone.querySelector(".modal-footer").addEventListener("click", function(e)
			{
				var target = e.target as Element;
				if(target && target.matches(".modal-button"))
				{
					var number = parseInt(target.id.substr(13));
					console.log(`The ${buttons[number].text} [${number}] button was pressed`);
					buttons[number].click();
				}
			});

			document.body.appendChild(clone);
			console.log(document.body.lastChild);
		}

		public Close()
		{
			document.body.removeChild(this.modal);

		}
	}
	class Button
	{
		public text: string;
		public classes: string;
		public click:Function;

		public constructor(text: string, classes: string, click:Function = null)
		{
			this.text = text;
			this.classes = classes;
			this.click = click;
		}

		public AppendButton(target:Element, index:number)
		{
			var button = document.createElement("button");
			var text = document.createTextNode(this.text);
			button.appendChild(text);
			button.className = this.classes + " pure-button modal-button";
			button.id += "modal-button-" + index;

			target.appendChild(button);

			//if(this.click == null || this.click == undefined)
			//	button.className += " pure-button-disabled";
		}
	}

	// A test modal.
	//var test:Modal = new Modal(
	//	"Hello World",
	//	"This is a test to see if the modal system works",
	//	[new Button("Ok","pure-button-default", function() { test.Close(); }), new Button("Close", "", function() {test.Close(); })]
	//);
}