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
			<h2 class="title"><i class="fa fa-users left"></i>Roles</h2>
			<?php $this->Roles(); ?>
			<a class="pure-button pure-button-primary button-compact" href="<?=ROOT?>/edit/permissions/roles/new">
				New <i class="fa fa-plus"></i>
			</a>
			<h2 class="title"><i class="fa fa-key left"></i>Permission Matrix</h2>
			<?php $this->Matrix(); ?>
			<input type="submit" class="pure-button pure-button-primary" name="update-matrix" value="Apply">
		</form>
	</div>
</div>

<?php }

function Roles()
{ ?>
	<table class="pure-table pure-table-horizontal">
		<thead>
			<tr>
				<!-- <th>ID</th> -->
				<th>Role</th>
				<th>Description</th>
				<th>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($this->allRoles as $role)
			{
				$editLink = ROOT . "/edit/permissions/roles/$role->slug";
				echo "<tr>";
				echo "<!-- <td>$role->role_id</td> -->";
				echo "<th>$role->slug</th>";
				echo "<td>$role->description</td>";
				echo "<td><a class=\"pure-button button-compact\" href=\"$editLink\"><i class=\"fa fa-edit\"></i></a></td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
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
			echo "<th>$permission->slug<span class=\"tool-tip\">$permission->description</span></th>";
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
