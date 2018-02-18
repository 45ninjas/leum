$(function()
{
	var slugInput = $("#tagfield #tags");
	var slugs = new Array();

	if(slugInput.val() != "")
		var slugs = slugInput.val().split("+");
	
	ChangedTags();

	// ==== Removing tags ====
	$('.tagfield').on("click", ".tag-delete", function(e)
	{
		e.preventDefault();

		// Get the index from the hidden input field's value.
		var tagSlug = $(this).parent().find('input').val();

		// Remove the item from the list.
		$(this).parent().remove();
		
		var tagIndex = slugs.indexOf(tagSlug);
		slugs.splice(tagIndex, 1);

		ChangedTags();
	});

	var autocompleteOptions =
	{
		url: function(phrase)
		{
			return "leum/api/v1/find/tags?query=" + phrase;
		},
		getValue: "slug",
		requestDelay: 500,

		list:
		{
			onChooseEvent: function()
			{
				var tag = $("#tag-input").getSelectedItemData();
				$("#tag-input").val('');
				// Create a new tag.
				CreateTag(tag);

			}
		}
	};
	$('#tag-input').easyAutocomplete(autocompleteOptions);

	function CreateTag(tag)
	{
		var t = document.querySelector("#tag-template");
		t.content.querySelector("input").value = tag.slug;
		t.content.querySelector("span").textContent = tag.slug;
		
		var tagField = document.querySelector("#tagfield");
		var clone = document.importNode(t.content, true);
		tagField.appendChild(clone);	

		slugs.push(tag.slug);
		ChangedTags();

	}

	function ChangedTags()
	{
		slugInput.val(slugs.join('+'));
	}

});