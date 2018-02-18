function TagEditor()
{
	var mediaId;
	var apiUrl = document.head.querySelector("[property=site-root]").content;

	var textInput = document.querySelector("#tag-editor-input");
	var tagField = document.querySelector("#tag-editor-field");

	function GetLike(query)
	{
		var url = apiUrl + "/tags/like?query=" + query;
		$.getJson(url, function(data)
		{
			if(data != false)
			{
				return data;
			}
		}).fail(function()
		{
			var modal = new Modal();
			modal.show("Error", "There was an error while communicating with the leum api");
		});
	}
}