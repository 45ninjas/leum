function TagEditor(textBox, tagField, tagInput, suggestionBox)
{
	var mediaId;

	this.SetMediaId = function(id)
	{
		mediaId = id;
	}

	var autoUpdate = false;
	var allowNew = false;
	var resultLimit = 10;
	var seperator = ',';
	var apiUrl = document.head.querySelector("[property=api-url]").content;	

	var tagString;

	var tags = new Array();

	var textInput = textBox;
	var tagInput = tagInput;
	var sugestionBox = suggestionBox;
	var field = tagField;


	// Text inputs.
	var timer;
	var topResult;

	var editor = this;

	textInput.addEventListener("input", function(e)
	{
		if(e.keyCode == 13)
		{
			e.preventDefault();

			if(topResult != null)
			{
				editor.ChooseSuggestion(topResult);
				textInput.value = "";
				return;
			}
		}

		clearTimeout(timer);
		timer = setTimeout(function()
		{
			if(textInput.value == "")
				ClearSugestions();
			else
				DoTextSearch();
		},
		600);

		if(e.keyCode == 13)
			return false;
	});

	field.addEventListener("click", function(e)
	{
		if(e.target.parentElement.matches(".remove-button"))
		{
			var slug = e.target.parentElement.parentElement.dataset.slug;
			editor.RemoveTag(slug);
		}
	});

	sugestionBox.addEventListener("click", function(e)
	{
		var slug = e.target.dataset.slug;
		if(slug == undefined)
			slug = e.target.parentElement.dataset.slug;

		if(slug != undefined)
		{
			editor.ChooseSuggestion(slug);
			textInput.value = "";
		}
	});

	function DoTextSearch()
	{
		var url = apiUrl + "/v2/tags?limit=" + editor.resultLimit + "&q=" + textInput.value;

		// TODO: Remove jquery dependency.
		$.getJSON(url, function(data)
		{
			ClearSugestions();
			if(data !== false)
			{
				ShowSugestions(data);
			}
		}).fail(function()
		{
			ClearSugestions();
		});
	}
	this.ChooseSuggestion = function(slug)
	{
		this.AddTag(slug);
		ClearSugestions();
	}
	function AddSugestion(sugestion)
	{
		var item = document.createElement("li");

		var slug = document.createElement("span");
		slug.classList.add("tag-slug");
		slug.appendChild(document.createTextNode(sugestion['slug']));

		var count = document.createElement("span");
		count.classList.add("tag-count");
		count.appendChild(document.createTextNode(sugestion['count']));

		item.dataset.slug = sugestion['slug'];

		// item.appendChild(id);
		item.appendChild(slug);
		item.appendChild(count);

		sugestionBox.appendChild(item);
	}
	function ShowSugestions(data)
	{
		sugestionBox.removeAttribute("hidden");

		var exists = false;
		var input = textInput.value.toLowerCase();

		for (var i = 0; i < data.length; i++)
		{
			if(i == 0)
				topResult = data[i]['slug'];
			AddSugestion(data[i]);

			if(editor.allowNew && data[i].slug == input)
				exists = true;
		}

		if(editor.allowNew && !exists)
			AddSugestion({ tag_id: -1, slug: input, count: "Create New" });

	}
	function ClearSugestions()
	{
		topResult = null;
		sugestionBox.setAttribute("hidden", "");
		while (sugestionBox.firstChild)
		{
			sugestionBox.removeChild(sugestionBox.firstChild);
		}
	}
	this.SetTags = function(slugs)
	{
		tags = new Array();

		slugs.forEach( function(element)
		{
			tags.push(element);
		});

		this.UpdateField();
	}

    this.AddTag = function(slug)
    { 
    	// Make sure the tag is not in the array.
    	if(!InArray(tags, slug))
    	{
	    	// Update the tags array to store the new tag.
	    	tags.push(slug);
   			TagsChanged(editor);
	    }
	    else
    		console.log("Slug " + slug + " already exists.");
    }
    this.CreateTagElement = function(slug)
    {
    	// Add some elements to the document to show that the new tag has been added.
    	var tagElement = document.createElement("span");
    	tagElement.classList.add("tag");
    	tagElement.classList.add("pure-button");
    	tagElement.dataset.slug = slug;

    	// The actual text of the tag.
    	var tagText = document.createElement("span");
    	tagText.appendChild(document.createTextNode(slug));
    	tagElement.appendChild(tagText);

    	// The close/remove button on the tag.
    	var tagRemove = document.createElement("span");
    	tagRemove.innerHTML = "<i class=\"fa fa-close\"></i>";
    	tagRemove.classList.add("remove-button");
    	tagElement.appendChild(tagRemove);

    	field.appendChild(tagElement);
    }
    this.RemoveTag = function(slug)
    {
    	console.log("Removing: " +  slug);
    	if(InArray(tags, slug))
    	{
    		// Remove the slug from the list of tags.
    		var index = tags.indexOf(slug);
    		tags.splice(index, 1);
    		TagsChanged(editor);
    	}
    	else
    		console.log("Slug " + slug + " does not exist, can't remove.");
    }
    this.UpdateField = function()
    {
    	var currentSlugs = new Array();

    	// Figure out what slugs already exist as 'visual' tags. Remove the ones
    	// that are no longer needed.
    	var tagElements = [].slice.call(field.children);

    	if(tagElements != null)
    	{
	    	tagElements.forEach(function(element)
	    	{
	    		var slug = element.dataset.slug;

	    		if(tags.includes(slug))
	    			currentSlugs.push(slug);
	    		else
	    			field.removeChild(element);
	    	});
	    }

    	// Add all the 'visual' tags that don't exist yet.
    	for (var i = 0; i < tags.length; i++)
    	{
    		if(tags[i] != null && tags[i] != "" && !currentSlugs.includes(tags[i]))
    			this.CreateTagElement(tags[i]);
    	}
    }
    this.Clear = function()
    {
    	for (var i = 0; i < tags.length; i++)
    	{
    		this.RemoveTag(tags[i]);
    	}
    }
    function InArray(array, value)
    {
    	return array.indexOf(value) > -1;
    }

    function TagsChanged(editor)
    {
    	tagString = tags.join(seperator);
    	if(tagInput != null)
    			tagInput.value = tagString;
    	if(editor.autoUpdate)
    	{
    		if(mediaId != null)
    		{
	    		console.log(tagString);
				var url = apiUrl + "/v2/media/" + mediaId;
				$.post(url, {'set-tags':tagString, 'add-new':true}, function(data)
				{
					console.log(data);
					if(data == null)
						return;

					if(data['error'])
					{
						alert(data['error']);
						console.log(data);
						return;
					}

					if(data['tags'])
					{
						tags = data['tags'];
						editor.UpdateField();			
					}

				});
			}
		}
		else
		{
			editor.UpdateField();
		}
    }
}