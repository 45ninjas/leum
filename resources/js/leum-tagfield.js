$(function()
{
	// ==== Removing tags ====
	$('.tag-delete').click(function(e)
	{
		e.preventDefault();

		// Get the index from the hidden input field's value.
		var tagIndex = $(this).parent().find('input').val();

		// Remove the item from the list.
		$(this).parent().remove();
	});

	var autocompleteOptions =
	{
		url: function(phrase)
		{
			return "leum/api/v1/find/tags?query=" + phrase;
		},
		getValue: "title",
		requestDelay: 500,

		list:
		{
			onChooseEvent: function()
			{
				var tag = $("#tag-input").getSelectedItemData();
				// Create a new tag.

				var t = document.querySelector("#tag-template");
				t.content.querySelector("input").value = tag.tag_id;
				t.content.querySelector("span").textContent = tag.title;
				
				var tagField = document.querySelector("#tagfield");
				var clone = document.importNode(t.content, true);
				tagField.appendChild(clone);
			}
		}
	};
	$('#tag-input').easyAutocomplete(autocompleteOptions);


});