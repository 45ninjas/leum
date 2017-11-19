<?php
require_once('api/v1/media.php');
/**
* View Media
*/
class Page
{
	public $title = "Media";
	private $mediaItems = array();
	private $total;
	public function __construct($arguments)
	{
		$db = Leum::Instance()->GetDatabase();
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$item = Media::Get($db, $arguments[0]);
			if($item)
				$this->mediaItems[] = $item;
		}
		else
			$this->mediaItems = Media::Get($db);	

		$this->total = count($this->mediaItems);
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>Media</h1>
	</div>
	<div class="content">
		<a class="pure-button pure-button-primary" href="<?php echo ROOT."edit/media/new/" ?>">
			<i class="fa fa-new"></i>
			Insert New Media
		</a>
		<?php
		if(isset($this->mediaItems) && $this->total > 0)
		{
		?>
		<p>Found <?php echo $this->total; ?> results.</p>
		<table class="pure-table pure-table-striped full-width">
			<thead>
				<tr>
					<th>ID</th>
					<th>Title</th>
					<th>File</th>
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
					<td><?php echo $item->media_id; ?></td>
					<td><?php echo $item->title; ?></td>
					<td><?php echo $item->path; ?></td>
					<td><?php echo $item->date; ?></td>
					<td>
						<a class="pure-button button-compact" href="<?php echo ROOT."edit/media/$item->media_id"; ?>">
							<i class="fa fa-edit"></i>
							Edit
						</a>
						<a class="pure-button button-delete button-compact" data-title="<?php echo $item->title; ?>" href="<?php echo ROOT."api/v1/media/$item->media_id"; ?>">
							<i class="fa fa-trash"></i>
							Delete
						</a>
						<a class="pure-button pure-button-primary button-compact" href="<?php echo ROOT."view/$item->media_id"; ?>">
							<i class="fa fa-eye"></i>
							View
						</a>
					</td>
				</tr>
				<?php
				}
			?>		
			</tbody>
		</table>
		<script type="text/javascript" src="<?php Asset("resources/js/deleter.js");?>"></script>
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
