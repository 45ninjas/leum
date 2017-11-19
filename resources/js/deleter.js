$(function()
{
	$('.button-delete').click(function(e)
	{
		e.preventDefault();
		if(window.confirm("Are you sure you want to delete '" + this.getAttribute('data-title') + "'?"))
		{
			var url= this.href;

			$.ajax({url: url, type: 'DELETE', success: function(result)
				{
					location.reload();
				}});
		}
	});
});