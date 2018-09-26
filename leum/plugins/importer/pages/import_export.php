<?php

include SYS_ROOT . '/leum/plugins/importer/export-utility.php';
include SYS_ROOT . '/leum/plugins/importer/import-utility.php';

echo ini_get('upload_max_filesize'), ", " , ini_get('post_max_size');


class Import_Export implements IPage
{
	private $header;
	private $success = false;
	private $file;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		// Make sure the user has permission to create tags and media.
		$leum->PermissionCheck("admin-pages", "media-create", "tags-create", "media-tags");

		// Set the title
		$title = "Import and Export utility";
		$leum->SetTitle($title);

		$args = ['title'=>$title, 'menu'=>Importer::HeaderMenu()];
		$this->header = Front::GetWidget('page_header', $args);

		Message::Create("warning", "Parent and child relations cannot be guaranteed at the moment.", "export");
		Message::Create("warning", "Exporting leum might take a long time", "export");

		if(isset($_POST['download']))
		{
			$this->file = "/backups/temp-backup.json";
			$output = SYS_ROOT . $this->file;
			$exporter = new ExportUtility($output);
			$exporter->ExportLeum();
			Message::Create("success", "Wrote export to $output", "export");
			$this->success = true;
			$this->file = ROOT . $this->file;
		}

		if(isset($_POST['import']))
		{
			$importer = new ImportUtility();
			$importer->ImportFile($_FILES['json']['tmp_name']);
		}

	}
	public function Content()
	{ ?>
	<div class="main">
		<?php $this->header->Show(); ?>
		
		<div class="content">
			<?php Message::ShowMessages("export"); ?>
			<?php Message::ShowMessages("importer"); ?>
			<?php if($this->success): ?>
			<p>The data was exported. <a href="<?=$this->file;?>"><?=$this->file;?></a></p>
			<?php endif; ?>
			<form class="pure-form pure-g" enctype="multipart/form-data" action="" method="post">
				<div class="pure-u-1-2">
					<h3>Export all of leum</h3>
					<input class="pure-button pure-button-primary" type="submit" name="download" value="Export leum">
				</div>
				<div class="pure-u-1-2">
					<h3>Import using a file</h3>
					<input type="file" name="json" accept="application/json">
					<input class="pure-button pure-button-primary" type="submit" name="import" value="Import from file">
				</div>
			</form>
		</div>
	</div>

	<?php
	}}
?>