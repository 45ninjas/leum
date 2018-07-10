function SuggestiveInput(sinput)
{
	suggestionInput = sinput;
	input = suggestionInput.querySelector(".user-input");
	suggestion = suggestionInput.querySelector(".suggestion");
	suggestionList = suggestionInput.querySelector(".list");

	var selectedSuggestion;
	suggestions = new Array(
		"Hello World"
	);

	matchEvent = new CustomEvent('suggestion-match');
	// var suggestionChangedEvent = new Event('suggestion-change');

	function SetSuggestion(text)
	{
		if(text != null)
		{
			suggestion.removeAttribute("hidden");
			suggestion.value = text;
			selectedSuggestion = text;
		}
		else
		{
			suggestion.setAttribute("hidden", "");
			selectedSuggestion = null;
			suggestion.value = "";
		}
	}
	this.SetSuggestions = function(newSuggestions)
	{
		suggestions = newSuggestions;
		CheckInput();
	}

	function CheckInput()
	{
		var value = input.value;
		if(value==="")
		{
			SetSuggestion(null);
			return;
		}

		for (var i = 0; i < suggestions.length; i++)
		{
			// Check for exact matches.
			if(suggestions[i] == value)
			{
				// Get more
				suggestionInput.dispatchEvent(new CustomEvent('suggestion-match', {detail: {text: ()=> suggestions[i]}}));
				SetSuggestion(null);
				return;
			}
		}
		// Check for near matches.
		var oneFound = false;
		for (var i = 0; i < suggestions.length; i++)
		{
			if(suggestions[i].substring(0, value.length) == value)
			{
				SetSuggestion(suggestions[i]);
				oneFound = true;
			}
		}

		if(!oneFound)
			SetSuggestion(null);
	}

	input.addEventListener("input", function(e)
	{
		CheckInput();
	});
	return this;
}
