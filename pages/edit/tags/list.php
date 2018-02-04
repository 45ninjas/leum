<?php
require_once SYS_ROOT . "/core/tag.php";
/**
* View Tags
*/
class Page
{
	public $title = "Edit Tags";
	private $tagItems = array();
	private $total;
	public function __construct($arguments)
	{
		$db = Leum::Instance()->GetDatabase();
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$item = Tag::GetSingle($db, $arguments[0]);
			if($item)
				$this->tagItems[] = $item;
		}
		else
			$this->tagItems = Tag::GetAll($db);	

		$this->total = count($this->tagItems);
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>Tag</h1>
	</div>
	<div class="content">
		<a class="pure-button pure-button-primary" href="<?php echo ROOT."/edit/tag/new/" ?>">
			Create New Tag
			<i class="fa fa-plus"></i>
		</a>
		<?php
		if(isset($this->tagItems) && $this->total > 0)
		{
		?>
		<p>Found <?php echo $this->total; ?> results.</p>
		<table class="pure-table pure-table-striped full-width">
			<thead>
				<tr>
					<th>ID</th>
					<th>Title</th>
					<th>Slug</th>
					<th>Tasks</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				foreach ($this->tagItems as $item)
				{
				?>
				<tr>
					<td><?php echo $item->tag_id; ?></td>
					<td><?php echo $item->title; ?></td>
					<td><?php echo $item->slug; ?></td>
					<td>
						<a class="pure-button button-compact" href="<?php echo ROOT."/edit/tag/$item->tag_id"; ?>">
							<i class="fa fa-edit"></i>
							Edit
						</a>
						<a class="pure-button button-delete button-compact" data-title="<?php echo $item->title; ?>" href="<?php echo ROOT."/api/v1/tag/$item->tag_id"; ?>">
							<i class="fa fa-trash"></i>
							Delete
						</a>
<!-- 						<a class="pure-button pure-button-primary button-compact" href="<?php echo ROOT."/view/$item->tag_id"; ?>">
							<i class="fa fa-eye"></i>
							View
						</a> -->
					</td>
				</tr>
				<?php
				}
			?>		
			</tbody>
		</table>
		<script type="text/javascript" src="<?php Asset("/resources/js/deleter.js");?>"></script>
		<?php
		}
		else
			echo "Zero results found.";
		?>
	</div>
</div>

<?php }
}
?>
