<?php
require_once SYS_ROOT . "/core/user-permission/permission.php";
require_once SYS_ROOT . "/core/user-permission/role.php";
/**
* View Tags
*/
class Page
{
	private $allRoles;
	private $allPermissions;
	private $title;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$this->title = "Permissions";
		$leum->SetTitle($this->title);

		$leum->RequireResource('/resources/css/leum-edit.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('/resources/css/leum-edit.css') . '">');

		// Check to see if the matrix needs to be updated.
		if(isset($_POST['roles']))
		{
			// Parse the 'roles' array from the form.
			foreach ($_POST['roles'] as $role => $permissions)
			{
				$roleObject = Role::GetSingle($dbc, $role);
				// if($roleObject->slug != 'root')
				$roleObject = RolePermissionMap::SetPermissions($dbc, $roleObject, $permissions);
			}
		}

		// Get all the roles and permissions for displaying.
		$this->allRoles = Role::GetAll($dbc, true);
		$this->allPermissions = Permission::GetAll($dbc);

		foreach ($this->allRoles as $role)
		{
			$role->GetPermissions($dbc);
		}
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1><?=$this->title;?></h1>
		<div class="pure-menu pure-menu-horizontal">
			<ul class="pure-menu">
				<li class="pure-menu-item"><a href="<?=ROOT."/edit";?>" class="pure-menu-link">&#10094; Edit</a></li>
			</ul>
		</div>
	</div>

	<div class="content">
		<form method="POST" class="pure-form">
			<?php $this->Matrix(); ?>
			<input type="submit" class="pure-button pure-button-primary" name="update-matrix" value="Apply">
		</form>
	</div>
</div>

<?php }

function Matrix()
{ ?>
<table class="pure-table pure-table-borderd permission-matrix">
	<thead>
		<tr>
			<th><!-- Role - Permission --></th>
			<?php
			foreach ($this->allRoles as $role)
			{
				echo "<th>$role->slug</th>";
				echo "<input type=\"hidden\" name=\"roles[$role->slug][]\" />";
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($this->allPermissions as $permission)
		{
			echo "<tr>";
			echo "<th>$permission->slug</th>";
			foreach ($this->allRoles as $role)
			{
				$checked = "";
				$disabled = "";

				if($role->HasPermission($permission))
					$checked = "checked";

				// if($role->slug == "root")
				// $disabled = "disabled";
				?>
				<td><input type="checkbox" value="<?=$permission->slug;?>" name="roles[<?=$role->slug;?>][]" <?=$checked;?> <?=$disabled;?>></td>
				<?php
			}
			echo "</tr>";
		}
		?>
	</tbody>
</table>
<?php }

function EchoValue($value)
{
	if($this->user != null)
		echo "value=\"$value\"";
}

}
?>
