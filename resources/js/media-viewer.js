var activeViewer = new MediaViewer();
var allIndexes = [];
var activeIndexIndex = 0;
var tagEditor;
function GetRootDir()
{
    return document.head.querySelector("[property=site-root]").content;
}

document.addEventListener("DOMContentLoaded", function ()
{
	tagEditor = new TagEditor(true);
	// Set the close button.
	var closeButton = document.querySelector("#media-viewer-close");
	closeButton.addEventListener("click", function()
	{
		activeViewer.Close();
	});

	// Register media click callbacks.
	var itemContainer = document.querySelector(".main .items");
	var items = itemContainer.children;
	for (var i = 0; i < items.length; i++)
	{
		allIndexes.push(parseInt(items[i].dataset.mediaIndex));
	}

	itemContainer.addEventListener("click", function(e)
	{
		var target = e.target.parentElement;
		if(target && target.matches(".item-tile"))
		{
			var index = target.dataset.mediaIndex;
			activeViewer.Show(index);
		}
	});

	var nextButton = document.querySelector("#media-viewer-next");
	var prevButton = document.querySelector("#media-viewer-prev");

	nextButton.addEventListener("click", function()
	{
		activeViewer.Show(allIndexes[activeIndexIndex + 1]);
	});
	prevButton.addEventListener("click", function()
	{
		activeViewer.Show(allIndexes[activeIndexIndex - 1]);
	});

	activeViewer.IndexChanged();
});

function MediaViewer()
{
	var viewer;
	var lastIndex;
	this.IndexChanged = function()
	{
		var index = GetMediaItemIndex();
		if(index != null)
		{
			if(lastIndex != index)
				this.Show(index);
		}
		else
			this.Close();
	}
	this.Show = function(mediaIndex)
	{
		if(typeof mediaIndex === 'string' || mediaIndex instanceof String)
			mediaIndex = parseInt(mediaIndex);

		activeIndexIndex = allIndexes.indexOf(parseInt(mediaIndex));

		location.hash = "#view" + mediaIndex;

		viewer = document.querySelector("#media-viewer");
		var url = GetRootDir() + "/api/v2/media/" + mediaIndex + "?usage=viewer";
		var jqxhr = $.getJSON(url, function(data)
		{
			if(data != false)
			{
				SetTitle(data["title"]);
				SetContent(data["content"]);
				SetEditLink(data["edit link"]);

				tagEditor.SetTags(data['tags']);
				tagEditor.SetMediaId(mediaIndex);

				SetHidden(false);
				SetModalBack(true);
				UpdateButtons();
			}
			else
			{
				var modal = new Modal();
				modal.Show("Error", "There was an error getting the media information. (Incorrect Index)");
			}
		}).fail(function()
		{
			var modal = new Modal();
			modal.Show("Error", "There was an error getting the media information.");
		});
	}
	this.Close = function()
	{
		viewer = document.querySelector("#media-viewer");
		SetModalBack(false);
		SetContent(null);
		SetHidden(true);
		tagEditor.SetMediaId(null);
	}
	function SetTitle(title)
	{
		if(title == null)
			viewer.querySelector("#media-title").innerHTML = "";
		else
			viewer.querySelector("#media-title").innerHTML = title;
	}
	function SetContent(content)
	{
		if(content == null)
		{
			var contentNode = viewer.querySelector(".media-item");
			if(contentNode != null)
				viewer.removeChild(contentNode);
		}
		else
		{
			if(content != null)
				SetContent(null);

			var footer = viewer.querySelector(".footer");
			var template = document.createElement('template');
			template.innerHTML = content;
			contentNode = template.content;
			viewer.insertBefore(template.content, footer);
		}
	}
	function SetTags(tagSlugs)
	{
		var slugs = viewer.querySelector(".tags");
		var editLink = viewer.querySelector("#media-edit-link");
		if(tagSlugs == null)
		{
			var children = [].slice.call(slugs.children);
			children.forEach( function(element, index)
			{
				if(element != editLink)
					slugs.removeChild(element);		
			});
		}
		else
		{
			SetTags(null);
			for (var i = 0; i < tagSlugs.length; i++)
			{
				var tagNode = document.createElement("li");
				tagNode.appendChild(document.createTextNode(tagSlugs[i]));
				slugs.appendChild(tagNode);
			}
		}
	}
	function SetEditLink(link)
	{
		if(link == null)
			viewer.querySelector("#media-edit-link").href = "";
		else
			viewer.querySelector("#media-edit-link").href = link;
	}
	function GetMediaItemIndex()
	{
    	var prefix = "#view";
    	var hash = location.hash;

    	if(hash.startsWith(prefix))
        	return parseInt(hash.substring(prefix.length), 10);
    	else
        	return null;
	}
	function SetHidden(hidden)
	{
		if(!hidden)
		{
			viewer.removeAttribute("hidden");
			document.querySelector("#media-viewer-close").removeAttribute("hidden");
			document.querySelector("#media-viewer-next").removeAttribute("hidden");
			document.querySelector("#media-viewer-prev").removeAttribute("hidden");
		}
		else
		{
			viewer.setAttribute("hidden","");
			document.querySelector("#media-viewer-close").setAttribute("hidden","");
			document.querySelector("#media-viewer-next").setAttribute("hidden","");
			document.querySelector("#media-viewer-prev").setAttribute("hidden","");
		}
	}
	function UpdateButtons()
	{
		if(activeIndexIndex < 1)
			document.querySelector("#media-viewer-prev").setAttribute("hidden","");
		if(activeIndexIndex == allIndexes.length - 1)
			document.querySelector("#media-viewer-next").setAttribute("hidden","");
	}
}