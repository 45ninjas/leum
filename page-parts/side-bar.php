<!-- Menu toggle -->
<a href="#menu" id="menuLink" class="menu-link">
    <!-- Hamburger icon -->
    <span></span>
</a>

<div id="menu">
    <div class="pure-menu">
        <a class="pure-menu-heading" href="<?php echo ROOT ?>"><?php echo APP_TITLE ?></a>
        <ul class="pure-menu-list">
<!--         	<li class="pure-menu-item menu-item-divided pure-menu-selected">
            </li> -->
            <?php
            if(isset($_SESSION["user_id"])):
            ?><li class="pure-menu-item"><a href="<?php echo ROOT; ?>/profile" class="pure-menu-link"><?=$_SESSION["username"];?></a></li>
            <li class="pure-menu-item"><a href="<?php echo ROOT; ?>?logout" class="pure-menu-link">Logout</a></li><?php
            else:
            ?><li class="pure-menu-item"><a href="<?php echo ROOT; ?>/login" class="pure-menu-link">Login</a></li><?php endif; ?>
            <li class="pure-menu-item"><a href="<?php echo ROOT; ?>/browse" class="pure-menu-link">Browse</a></li>
            <li class="pure-menu-item"><a href="<?php echo ROOT; ?>/edit" class="pure-menu-link">Edit</a></li>
            <li class="pure-menu-item"><a href="<?php echo ROOT; ?>/preferences" class="pure-menu-link">Preferences</a></li>
        </ul>
    </div>
</div>