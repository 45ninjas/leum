<?php
/**
* Edit tag page
*/
require_once SYS_ROOT . "/core/tag.php";
class Page
{
	public $title = "Create Tag";
	private $tagItem;
	private $modify = false;
	private $db;

	public function __construct($arguments)
	{
		$this->db = Leum::Instance()->GetDatabase();

		$tagId = null;
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$this->modify = true;
			$tagId = $arguments[0];
		}

		if(isset($_POST['modify']) && isset($_POST['slug']))
		{
			$this->tagItem = new Tag();
			$this->tagItem->slug = $_POST['slug'];
			Tag::InsertSingle($this->db, $this->tagItem,$tagId);
		}

		if(isset($tagId))
		{
			$this->tagItem = Tag::GetSingle($this->db, $tagId);
			$this->title = "Edit Tag";

			if($this->tagItem == null)
			{
				Leum::Instance()->Show404Page("Tag $tagId does not exist in the database.");
				return;
			}
		}
		else
			$this->tagItem = new Tag();
	}
	public function Content()
	{ ?>

	<div class="main">
		<div class="header">
			<h1><?php echo $this->title; ?></h1>
		</div>
		<div class="content">
			<form class="pure-form pure-form-stacked" method="post">
				<fieldset>
					<label for="slug">Slug</label>
					<input tabindex="2" class="pure-u-1" type="text" name="slug" id="slug" placeholder="Slug" <?php $this->EchoValue($this->tagItem->slug); ?>>
				</fieldset>
				<button tabindex="3" type="submit" name="modify" class="pure-button pure-button-primary"><?php if($this->modify) echo "Apply"; else echo "Create";?></button>
				<?php if($this->modify): ?>
				<a class="pure-button button-delete" data-title="<?php echo $this->tagItem->title; ?>" href="<?php echo ROOT."/api/v2/tags/" .$this->tagItem->tag_id; ?>">
					<i class="fa fa-trash"></i>
					Delete
				</a>
				<?php endif; ?>
				<script type="text/javascript" src="<?php Asset("/resources/js/deleter.js");?>"></script>
			</form>
		</div>
	</div>

<?php }
	function EchoValue($value)
	{
		if($this->modify)
		echo "value=\"$value\"";
	}
}
?>
