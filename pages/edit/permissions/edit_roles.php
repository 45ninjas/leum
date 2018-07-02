<?php
require_once SYS_ROOT . "/core/user-permission/permission.php";
require_once SYS_ROOT . "/core/user-permission/role.php";
/**
* View Tags
*/
class edit_roles implements IPage
{
	private $role;
	private $title;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->PermissionCheck("admin-pages", "permissions-edit");
		$this->title = "Edit Role";

		$leum->RequireResource('/resources/css/leum-edit.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('/resources/css/leum-edit.css') . '">');

		// Apply changes if they exist.
		if(isset($_POST['slug']))
		{
			if(isset($_POST['delete']) && isset($_POST['role_id']))
			{
				Role::DeleteSingle($dbc, $_POST['role_id']);
			}
			else
			{
				$role = new Role();
				$role->slug = $_POST['slug'];

				if(isset($_POST['description']))
					$role->description = $_POST['description'];

				if(isset($_POST['role_id']))
					$role->role_id = $_POST['role_id'];

				Role::InsertSingle($dbc, $role, $role->role_id);
			}
		}

		if(isset($arguments[0]))
		{
			// Looks like we are editing an existing role.
			$roleSlug = $arguments[0];
			if(!$this->role = Role::GetSingle($dbc, $roleSlug))
				$leum->Show404Page("Role $roleSlug does not exist.");
		}
		else
		{
			// Looks like we are creating a new role.
			$this->title = "Create Role";
		}

		$leum->SetTitle($this->title);
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1><?=$this->title;?></h1>
		<div class="pure-menu pure-menu-horizontal">
			<ul class="pure-menu">
				<li class="pure-menu-item"><a href="<?=ROOT."/edit/permissions";?>" class="pure-menu-link">&#10094; Permissions</a></li>
				<li class="pure-menu-item"><a href="<?=ROOT."/edit/permissions/roles/new";?>" class="pure-menu-link">New Role <i class="fa fa-plus"></i></a></li>
			</ul>
		</div>
	</div>

	<div class="content">
		<form method="POST" class="pure-form pure-form-stacked" method="post">
			<?php if(isset($this->role)): ?>
			<label for="role_id">Role ID</label>
			<input class="pure-input-1" readonly type="text" id="role_id" name="role_id" <?php if(isset($this->role)) $this->EchoValue($this->role->role_id); ?>>
			<?php endif; ?>
			<label for="slug">Slug</label>
			<input class="pure-input-1" type="text" id="slug" name="slug" placeholder="slug" <?php if(isset($this->role)) $this->EchoValue($this->role->slug); ?>>
			<label for="description">Description</label>
			<textarea class="pure-input-1" name="description" id="description" placeholder="description" rows="3" maxlength="255"><?php if(isset($this->role)) echo $this->role->description; ?></textarea>
			<input type="submit" class="pure-button pure-button-primary" name="submit" value="Apply">
			<?php if(isset($this->role)): ?>
			<input type="submit" class="pure-button button-delete" name="delete" value="Delete" id="delete" onclick="javascript:return DeleteConfirm('<?=$this->role->slug?>')">
			<?php endif; ?>
		</form>
	</div>
</div>

<script type="text/javascript">
	function DeleteConfirm(title)
	{
		return(window.confirm("Are you sure you want to delete the '" + title + "' role? This Cannot be undone."));
	}
</script>

<?php }

function EchoValue($value)
{
	if($this->role != null)
		echo "value=\"$value\"";
}

}
?>
