<?php
require_once SYS_ROOT . "/core/media.php";
/**
* View Media
*/
class Page
{
	public $title = "Edit Media";
	private $mediaItems = array();
	private $total;
	public function __construct($arguments)
	{
		$db = Leum::Instance()->GetDatabase();
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$item = Media::GetSingle($db, $arguments[0]);
			if($item)
				$this->mediaItems[] = $item;
		}
		else
			$this->mediaItems = Media::GetAll($db);	

		$this->total = count($this->mediaItems);
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>Media</h1>
		<div class="pure-menu pure-menu-horizontal">
			<ul class="pure-menu">
				<li class="pure-menu-item"><a href="<?=ROOT."/edit/";?>" class="pure-menu-link">&#10094; Edit</a></li>
				<li class="pure-menu-item"><a href="<?=ROOT."/edit/media/new";?>" class="pure-menu-link">Create New</a></li>
				<li class="pure-menu-item"><a href="<?=ROOT."/utils/thumbnails-all.php";?>" class="pure-menu-link">All Thumbnails</a></li>
				<li class="pure-menu-item"><a href="<?=ROOT."/utils/tags-all.php";?>" class="pure-menu-link">All Tags</a></li>
				<li class="pure-menu-item"><a href="<?=ROOT."/utils/importer.php";?>" class="pure-menu-link">Import</a></li>
			</ul>
		</div>
	</div>
	<div class="content">
		<?php
		if(isset($this->mediaItems) && $this->total > 0)
		{
		?>
		<p>Found <?php echo $this->total; ?> results.</p>
		<table class="pure-table pure-table-striped full-width media-edit">
			<thead>
				<tr>
					<th>ID</th>
					<th>Title</th>
					<th>Path</th>
					<th>Date Created</th>
					<th>Tasks</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				foreach ($this->mediaItems as $item)
				{
				?>
				<tr>
					<td class= "id" ><?php echo $item->media_id; ?></td>
					<td class="title" title="<?=$item->title?>"><?php echo $item->title; ?></td>
					<td class="path" title="<?=$item->path?>"><?php echo $item->path; ?></td>
					<td class="date" ><?php echo $item->GetDate("Y-m-d"); ?></td>
					<td>
						<a class="pure-button button-compact" href="<?php echo ROOT."/edit/media/$item->media_id"; ?>">
							<i class="fa fa-edit"></i>
						</a>
						<a class="pure-button button-delete button-compact" data-title="<?php echo $item->title; ?>" href="<?php echo ROOT."/api/v1/media/$item->media_id"; ?>">
							<i class="fa fa-trash"></i>
						</a>
						<a class="pure-button pure-button-primary button-compact" href="<?php echo ROOT."/view/$item->media_id"; ?>">
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
