<?php
class PageButtons
{

	public $totalPages;
	public $currentPage;
	public $pageRange;
	function __construct($totalPages, $currentPage, $pageRange = 7)
	{
		$this->totalPages = $totalPages;
		$this->currentPage = $currentPage;
		$this->pageRange = $pageRange;
	}

	function DoButtons()
	{
		// Output the container div and the previous button.
		?>
		<div class="leum-page-buttons pure-button-group" role="group" aria-label="page number">
			<a class="pure-button <?php if(!$this->InBounds($this->currentPage - 1)) echo "pure-button-disabled"; ?>" href="<?php $this->GetHref($this->currentPage - 1); ?>" aria-label="previous page">
				<i class="fa fa-arrow-left"></i>
			</a>
			<?php
			// Loop over each page in the total pages. Check to see if the
			// button should be shown. Also add 'pure-button-primary' if the
			// button is the same as the current page.
			for ($i=0; $i < $this->totalPages; $i++)
			{
				
				if($this->ShowButton($i + 1))
				{

				?>
				<a class="pure-button <?php if($this->currentPage == $i + 1) echo "pure-button-primary" ?>" href="<?php $this->GetHref($i + 1) ?>"><?php echo $i + 1 ?></a>
				<?php

				}
			}
			
			// Output next button and close the container div.
			?>
			<a class="pure-button <?php if(!$this->InBounds($this->currentPage + 1)) echo "pure-button-disabled"; ?>" href="<?php $this->GetHref($this->currentPage + 1); ?>" aria-label="next page">
				<i class="fa fa-arrow-right"></i>
			</a>
		</div>
		<?php

	}

	function GetHref($number)
	{
		// https://stackoverflow.com/a/23670653
		$query = $_GET;
		unset($query['request']);
		$query['page'] = $number;
		$query_result = http_build_query($query);
		echo "?$query_result";
	}
	function InBounds($number)
	{
		// Check to see if the page number is within bounds.
		return $number <= $this->totalPages && $number > 0;
	}
	function ShowButton($number)
	{
		// Only return true if the $number is within the pageRange.
		if($number > $this->currentPage - $this->pageRange / 2&&
			$number < $this->currentPage + $this->pageRange / 2)
			return true;

		return false;
	}
}
?>