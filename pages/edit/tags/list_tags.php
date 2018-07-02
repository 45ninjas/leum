<?php
require_once SYS_ROOT . "/core/tag.php";
/**
* View Tags
*/
class list_tags implements IPage
{
	private $tagItems = array();
	private $total;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->PermissionCheck("admin-pages", "tags-edit");
		$leum->SetTitle("Tags");
		$this->tagItems = Tag::GetAll($dbc);	
		$this->total = count($this->tagItems);
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>Tag</h1>
		<div class="pure-menu pure-menu-horizontal">
			<ul class="pure-menu">
				<li class="pure-menu-item"><a href="<?=ROOT."/edit/";?>" class="pure-menu-link">&#10094; Edit</a></li>
				<li class="pure-menu-item"><a href="<?=ROOT."/edit/tag/new";?>" class="pure-menu-link">Create New <i class="fa fa-plus"></i></a></li>
				<li class="pure-menu-item"><a href="<?=ROOT."/utils/tags-all.php";?>" class="pure-menu-link">Import All Tags <i class="fa fa-download"></i></a></li>
			</ul>
		</div>
	</div>
	<div class="content">
		<?php
		if(isset($this->tagItems) && $this->total > 0)
		{
		?>
		<p>Found <?php echo $this->total; ?> tags.</p>
		<table class="pure-table pure-table-striped full-width">
			<thead>
				<tr>
					<th>ID</th>
					<th>Slug</th>
					<th>Item Count</th>
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
					<td><?php echo $item->slug; ?></td>
					<td><?php echo $item->count; ?></td>
					<td>
						<a class="pure-button button-compact" href="<?php echo ROOT."/edit/tag/$item->tag_id"; ?>">
							<i class="fa fa-edit"></i>
						</a>
						<a class="pure-button button-delete button-compact" data-title="<?php echo $item->title; ?>" href="<?php echo ROOT."/api/v1/tag/$item->tag_id"; ?>">
							<i class="fa fa-trash"></i>
						</a>
						<a class="pure-button pure-button-primary button-compact" href="<?php echo ROOT."/browse/?q=$item->slug"; ?>">
							<i class="fa fa-eye"></i>
						</a>
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
