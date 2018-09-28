<?php
include_once SYS_ROOT . "/leum/plugins/shows/controllers/show_controller.php";
include_once SYS_ROOT . "/leum/plugins/shows/views/show_view.php";
class edit_shows implements IPage
{
	private $show;
	private $header;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$title = "Edit Show";

		if(isset($_POST['set-media']))
		{
			try
			{
				$this->show = ShowController::ObtainData(INPUT_POST);
				if(isset($_POST['id']))
					ShowController::UpdateShow($this->show)
				else
					ShowController::CreateShow($this->show);
			}
			catch (Eexception $e)
			{
				
			}
		}

		if(isset($arguments[0]))
		{
			$this->show = Show::Get($dbc, $arguments[0]);
			if($this->show)
			{
				$title = $this->show->title;
				$this->show->GetEpisodes($dbc);
			}
			else
				$leum->Show404Page();
		}

		$menu = Front::GetWidget('menu', ['items'=>[
			['href' => ROOT . "/shows/add-new/", 'content' => 'Add Show']
		]]);
		$this->header = Front::GetWidget('page_header',['title'=>$title, 'menu'=>$menu]);
		$leum->SetTitle($title);

		if(isset($_POST['set-media']))
		{
			$controller = new ShowController();
			$controller->ObtainData();
		}
	}
	public function Content()
	{ ?>
<div class="main">
	<?php
	$this->header->Show();
	ShowView::EditForm($this->show);
	?>
</div>

<?php

	}
}
?>