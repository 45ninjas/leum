var activeViewer = new MediaViewer();
function GetRootDir()
{
    return document.head.querySelector("[property=site-root]").content;
}

document.addEventListener("DOMContentLoaded", function ()
{
	activeViewer.IndexChanged();
	$(window).bind('hashchange', function()
	{
		activeViewer.IndexChanged();
	});
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
				Show(index);
		}
		else
			Close();
	}
	function Show(mediaIndex)
	{
		viewer = document.querySelector("#media-viewer");
		var url = GetRootDir() + "/api/v2/media/" + mediaIndex + "?usage=viewer";
		var jqxhr = $.getJSON(url, function(data)
		{
			if(data != false)
			{
				SetTitle(data["title"]);
				SetContent(data["content"]);
				SetTags(data["tag slugs"]);
				SetEditLink(data["edit link"]);
				
				SetModalBack(true);
				viewer.removeAttribute("hidden","");
			}
			else
			{
				var modal = new Modal();
				modal.Show("Error", "There was an error getting the media information. (Incorrect Index)");
			}
		}).fail(function()
		{
			console.log();
			var modal = new Modal();
			modal.Show("Error", "There was an error getting the media information.");
		});
	}
	function Close()
	{
		viewer = document.querySelector("#media-viewer");
		SetModalBack(false);
		SetContent(null);
		viewer.setAttribute("hidden","");
	}
	function SetTitle(title)
	{
		if(title == null)
			viewer.querySelector("#media-title").content = "";
		else
			viewer.querySelector("#media-title").content = title;
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
}